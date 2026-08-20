<?php
require_once __DIR__ . '/../classes/ProductUrlBuilder.php';

$failures = 0;

function assertEqual($actual, $expected, string $label)
{
    global $failures;
    if ($actual === $expected) {
        echo "PASS: $label\n";
    } else {
        $failures++;
        echo "FAIL: $label — expected " . var_export($expected, true) . ", got " . var_export($actual, true) . "\n";
    }
}

// Real fixture verified live on 2026-08-20 via curl against tienda.vinofilos.es.
$productWithEan = [
    'id' => 4668,
    'link_rewrite' => '46-cepas-2023-75cl',
    'ean13' => '9732925437630',
    'cat_rewrite' => 'tintos',
];
assertEqual(
    WineRedirectsProductUrlBuilder::buildPath($productWithEan),
    '/tintos/4668-46-cepas-2023-75cl-9732925437630.html',
    'buildPath appends the ean13 with a leading dash'
);
assertEqual(
    WineRedirectsProductUrlBuilder::build($productWithEan, 'https://tienda.vinofilos.es'),
    'https://tienda.vinofilos.es/tintos/4668-46-cepas-2023-75cl-9732925437630.html',
    'build prefixes the base URL'
);

// Real fixture: id 5542 "Cantagrillos - 75cl" has an empty ean13 in the DB.
$productNoEan = [
    'id' => 5542,
    'link_rewrite' => 'cantagrillos-75cl',
    'ean13' => '',
    'cat_rewrite' => 'espumosos',
];
assertEqual(
    WineRedirectsProductUrlBuilder::buildPath($productNoEan),
    '/espumosos/5542-cantagrillos-75cl.html',
    'buildPath omits the trailing dash-ean segment when ean13 is empty'
);

echo $failures === 0 ? "\nALL PASS\n" : "\n$failures FAILURE(S)\n";
exit($failures === 0 ? 0 : 1);
