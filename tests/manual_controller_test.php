<?php
// Functional check: builds the same product list + pairing the controller will use,
// and prints counts so we can sanity-check them against the 2026-08-20/21 manual
// analysis (which found 706 total Redirect 301 lines already applied — after that
// work, this list should be small/empty for real vintage swaps that predate it).
require_once '/var/www/vhosts/vinofilos.es/tienda.vinofilos.es/config/config.inc.php';
require_once _PS_MODULE_DIR_ . 'wineredirects/classes/VintageMatcher.php';
require_once _PS_MODULE_DIR_ . 'wineredirects/classes/HtaccessParser.php';
require_once _PS_MODULE_DIR_ . 'wineredirects/classes/ProductUrlBuilder.php';

$idLang = (int) Context::getContext()->language->id;
$idShop = (int) Context::getContext()->shop->id;

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
$products = array_map(function ($row) {
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

echo 'Total products: ' . count($products) . "\n";

$pairs = WineRedirectsVintageMatcher::findPairs($products);
echo 'Pairs found (before excluding already-redirected): ' . count($pairs) . "\n";

$htaccessContent = (string) @file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
$redirectedIds = WineRedirectsHtaccessParser::extractRedirectedProductIds($htaccessContent);
echo 'Already-redirected ids found in .htaccess: ' . count($redirectedIds) . "\n";

$confirmed = 0;
$review = 0;
foreach ($pairs as $pair) {
    if (in_array($pair['old']['id'], $redirectedIds, true)) {
        continue;
    }
    if ($pair['needsReview']) {
        $review++;
    } else {
        $confirmed++;
    }
}

echo "Confirmed suggestions (not yet in .htaccess): $confirmed\n";
echo "Needs-review suggestions (not yet in .htaccess): $review\n";
