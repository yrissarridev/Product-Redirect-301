<?php
// Functional round-trip check for WineRedirects::install()/uninstall().
// Run from anywhere: php manual_install_test.php
//
// WARNING: this always ends with the module UNINSTALLED (its last check
// calls uninstall()), and if the module is already really installed, the
// install() call in this script fails (PrestaShop blocks double-install)
// — but the trailing uninstall() call still runs and *does* remove the
// real installation. Don't run this against a live-installed module
// expecting it to be a no-op: if wineredirects is currently installed,
// reinstall it afterwards (see the one-liner in the plan's Task 7 Step 2).
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

$idCatalogTab = (int) Tab::getIdFromClassName('AdminCatalog');
$tab = new Tab($idTabAfterInstall);
check($idCatalogTab > 0, 'this store has a Tab with class_name AdminCatalog to nest under');
check((int) $tab->id_parent === $idCatalogTab, 'the new Tab is nested under the Catálogo menu, not left at the top level');

check($module->uninstall(), 'uninstall() returns true');
$idTabAfterUninstall = (int) Tab::getIdFromClassName('AdminWineRedirects');
check($idTabAfterUninstall === 0, 'the Tab is gone after uninstall()');

echo $failures === 0 ? "\nALL PASS\n" : "\n$failures FAILURE(S)\n";
exit($failures === 0 ? 0 : 1);
