<?php
// Functional round-trip check for WineRedirects::install()/uninstall().
// Run from anywhere: php manual_install_test.php
require_once '/var/www/vhosts/vinofilos.es/tienda.vinofilos.es/config/config.inc.php';
require_once _PS_MODULE_DIR_ . 'wineredirects/wineredirects.php';

$failures = 0;

function check(bool $condition, string $label)
{
    global $failures;
    if ($condition) {
        echo "PASS: $label\n";
    } else {
        $failures++;
        echo "FAIL: $label\n";
    }
}

// Clean slate: if a previous failed run left the tab behind, remove it first.
$existingIdTab = (int) Tab::getIdFromClassName('AdminWineRedirects');
if ($existingIdTab) {
    (new Tab($existingIdTab))->delete();
}

$module = new WineRedirects();

check($module->install(), 'install() returns true');
$idTabAfterInstall = (int) Tab::getIdFromClassName('AdminWineRedirects');
check($idTabAfterInstall > 0, 'a Tab with class_name AdminWineRedirects exists after install()');

check($module->uninstall(), 'uninstall() returns true');
$idTabAfterUninstall = (int) Tab::getIdFromClassName('AdminWineRedirects');
check($idTabAfterUninstall === 0, 'the Tab is gone after uninstall()');

echo $failures === 0 ? "\nALL PASS\n" : "\n$failures FAILURE(S)\n";
exit($failures === 0 ? 0 : 1);
