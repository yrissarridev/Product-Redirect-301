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

        $idParent = (int) Tab::getIdFromClassName('AdminParentCatalog');

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
