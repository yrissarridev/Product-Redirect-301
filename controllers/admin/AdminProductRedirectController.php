<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'productredirect/classes/VintageMatcher.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/HtaccessParser.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/HtaccessWriter.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/ProductUrlBuilder.php';

class AdminProductRedirectController extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAplicarHtaccess')) {
            $this->applyToHtaccess();
        }

        parent::postProcess();
    }

    private function applyToHtaccess()
    {
        $linesText = Tools::getValue('lines_text', '');
        $lines = preg_split('/\r\n|\r|\n/', (string) $linesText);

        try {
            $added = ProductRedirectHtaccessWriter::appendLines(_PS_ROOT_DIR_ . '/.htaccess', $lines);
            if ($added > 0) {
                $this->confirmations[] = $added . ' redirect(s) 301 añadidos al .htaccess correctamente.';
            } else {
                $this->errors[] = 'No había ninguna línea nueva que añadir (o no estaban marcadas).';
            }
        } catch (Exception $e) {
            $this->errors[] = 'No se pudo escribir en el .htaccess: ' . $e->getMessage();
        }
    }

    // AdminController::createTemplate() always resolves $tpl_name relative to
    // the admin theme's own template directory (getTemplateDir(0)), so a
    // module's own .tpl file has to be resolved explicitly here instead —
    // the front-office 'module:' resource prefix does not apply to admin
    // controllers.
    public function createTemplate($tpl_name)
    {
        return $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . 'productredirect/views/templates/admin/' . $tpl_name,
            $this->context->smarty
        );
    }

    public function initContent()
    {
        parent::initContent();

        $products = $this->fetchProducts();
        $productsById = [];
        foreach ($products as $product) {
            $productsById[$product['id']] = $product;
        }

        $htaccessContent = (string) @file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        $redirectEntries = ProductRedirectHtaccessParser::extractRedirectEntries($htaccessContent);
        $redirectedIds = array_column($redirectEntries, 'id');

        $pairs = ProductRedirectVintageMatcher::findPairs($products);

        $confirmed = [];
        $review = [];
        foreach ($pairs as $pair) {
            if (in_array($pair['old']['id'], $redirectedIds, true)) {
                continue;
            }
            $row = $this->buildRow($pair);
            if ($pair['needsReview']) {
                $review[] = $row;
            } else {
                $confirmed[] = $row;
            }
        }

        // A redirect goes stale the moment its source product comes back —
        // active again, with stock — since the .htaccess rule is static and
        // has no way to know that on its own; it would keep bouncing real
        // customers away from a product that's sellable again.
        $obsolete = [];
        foreach ($redirectEntries as $entry) {
            $product = $productsById[$entry['id']] ?? null;
            if ($product !== null && $product['active'] && $product['quantity'] > 0) {
                $obsolete[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'qty' => $product['quantity'],
                    'line' => $entry['line'],
                ];
            }
        }

        $this->context->smarty->assign([
            'confirmed' => $confirmed,
            'review' => $review,
            'obsolete' => $obsolete,
            'currentIndex' => self::$currentIndex,
            'token' => $this->token,
        ]);
        $this->setTemplate('list.tpl');
    }

    private function fetchProducts(): array
    {
        $idLang = (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;

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

    private function buildRow(array $pair): array
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
}
