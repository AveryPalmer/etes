<?php
/**
 * 2007-2022 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2022 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;

class AdminGeoLocationMessagesController extends ModuleAdminController
{
    public $_module;
    public $baseAdminPath;
    public function __construct()
    {
        parent::__construct();
        $this->context= Context::getContext();
        $this->bootstrap = true;
        $this->_module = new Ets_geolocation();
        $this->baseAdminPath = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->_module->name . '&tab_module=' . $this->_module->tab . '&module_name=' . $this->_module->name;
        $url_link = $this->baseAdminPath . '&control=messages&list=true';
        if (!$this->_module->is17){
            $url_link =$this->_module->getAdminBaseLink(). basename(_PS_ADMIN_DIR_) . '/'.$url_link;
        }
        Tools::redirect($url_link);
    }
}