<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductRedirect extends Module
{
    public function __construct()
    {
        $this->name = 'productredirect';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Yrissarri';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('Product Redirects 301');
        $this->description = $this->l('Sugerencias de redirect 301 de productos descatalogados a la versión activa más reciente del mismo producto.');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // PrestaShop's default class_name for the "Catalog" parent menu is
        // 'AdminParentCatalog', but some stores (this one included) have it
        // registered as plain 'AdminCatalog' instead — try both, and fall
        // back to a top-level tab (id_parent = 0) rather than failing the
        // whole install if neither is found.
        $idParent = (int) Tab::getIdFromClassName('AdminParentCatalog');
        if (!$idParent) {
            $idParent = (int) Tab::getIdFromClassName('AdminCatalog');
        }

        $tab = new Tab();
        $tab->class_name = 'AdminProductRedirect';
        $tab->module = $this->name;
        $tab->id_parent = $idParent;
        $tab->icon = 'sync_problem';
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int) $lang['id_lang']] = 'Product Redirects 301';
        }

        return $tab->add();
    }

    public function uninstall()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminProductRedirect');
        if ($idTab) {
            $tab = new Tab($idTab);
            $tab->delete();
        }

        Configuration::deleteByName('PRODUCTREDIRECT_CRON_TOKEN');
        Configuration::deleteByName('PRODUCTREDIRECT_CRON_LAST_RUN');
        Configuration::deleteByName('PRODUCTREDIRECT_CRON_LAST_ADDED');
        Configuration::deleteByName('PRODUCTREDIRECT_CRON_LAST_DEACTIVATED');
        Configuration::deleteByName('PRODUCTREDIRECT_CRON_LAST_STATUS');

        return parent::uninstall();
    }
}
