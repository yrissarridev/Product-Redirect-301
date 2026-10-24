<?php
require_once __DIR__ . '/../classes/HtaccessParser.php';
require_once __DIR__ . '/../classes/HtaccessWriter.php';

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

$fixture = <<<'HTACCESS'
RewriteEngine on

#301 modulo
Redirect 301 /blancos/4886-rosalia-2023-75cl.html https://example.com/blancos/10008-rosalia-2024-75cl.html
Redirect 301 /tintos/72-trail-2020.html https://example.com/tintos/2619-trail-2021.html
Redirect 301 /es/content/17-best-sellers https://example.com/content/17-best-sellers
HTACCESS;

$dir = sys_get_temp_dir() . '/prw-' . getmypid();
@mkdir($dir);
$path = $dir . '/.htaccess';
file_put_contents($path, $fixture);

// removeLines() — drops only the exact obsolete product redirect lines it is given.
$removed = ProductRedirectHtaccessWriter::removeLines($path, [
    'Redirect 301 /blancos/4886-rosalia-2023-75cl.html https://example.com/blancos/10008-rosalia-2024-75cl.html',
    'Redirect 301 /es/content/17-best-sellers https://example.com/content/17-best-sellers',
    'Redirect 301 /tintos/999-no-existe.html https://example.com/tintos/1-x.html',
]);
$after = file_get_contents($path);

assertEqual($removed, [4886], 'removes only the product redirect present in the file, returns its id');
assertEqual(strpos($after, '4886-rosalia') === false, true, 'the obsolete line is gone');
assertEqual(strpos($after, '72-trail-2020') !== false, true, 'other product redirects are kept');
assertEqual(strpos($after, '17-best-sellers') !== false, true, 'non-product lines are never removed, even if passed in');
assertEqual(count(glob($path . '.bak-productredirect-*')), 1, 'writes a backup before changing the file');

$sameContent = file_get_contents($path);
assertEqual(ProductRedirectHtaccessWriter::removeLines($path, ['Redirect 301 /blancos/4886-rosalia-2023-75cl.html https://example.com/blancos/10008-rosalia-2024-75cl.html']), [], 'returns nothing when the line is already gone');
assertEqual(file_get_contents($path), $sameContent, 'does not touch the file when there is nothing to remove');

array_map('unlink', glob($dir . '/.htaccess*'));
@rmdir($dir);

echo $failures === 0 ? "\nALL PASS\n" : "\n$failures FAILURE(S)\n";
exit($failures === 0 ? 0 : 1);
