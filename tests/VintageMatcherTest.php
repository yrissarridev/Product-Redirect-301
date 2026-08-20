<?php
require_once __DIR__ . '/../classes/VintageMatcher.php';

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

// baseKey()
assertEqual(
    WineRedirectsVintageMatcher::baseKey('46 Cepas 2021 - 75cl'),
    '46 cepas - 75cl',
    'baseKey strips a 4-digit year and lowercases'
);
assertEqual(
    WineRedirectsVintageMatcher::baseKey('Dulce María Blanco - 75cl'),
    'dulce maría blanco - 75cl',
    'baseKey leaves names with no year untouched (besides lowercasing)'
);
assertEqual(
    WineRedirectsVintageMatcher::baseKey('Abadía Retuerta Selección Especial 2019 - Magnum 1,5L'),
    'abadía retuerta selección especial - magnum 1,5l',
    'baseKey collapses the double space left after removing the year'
);

// extractYear()
assertEqual(WineRedirectsVintageMatcher::extractYear('46 Cepas 2021 - 75cl'), 2021, 'extractYear finds a 2021 year');
assertEqual(WineRedirectsVintageMatcher::extractYear('Dulce María Blanco - 75cl'), null, 'extractYear returns null when no year present');
assertEqual(WineRedirectsVintageMatcher::extractYear('Akoibon 2021 - 75cl'), 2021, 'extractYear finds the first year token');

// findPairs()
$products = [
    ['id' => 121, 'name' => '46 Cepas 2021 - 75cl', 'active' => false, 'quantity' => 0],
    ['id' => 4668, 'name' => '46 Cepas 2023 - 75cl', 'active' => true, 'quantity' => 72],
    ['id' => 1260, 'name' => 'Akoibon 2020 - 75cl', 'active' => true, 'quantity' => 3],
    ['id' => 4076, 'name' => 'Akoibon 2021 - 75cl', 'active' => true, 'quantity' => 0],
    ['id' => 46, 'name' => 'Dulce María Blanco - 75cl', 'active' => true, 'quantity' => 202],
];
$pairs = WineRedirectsVintageMatcher::findPairs($products);

assertEqual(count($pairs), 1, 'findPairs returns exactly one pair for the fixture set');
if (count($pairs) === 1) {
    assertEqual($pairs[0]['old']['id'], 121, 'the pair\'s old product is id 121 (46 Cepas 2021)');
    assertEqual($pairs[0]['new']['id'], 4668, 'the pair\'s new product is id 4668 (46 Cepas 2023)');
    assertEqual($pairs[0]['needsReview'], false, 'a pair where both names have years and new > old is confirmed');
}

echo $failures === 0 ? "\nALL PASS\n" : "\n$failures FAILURE(S)\n";
exit($failures === 0 ? 0 : 1);
