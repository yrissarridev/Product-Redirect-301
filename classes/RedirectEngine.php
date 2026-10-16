<?php
// Lógica compartida entre el botón manual (AdminProductRedirectController) y
// el cron automático (cron.php) — así los dos caminos aplican exactamente
// las mismas reglas y nunca pueden divergir.
class ProductRedirectEngine
{
    public static function fetchProducts(): array
    {
        $context = Context::getContext();
        $idLang = (int) $context->language->id;
        $idShop = (int) $context->shop->id;

        $sql = 'SELECT p.id_product AS id, pl.name AS name, p.active AS active,
                       IFNULL(sa.quantity, 0) AS quantity, pl.link_rewrite AS link_rewrite,
                       p.ean13 AS ean13, cl.link_rewrite AS cat_rewrite
                FROM ' . _DB_PREFIX_ . 'product p
                INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl
                    ON pl.id_product = p.id_product AND pl.id_lang = ' . $idLang . ' AND pl.id_shop = ' . $idShop . '
                LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa
                    ON sa.id_product = p.id_product AND sa.id_product_attribute = 0 AND sa.id_shop = ' . $idShop . '
                LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl
                    ON cl.id_category = p.id_category_default AND cl.id_lang = ' . $idLang . ' AND cl.id_shop = ' . $idShop;

        $rows = Db::getInstance()->executeS($sql);

        return array_map(function ($row) {
            return [
                'id' => (int) $row['id'],
                'name' => $row['name'],
                'active' => (bool) $row['active'],
                'quantity' => (int) $row['quantity'],
                'link_rewrite' => $row['link_rewrite'],
                'ean13' => $row['ean13'],
                'cat_rewrite' => $row['cat_rewrite'],
            ];
        }, $rows);
    }

    public static function buildRow(array $pair): array
    {
        // Tools::getShopDomainSsl($http=true, ...) already prepends the protocol
        // (https:// when PS_SSL_ENABLED, per PrestaShop core) — do not prefix it again.
        $baseUrl = Tools::getShopDomainSsl(true, false);

        return [
            'old_id' => $pair['old']['id'],
            'old_name' => $pair['old']['name'],
            'old_active' => $pair['old']['active'],
            'old_qty' => $pair['old']['quantity'],
            'old_url' => ProductRedirectUrlBuilder::build($pair['old'], $baseUrl),
            'new_id' => $pair['new']['id'],
            'new_name' => $pair['new']['name'],
            'new_qty' => $pair['new']['quantity'],
            'new_url' => ProductRedirectUrlBuilder::build($pair['new'], $baseUrl),
            'redirect_line' => 'Redirect 301 '
                . ProductRedirectUrlBuilder::buildPath($pair['old'])
                . ' '
                . ProductRedirectUrlBuilder::build($pair['new'], $baseUrl),
        ];
    }

    // El redirect ya se encarga de mandar al cliente al producto nuevo, pero
    // el antiguo seguía pudiendo salir activo en catálogo/buscador/sitemap
    // mientras tanto — lo desactivamos en el mismo paso en que se aplica el
    // redirect, para que no se quede a medias.
    public static function deactivateProducts(array $productIds): int
    {
        $count = 0;
        foreach ($productIds as $idProduct) {
            $product = new Product((int) $idProduct);
            if (!Validate::isLoadedObject($product) || !$product->active) {
                continue;
            }
            $product->active = 0;
            try {
                if ($product->update()) {
                    $count++;
                }
            } catch (\Throwable $e) {
                // Un hook de otro módulo (ej. iqitsizecharts, tallas de ropa)
                // puede lanzar un error DESPUÉS de que Product::update() ya
                // haya escrito en las dos tablas — verificado repetidas veces
                // en BD real. No dejamos que ese fallo ajeno tire abajo el
                // resto del lote.
                $count++;
            }
        }

        return $count;
    }

    // Versión sin hooks de deactivateProducts(), solo para el cron automático
    // (nadie vigilando). Product::update() dispara actionObjectProductUpdateAfter
    // en TODOS los módulos instalados, y al menos uno de ellos (iqitsizecharts,
    // tallas de ropa, nada que ver con esto) revienta con un error no capturable
    // por try/catch cuando se ejecuta fuera del contexto normal del admin —
    // verificado en vivo varias veces que el UPDATE real en BD siempre se
    // completa igualmente, el problema es solo que tira abajo la respuesta
    // HTTP del cron después. Escribir directo evita el hook por completo.
    // El botón manual del admin sigue usando deactivateProducts() (contexto de
    // admin real, ya probado sin problemas) para no perder esos side-effects
    // cuando hay un humano vigilando.
    public static function deactivateProductsSql(array $productIds): int
    {
        $count = 0;
        foreach ($productIds as $idProduct) {
            $idProduct = (int) $idProduct;
            $isActive = (bool) Db::getInstance()->getValue(
                'SELECT active FROM ' . _DB_PREFIX_ . 'product WHERE id_product = ' . $idProduct
            );
            if (!$isActive) {
                continue;
            }
            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product SET active = 0 WHERE id_product = ' . $idProduct);
            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product_shop SET active = 0 WHERE id_product = ' . $idProduct);
            $count++;
        }

        return $count;
    }

    /**
     * Pares "Confirmados" (año nuevo > año viejo, sin ambigüedad) que todavía
     * no están redirigidos — las únicas que el cron tiene permiso de tocar.
     * Las "A revisar" (sin año detectable) se quedan siempre para el botón
     * manual, nunca las aplica el cron.
     */
    public static function findConfirmedLines(): array
    {
        $products = self::fetchProducts();

        $htaccessContent = (string) @file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        $redirectEntries = ProductRedirectHtaccessParser::extractRedirectEntries($htaccessContent);
        $redirectedIds = array_column($redirectEntries, 'id');

        $pairs = ProductRedirectVintageMatcher::findPairs($products);

        $lines = [];
        foreach ($pairs as $pair) {
            if ($pair['needsReview']) {
                continue;
            }
            if (in_array($pair['old']['id'], $redirectedIds, true)) {
                continue;
            }
            $row = self::buildRow($pair);
            $lines[] = $row['redirect_line'];
        }

        return $lines;
    }

    /**
     * Ejecuta el ciclo completo automático: busca los pares confirmados
     * pendientes, los escribe en el .htaccess real y desactiva los productos
     * antiguos correspondientes. Pensado para ser llamado tanto desde el
     * cron (cron.php) como, si hiciera falta, desde el propio admin.
     *
     * @return array{added: int, deactivated: int}
     */
    public static function applyAutomatic(): array
    {
        $lines = self::findConfirmedLines();

        if (empty($lines)) {
            return ['added' => 0, 'deactivated' => 0];
        }

        $addedIds = ProductRedirectHtaccessWriter::appendLines(_PS_ROOT_DIR_ . '/.htaccess', $lines);
        $deactivated = self::deactivateProductsSql($addedIds);

        return ['added' => count($addedIds), 'deactivated' => $deactivated];
    }
}
