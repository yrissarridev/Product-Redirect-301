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
}
