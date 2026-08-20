<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class WineRedirects extends Module
{
    public function __construct()
    {
        $this->name = 'wineredirects';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Vinofilos';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Redirects 301 (añadas)');
        $this->description = $this->l('Sugerencias de redirect 301 para vinos descatalogados con añada más reciente activa.');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // This store's Catálogo menu uses class_name 'AdminCatalog' (id_tab 9),
        // not PrestaShop's default 'AdminParentCatalog' — confirmed by querying
        // ps_tab directly (AdminProducts has id_parent=9, and that row's own
        // class_name is 'AdminCatalog').
        $idParent = (int) Tab::getIdFromClassName('AdminCatalog');

        $tab = new Tab();
        $tab->class_name = 'AdminWineRedirects';
        $tab->module = $this->name;
        $tab->id_parent = $idParent;
        $tab->icon = 'sync_problem';
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int) $lang['id_lang']] = 'Redirects 301 (añadas)';
        }

        return $tab->add();
    }

    public function uninstall()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminWineRedirects');
        if ($idTab) {
            $tab = new Tab($idTab);
            $tab->delete();
        }

        return parent::uninstall();
    }
}
