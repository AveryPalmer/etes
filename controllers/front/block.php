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
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2022 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;

class Ets_geolocationBlockModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->template = ($this->module->is17 ? 'module:ets_geolocation/views/templates/front/' : '').'block.tpl';
    }
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(array(
            'msg' => Configuration::get('ETS_GEO_BLOG_MSG', $this->context->language->id)
        ));
        $content = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/front/block.tpl');
        exit($content);
    }
}