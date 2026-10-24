<?php
/**
 * Cron automático del módulo Product Redirects 301.
 *
 * Quita del .htaccess los redirects "Obsoletos" (su producto de origen
 * vuelve a estar activo y con stock) y aplica SOLO los pares "Confirmados" (año nuevo > año
 * viejo detectado en el nombre, sin ambigüedad) que aún no estén
 * redirigidos, y desactiva el producto antiguo correspondiente — exactamente
 * la misma lógica que el botón manual "Aplicar al .htaccess", pero nunca
 * toca los pares "A revisar" (esos necesitan ojo humano, se quedan para el
 * botón).
 *
 * Invocación (HTTP, protegido por token):
 *   https://tienda.vinofilos.es/modules/productredirect/cron.php?token=XXXX
 *
 * El token se genera y se consulta/regenera desde la pestaña "Cron" del
 * propio admin del módulo.
 */

header('Content-Type: text/plain; charset=utf-8');

// Al desactivar un producto, Product::update() dispara el hook
// actionObjectProductUpdateAfter de TODOS los módulos instalados — incluido
// iqitsizecharts (tallas de ropa, nada que ver con esto), cuyo
// joinWithProduct() hace (int)Tools::getValue('iqitsizecharts')['chart'];
// si esa clave no viene en la petición, Tools::getValue() devuelve false y
// indexar un bool escala a "Error fatal" en este hosting. Rellenamos la
// clave con un array vacío para que esa línea ajena no reviente este cron.
$_GET['iqitsizecharts'] = ['chart' => -1];
$_POST['iqitsizecharts'] = ['chart' => -1];

chdir(dirname(__FILE__) . '/../..');
require_once 'config/config.inc.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/VintageMatcher.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/HtaccessParser.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/HtaccessWriter.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/ProductUrlBuilder.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/RedirectEngine.php';

$expectedToken = (string) Configuration::get('PRODUCTREDIRECT_CRON_TOKEN');
$givenToken = (string) Tools::getValue('token', '');

if ($expectedToken === '' || !hash_equals($expectedToken, $givenToken)) {
    http_response_code(403);
    echo "Token invalido o no configurado.\n";
    exit;
}

try {
    $result = ProductRedirectEngine::applyAutomatic();

    Configuration::updateValue('PRODUCTREDIRECT_CRON_LAST_RUN', time());
    Configuration::updateValue('PRODUCTREDIRECT_CRON_LAST_ADDED', (int) $result['added']);
    Configuration::updateValue('PRODUCTREDIRECT_CRON_LAST_DEACTIVATED', (int) $result['deactivated']);
    Configuration::updateValue('PRODUCTREDIRECT_CRON_LAST_REMOVED', (int) $result['removed']);
    Configuration::updateValue('PRODUCTREDIRECT_CRON_LAST_STATUS', 'ok');

    echo "OK. Redirects anadidos: {$result['added']}. Productos desactivados: {$result['deactivated']}. Obsoletos eliminados: {$result['removed']}.\n";
} catch (\Throwable $e) {
    Configuration::updateValue('PRODUCTREDIRECT_CRON_LAST_RUN', time());
    Configuration::updateValue('PRODUCTREDIRECT_CRON_LAST_STATUS', 'error: ' . $e->getMessage());

    http_response_code(500);
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
