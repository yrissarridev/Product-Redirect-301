<?php
require_once __DIR__ . '/../classes/HtaccessParser.php';

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
# ~~start~~
RewriteEngine on
RewriteRule ^api$ api/ [L]

Redirect 301 /es/content/17-los-mas-vendidos https://tienda.vinofilos.es/content/17-los-mas-vendidos
Redirect 301 /tintos/72-tarima-hill-2020-8436555720285.html https://tienda.vinofilos.es/tintos/2619-tarima-hill-2021-8436555720285.html
Redirect 301 /rosados/2464-charlatan-2022-8437009260067.html https://tienda.vinofilos.es/rosados/5463-charlatan-2023-75cl-8437009260067.html
Redirect 301 /tintos/tajinaste-vendimia-seleccionada-2022.html https://tienda.vinofilos.es/tintos/4785-tajinaste-vendimia-seleccionada-2023-75cl-8437010950049.html

<IfModule mod_rewrite.c>
RewriteRule ^(.+) $1.br
</IfModule>
HTACCESS;

$ids = WineRedirectsHtaccessParser::extractRedirectedProductIds($fixture);
sort($ids);

assertEqual($ids, [72, 2464], 'extracts only ids from lines with a numeric-id source path, skipping content redirects and slug-only paths');
assertEqual(WineRedirectsHtaccessParser::extractRedirectedProductIds(''), [], 'returns an empty array for empty content');

echo $failures === 0 ? "\nALL PASS\n" : "\n$failures FAILURE(S)\n";
exit($failures === 0 ? 0 : 1);
