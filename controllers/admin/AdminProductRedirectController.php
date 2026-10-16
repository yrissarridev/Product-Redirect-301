<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'productredirect/classes/VintageMatcher.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/HtaccessParser.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/HtaccessWriter.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/ProductUrlBuilder.php';
require_once _PS_MODULE_DIR_ . 'productredirect/classes/RedirectEngine.php';

class AdminProductRedirectController extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAplicarHtaccess')) {
            $this->applyToHtaccess();
        }

        if (Tools::isSubmit('submitRegenerarCronToken')) {
            $this->regenerateCronToken();
        }

        parent::postProcess();
    }

    private function applyToHtaccess()
    {
        $linesText = Tools::getValue('lines_text', '');
        $lines = preg_split('/\r\n|\r|\n/', (string) $linesText);

        try {
            $addedIds = ProductRedirectHtaccessWriter::appendLines(_PS_ROOT_DIR_ . '/.htaccess', $lines);
            if (!empty($addedIds)) {
                $deactivated = ProductRedirectEngine::deactivateProducts($addedIds);
                $this->confirmations[] = count($addedIds) . ' redirect(s) 301 añadidos al .htaccess correctamente'
                    . ($deactivated > 0 ? ' (' . $deactivated . ' producto(s) antiguo(s) desactivado(s) de paso)' : '') . '.';
            } else {
                $this->errors[] = 'No había ninguna línea nueva que añadir (o no estaban marcadas).';
            }
        } catch (Exception $e) {
            $this->errors[] = 'No se pudo escribir en el .htaccess: ' . $e->getMessage();
        }
    }

    private function regenerateCronToken()
    {
        $newToken = bin2hex(Tools::hash((string) mt_rand() . microtime()));
        $newToken = substr($newToken, 0, 40);
        Configuration::updateValue('PRODUCTREDIRECT_CRON_TOKEN', $newToken);
        $this->confirmations[] = 'Token del cron regenerado. Actualiza la URL en tu crontab.';
    }

    // AdminController::createTemplate() always resolves $tpl_name relative to
    // the admin theme's own template directory (getTemplateDir(0)), so a
    // module's own .tpl file has to be resolved explicitly here instead —
    // the front-office 'module:' resource prefix does not apply to admin
    // controllers.
    public function createTemplate($tpl_name)
    {
        return $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . 'productredirect/views/templates/admin/' . $tpl_name,
            $this->context->smarty
        );
    }

    public function initContent()
    {
        parent::initContent();

        $products = ProductRedirectEngine::fetchProducts();
        $productsById = [];
        foreach ($products as $product) {
            $productsById[$product['id']] = $product;
        }

        $htaccessContent = (string) @file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        $redirectEntries = ProductRedirectHtaccessParser::extractRedirectEntries($htaccessContent);
        $redirectedIds = array_column($redirectEntries, 'id');

        $pairs = ProductRedirectVintageMatcher::findPairs($products);

        $confirmed = [];
        $review = [];
        foreach ($pairs as $pair) {
            if (in_array($pair['old']['id'], $redirectedIds, true)) {
                continue;
            }
            $row = ProductRedirectEngine::buildRow($pair);
            if ($pair['needsReview']) {
                $review[] = $row;
            } else {
                $confirmed[] = $row;
            }
        }

        // A redirect goes stale the moment its source product comes back —
        // active again, with stock — since the .htaccess rule is static and
        // has no way to know that on its own; it would keep bouncing real
        // customers away from a product that's sellable again.
        $obsolete = [];
        foreach ($redirectEntries as $entry) {
            $product = $productsById[$entry['id']] ?? null;
            if ($product !== null && $product['active'] && $product['quantity'] > 0) {
                $obsolete[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'qty' => $product['quantity'],
                    'line' => $entry['line'],
                ];
            }
        }

        $cronToken = (string) Configuration::get('PRODUCTREDIRECT_CRON_TOKEN');
        if ($cronToken === '') {
            $cronToken = bin2hex(Tools::hash((string) mt_rand() . microtime()));
            $cronToken = substr($cronToken, 0, 40);
            Configuration::updateValue('PRODUCTREDIRECT_CRON_TOKEN', $cronToken);
        }
        $cronUrl = Tools::getShopDomainSsl(true, false) . '/modules/productredirect/cron.php?token=' . $cronToken;

        $cronLastRun = (int) Configuration::get('PRODUCTREDIRECT_CRON_LAST_RUN');
        $cronLastStatus = (string) Configuration::get('PRODUCTREDIRECT_CRON_LAST_STATUS');
        $cronLastAdded = (int) Configuration::get('PRODUCTREDIRECT_CRON_LAST_ADDED');
        $cronLastDeactivated = (int) Configuration::get('PRODUCTREDIRECT_CRON_LAST_DEACTIVATED');

        $this->context->smarty->assign([
            'confirmed' => $confirmed,
            'review' => $review,
            'obsolete' => $obsolete,
            'currentIndex' => self::$currentIndex,
            'token' => $this->token,
            'cron_url' => $cronUrl,
            'cron_last_run' => $cronLastRun ? Tools::displayDate(date('Y-m-d H:i:s', $cronLastRun), null, true) : null,
            'cron_last_status' => $cronLastStatus,
            'cron_last_is_error' => (strpos($cronLastStatus, 'error') === 0),
            'cron_last_added' => $cronLastAdded,
            'cron_last_deactivated' => $cronLastDeactivated,
        ]);
        $this->setTemplate('list.tpl');
    }
}
