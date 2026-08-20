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

// PrestaShop's default friendly-URL format is /{category}/{id}-{link_rewrite}-{ean13}.html
// — verified against a live PrestaShop 1.7 store's actual product URLs before writing this.
$productWithEan = [
    'id' => 4668,
    'link_rewrite' => 'trail-runner-2023',
    'ean13' => '9732925437630',
    'cat_rewrite' => 'shoes',
];
assertEqual(
    ProductRedirectUrlBuilder::buildPath($productWithEan),
    '/shoes/4668-trail-runner-2023-9732925437630.html',
    'buildPath appends the ean13 with a leading dash'
);
assertEqual(
    ProductRedirectUrlBuilder::build($productWithEan, 'https://example.com'),
    'https://example.com/shoes/4668-trail-runner-2023-9732925437630.html',
    'build prefixes the base URL'
);

// Some products have no ean13 set in the catalog.
$productNoEan = [
    'id' => 5542,
    'link_rewrite' => 'classic-backpack',
    'ean13' => '',
    'cat_rewrite' => 'bags',
];
assertEqual(
    ProductRedirectUrlBuilder::buildPath($productNoEan),
    '/bags/5542-classic-backpack.html',
    'buildPath omits the trailing dash-ean segment when ean13 is empty'
);

echo $failures === 0 ? "\nALL PASS\n" : "\n$failures FAILURE(S)\n";
exit($failures === 0 ? 0 : 1);
