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
    ProductRedirectVintageMatcher::baseKey('Trail Runner 2021 - Size 42'),
    'trail runner - size 42',
    'baseKey strips a 4-digit year and lowercases'
);
assertEqual(
    ProductRedirectVintageMatcher::baseKey('Classic Backpack - 20L'),
    'classic backpack - 20l',
    'baseKey leaves names with no year untouched (besides lowercasing)'
);
assertEqual(
    ProductRedirectVintageMatcher::baseKey('Alpine Jacket Waterproof 2019 - XL'),
    'alpine jacket waterproof - xl',
    'baseKey collapses the double space left after removing the year'
);

// extractYear()
assertEqual(ProductRedirectVintageMatcher::extractYear('Trail Runner 2021 - Size 42'), 2021, 'extractYear finds a 2021 year');
assertEqual(ProductRedirectVintageMatcher::extractYear('Classic Backpack - 20L'), null, 'extractYear returns null when no year present');
assertEqual(ProductRedirectVintageMatcher::extractYear('City Bike 2021 - Medium'), 2021, 'extractYear finds the first year token');

// findPairs()
$products = [
    ['id' => 121, 'name' => 'Trail Runner 2021 - Size 42', 'active' => false, 'quantity' => 0],
    ['id' => 4668, 'name' => 'Trail Runner 2023 - Size 42', 'active' => true, 'quantity' => 72],
    ['id' => 1260, 'name' => 'City Bike 2020 - Medium', 'active' => true, 'quantity' => 3],
    ['id' => 4076, 'name' => 'City Bike 2021 - Medium', 'active' => true, 'quantity' => 0],
    ['id' => 46, 'name' => 'Classic Backpack - 20L', 'active' => true, 'quantity' => 202],
];
$pairs = ProductRedirectVintageMatcher::findPairs($products);

assertEqual(count($pairs), 1, 'findPairs returns exactly one pair for the fixture set');
if (count($pairs) === 1) {
    assertEqual($pairs[0]['old']['id'], 121, 'the pair\'s old product is id 121 (Trail Runner 2021)');
    assertEqual($pairs[0]['new']['id'], 4668, 'the pair\'s new product is id 4668 (Trail Runner 2023)');
    assertEqual($pairs[0]['needsReview'], false, 'a pair where both names have years and new > old is confirmed');
}

// This fixture encodes three important cases:
// - Trail Runner: a genuine old->new pair, should match.
// - City Bike: id 4076 (2021, disabled) has a *higher* year than id 1260
//   (2020, active) — wrong direction, must be discarded entirely, not
//   paired backwards.
// - Classic Backpack: only one product in its group (no other vintage
//   exists) — must not appear in any pair.

echo $failures === 0 ? "\nALL PASS\n" : "\n$failures FAILURE(S)\n";
exit($failures === 0 ? 0 : 1);
