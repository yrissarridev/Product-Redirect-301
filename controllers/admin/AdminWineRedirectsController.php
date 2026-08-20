<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'wineredirects/classes/VintageMatcher.php';
require_once _PS_MODULE_DIR_ . 'wineredirects/classes/HtaccessParser.php';
require_once _PS_MODULE_DIR_ . 'wineredirects/classes/ProductUrlBuilder.php';

class AdminWineRedirectsController extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $products = $this->fetchProducts();
        $pairs = WineRedirectsVintageMatcher::findPairs($products);
        $redirectedIds = WineRedirectsHtaccessParser::extractRedirectedProductIds(
            (string) @file_get_contents(_PS_ROOT_DIR_ . '/.htaccess')
        );

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

        $this->context->smarty->assign([
            'confirmed' => $confirmed,
            'review' => $review,
        ]);
        $this->setTemplate('module:wineredirects/views/templates/admin/list.tpl');
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
            'old_url' => WineRedirectsProductUrlBuilder::build($pair['old'], $baseUrl),
            'new_id' => $pair['new']['id'],
            'new_name' => $pair['new']['name'],
            'new_qty' => $pair['new']['quantity'],
            'new_url' => WineRedirectsProductUrlBuilder::build($pair['new'], $baseUrl),
            'redirect_line' => 'Redirect 301 '
                . WineRedirectsProductUrlBuilder::buildPath($pair['old'])
                . ' '
                . WineRedirectsProductUrlBuilder::build($pair['new'], $baseUrl),
        ];
    }
}
