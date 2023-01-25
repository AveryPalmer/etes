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

class Ets_geolocationProcessModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->template = $this->module->is17 ? 'module:ets_geolocation/views/templates/front/popup.tpl' : 'popup16.tpl';
    }

    public function initContent()
    {
        parent::initContent();
        //checking geo rules is block.
        if ((int)Configuration::get('ETS_GEO_IGNORE_BOTS') && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/BotLink|ahoy|AlkalineBOT|anthill|appie|arale|araneo|AraybOt|ariadne|arks|ATN_Worldwide|Atomz|bbot|Bjaaland|Ukonline|borg\-bot\/0\.9|boxseabot|bspider|calif|christcrawler|CMC\/0\.01|combine|confuzzledbot|CoolBot|cosmos|Internet Cruiser Robot|cusco|cyberspyder|cydralspider|desertrealm, desert realm|digger|DIIbot|grabber|downloadexpress|DragonBot|dwcp|ecollector|ebiness|elfinbot|esculapio|esther|fastcrawler|FDSE|FELIX IDE|ESI|fido|H�m�h�kki|KIT\-Fireball|fouineur|Freecrawl|gammaSpider|gazz|gcreep|golem|googlebot|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|iajabot|INGRID\/0\.1|Informant|InfoSpiders|inspectorwww|irobot|Iron33|JBot|jcrawler|Teoma|Jeeves|jobo|image\.kapsi\.net|KDD\-Explorer|ko_yappo_robot|label\-grabber|larbin|legs|Linkidator|linkwalker|Lockon|logo_gif_crawler|marvin|mattie|mediafox|MerzScope|NEC\-MeshExplorer|MindCrawler|udmsearch|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|sharp\-info\-agent|WebMechanic|NetScoop|newscan\-online|ObjectsSearch|Occam|Orbsearch\/1\.0|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|Getterrobo\-Plus|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Search\-AU|searchprocess|Senrigan|Shagseeker|sift|SimBot|Site Valet|skymob|SLCrawler\/2\.0|slurp|ESI|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|nil|suke|http:\/\/www\.sygol\.com|tach_bw|TechBOT|templeton|titin|topiclink|UdmSearch|urlck|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|crawlpaper|wapspider|WebBandit\/1\.0|webcatcher|T\-H\-U\-N\-D\-E\-R\-S\-T\-O\-N\-E|WebMoose|webquest|webreaper|webs|webspider|WebWalker|wget|winona|whowhere|wlm|WOLP|WWWC|none|XGET|Nederland\.zoek|AISearchBot|woriobot|NetSeer|Nutch|YandexBot/i', $_SERVER['HTTP_USER_AGENT'])) {
            exit();
        }
        $msg = $this->module->l('Geolocation is disable or unavailable', 'process');
        if (!(int)Configuration::get('PS_GEOLOCATION_ENABLED') || !$this->module->isGeoLiteCityAvailable()) {
            die($msg);
        }
        if ($this->module->checkThisCountryDisable() && $geo_rule = Geo_rules::getRulesByIdCountry()) {
            if (isset($geo_rule['block_user']) && $geo_rule['block_user']) {
                die(json_encode(array(
                    'link_block' => $this->context->link->getModuleLink($this->module->name, 'block', array(), Tools::usingSecureMode())
                )));
            } elseif(isset($geo_rule['url_redirect']) && $geo_rule['url_redirect']){
                die(json_encode(array(
                                          'link_block' => $geo_rule['url_redirect']
                                      )));
            } elseif (isset($geo_rule['disable_geo']) && $geo_rule['disable_geo']) {
                die($msg);
            }
        }
        $this->setTemplate($this->template);
        if (Tools::getValue('geo_auto_processing') && !$this->context->cookie->ets_geocountryloaded
            && (!(int)Configuration::get('ETS_GEO_ON_HOME_ONLY') || $this->context->cookie->page_controller == 'index')
        ) {
            //set shipping tax.
            $this->module->detectedAddress($this->context->cookie->iso_code_country);
            //set currency and language
            $id_currency = $this->context->currency->id;
            $id_lang = $this->context->language->id;

            if (($auto_currency = Configuration::get('ETS_GEO_AUTO_CURRENCY')) && Currency::isMultiCurrencyActivated()) {
                if (isset($geo_rule['currency_to_set']) && (int)$geo_rule['currency_to_set']) {
                    $id_currency = (int)$geo_rule['currency_to_set'];
                } elseif ($auto_currency && $this->context->country->id_currency) {
                    $id_currency = (int)$this->context->country->id_currency;
                } elseif ($auto_currency && ($currency_id = (int)$this->getGeoIDCurrency())) {
                    $id_currency = $currency_id;
                }
            }
            if (($auto_lang = Configuration::get('ETS_GEO_AUTO_LANG')) && Language::isMultiLanguageActivated()) {
                if (isset($geo_rule['lang_to_set']) && (int)$geo_rule['lang_to_set']) {
                    $id_lang = (int)$geo_rule['lang_to_set'];
                } elseif ($auto_lang && ($id_language = $this->module->getGeoIdLang())) {
                    $id_lang = (int)$id_language;
                }
            }
            $is_hide_popup = !(int)Configuration::get('ETS_GEO_HIDE_NOTIFICATION');
            $msg = '';
            $json = array();
            $url_params = array();
            $argument = array(
                'country_name' => is_array($this->context->country->name) ? $this->context->country->name[$this->context->language->id] : $this->context->country->name,
                'geo_hide_notification' => $is_hide_popup,
            );
            if ($id_lang != $this->context->language->id && $id_currency != $this->context->currency->id) {
                if ($is_hide_popup) {
                    $this->context->cookie->id_currency = $id_currency;
                    $this->context->cookie->id_lang = $id_lang;
                    $this->context->cookie->ets_geocountryloaded = 1;
                    $json['link_reload'] = $this->pregLink($this->context->cookie->id_lang);
                } else
                    $url_params = array('lang_id' => $id_lang, 'currency_id' => $id_currency);
                $msg = Configuration::get('ETS_GEO_' . (!$is_hide_popup ? 'CONFIRM' : 'SETTING') . '_MSG', $this->context->language->id);
            } elseif ($id_lang != $this->context->language->id) {
                if ($is_hide_popup) {
                    $this->context->cookie->id_lang = $id_lang;
                    $this->context->cookie->ets_geocountryloaded = 1;
                    $json['link_reload'] = $this->pregLink($this->context->cookie->id_lang);
                } else
                    $url_params = array('lang_id' => $id_lang);
                $msg = Configuration::get('ETS_GEO_' . (!$is_hide_popup ? 'LANGUAGE' : 'SETTING') . '_MSG', $this->context->language->id);
            } elseif ($id_currency != $this->context->currency->id) {
                if ($is_hide_popup) {
                    $this->context->cookie->id_currency = $id_currency;
                    $this->context->cookie->ets_geocountryloaded = 1;
                    $json['link_reload'] = $this->context->language->language_code;
                } else
                    $url_params = array('currency_id' => $id_currency);
                $msg = Configuration::get('ETS_GEO_' . (!$is_hide_popup ? 'CURRENCY' : 'SETTING') . '_MSG', $this->context->language->id);
            } else {
                $this->context->cookie->ets_geocountryloaded = 1;
            }
            if ($this->context->cookie->ets_geocountryloaded) {
                $this->visited();
            }
            if ($url_params) {
                $argument['btn_link'] = $this->context->link->getModuleLink('ets_geolocation', 'process', $url_params, Tools::usingSecureMode());
            }
            $msg = $this->replaceShortCode($msg, $id_lang, $id_currency);
            $argument['msg'] = $msg;
            $json['html'] = $this->displayPopup($argument);
            die(json_encode($json));
        } elseif (Tools::getValue('geo_confirm')) {
            if ($id_lang = Tools::getValue('lang_id')) {
                $this->context->cookie->id_lang = $id_lang;
            }
            if ($id_currency = Tools::getValue('currency_id')) {
                $this->context->cookie->id_currency = $id_currency;
            }
            $this->context->cookie->ets_geocountryloaded = 1;
            $this->visited();
            die(json_encode(array(
                'link_reload' => $this->pregLink($this->context->cookie->id_lang ?: Configuration::get('PS_LANG_DEFAULT'))
            )));
        } elseif (Tools::getValue('geo_country_selected')) {
            $content_choose = Configuration::get('ETS_GEO_CHOOSE_MSG', $this->context->language->id);
            $list_countries = $this->module->ets_getCountriesNotBlock($this->context->language->id, true);
            foreach ($list_countries as &$list_country) {
                $iso_code = $list_country['iso_code'];
                $id_lang_temp = $this->module->getGeoIDLang((int)$list_country['id_country']);
                if ($id_lang_temp) {
                    if (file_exists(_PS_TMP_IMG_DIR_ . 'lang_mini_' . $id_lang_temp . '_' . $this->context->shop->id . '.jpg')) {
                        $list_country['icon_image'] = _PS_TMP_IMG_ . 'lang_mini_' . $id_lang_temp . '_' . $this->context->shop->id . '.jpg';
                    } elseif (file_exists(_PS_IMG_DIR_ . 'l/' . $id_lang_temp . '.jpg')) {
                        $list_country['icon_image'] = _PS_IMG_ . 'l/' . $id_lang_temp . '.jpg';
                    }
                } else {
                    if (file_exists($this->module->getLocalPath() . 'views/img/flag/' . Tools::strtolower($iso_code) . '.jpg')) {
                        $list_country['icon_image'] = $this->module->getPathUri() . 'views/img/flag/' . Tools::strtolower($iso_code) . '.jpg';
                    } else {
                        $list_country['icon_image'] = $this->module->getPathUri() . 'views/img/flag/no_country.jpg';
                    }
                }
            }
            $url_cart_page = $this->context->link->getPageLink('my-account', Configuration::get('PS_SSL_ENABLED'), $this->context->language->id);
            $this->context->smarty->assign(array(
                'content_choose' => $content_choose,
                'list_country' => $list_countries,
                'current_country_id' => (isset($this->context->country->id) && $this->context->country->id && $this->context->country->active ? $this->context->country->id : Configuration::get('PS_COUNTRY_DEFAULT')),
                'url_cart_page' => $url_cart_page
            ));
            $content_pop_choose = $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/front/popup_choose.tpl');
            die(json_encode(array(
                'content_pop_choose' => $content_pop_choose,
            )));
        } elseif (Tools::getValue('geo_loaded')) {
            $this->context->cookie->ets_geocountryloaded = 1;
            $this->visited();
            die('Geo is loaded');
        } elseif (Tools::getValue('geo_selected_country')
            && ($id_country = Tools::getValue('country_id'))
            && ($country = new Country($id_country))
            && $country->id
            && $country->active
            && $country->iso_code != $this->context->country->iso_code) {

            $geo_rule = Geo_rules::getRulesByIdCountry($id_country);
            if ($geo_rule) {
                if (isset($geo_rule['block_user']) && $geo_rule['block_user']) {
                    die(json_encode(array(
                        'link_block' => $this->context->link->getModuleLink($this->module->name, 'block', array(), Tools::usingSecureMode())
                    )));
                } elseif(isset($geo_rule['url_redirect']) && $geo_rule['url_redirect']){
                    die(json_encode(array(
                        'link_block' => $geo_rule['url_redirect']
                    )));
                }
            }
            $this->context->cookie->iso_code_country = $country->iso_code;
            $this->module->detectedAddress($this->context->cookie->iso_code_country);
            $this->context->cookie->id_currency = $country->id_currency ?: (($currency_id = (int)$this->getGeoIDCurrency($id_country)) ? $currency_id : (int)Configuration::get('PS_CURRENCY_DEFAULT'));
            $this->context->cookie->id_lang = ($id_lang = $this->module->getGeoIDLang($id_country)) ? $id_lang : (int)Configuration::get('PS_LANG_DEFAULT');
            if ($geo_rule) {
                if (isset($geo_rule['currency_to_set']) && (int)$geo_rule['currency_to_set']) {
                    $this->context->cookie->id_currency = (int)$geo_rule['currency_to_set'];
                }
                if (isset($geo_rule['lang_to_set']) && (int)$geo_rule['lang_to_set']) {
                    $this->context->cookie->id_lang = (int)$geo_rule['lang_to_set'];
                }

            }
            die(json_encode(array(
                'link_reload' => $this->pregLink($this->context->cookie->id_lang)
            )));
        }
        die('exit');
    }

    public function visited()
    {
        $currentIp = Tools::getRemoteAddr();
        //Add visited.
        if (Geo_visit::checkIpVisitToDay($currentIp)) {
            return;
        }
        $geo_visit_day = new Geo_visit_day();
        $geo_visit_day->deleteOtherDay();
        $geo_visit_day->ip_visit = $currentIp;
        $geo_visit_day->add();;
        if ($check_country = Geo_visit::getCountryVisitToDay($this->context->country->id)) {
            $geo_visit = new Geo_visit($this->context->country->id);
        } else {
            $geo_visit = new Geo_visit();
            $geo_visit->id_country = $this->context->country->id;
            $geo_visit->visit = 1;
        }
        $geo_visit->last_ip = $currentIp;
        $geo_visit->last_visit_time = date('Y-m-d H:i:s');
        if ($check_country) {
            $geo_visit->update_custom();
        } else {
            $geo_visit->add();
        }
    }

    public function pregLink($id_lang)
    {
        $language = new Language($id_lang);
        return $language->iso_code;
    }

    public function getGeoIDCurrency($id_country = 0)
    {
        if (($iso_code_country = $id_country && ($iso_code = Db::getInstance()->getValue('SELECT iso_code FROM ' . _DB_PREFIX_ . 'country WHERE id_country=' . (int)$id_country)) ? $iso_code : $this->context->cookie->iso_code_country) && ($iso_code_currency = Db::getInstance()->getValue('SELECT iso_code_currency FROM ' . _DB_PREFIX_ . 'ets_geo_currency WHERE iso_code_country = "' . pSQL(Tools::strtoupper($iso_code_country)) . '"'))) {
            return (int)Db::getInstance()->getValue('
                SELECT c.id_currency 
                FROM ' . _DB_PREFIX_ . 'currency c ' . Shop::addSqlAssociation('currency', 'c') . ' 
                WHERE iso_code = "' . pSQL(Tools::strtoupper($iso_code_currency)) . '"
            ');
        }
        return 0;
    }



    public function displayPopup($argument = array())
    {
        if (!isset($argument['msg']) || !$argument['msg']) {
            return false;
        }
        $this->context->smarty->assign($argument);
        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/front/popup.tpl');
    }

    public function replaceShortCode($msg, $id_lang, $id_currency)
    {
        if (!$msg) {
            return $msg;
        }
        $language = new Language($id_lang ?: Configuration::get('PS_LANG_DEFAULT'));
        $currency = new Currency($id_currency);
        $msg = str_replace(array('[detected_country]', '[detected_language]', '[detected_currency]', '[current_language]', '[current_currency]'),
            array(is_array($this->context->country->name) ? $this->context->country->name[$language->id] : $this->context->country->name, $language->name, Tools::ucfirst($currency->name) . ' (' . $currency->iso_code . ')', $this->context->language->name, Tools::ucfirst($this->context->currency->name) . ' (' . $this->context->currency->iso_code . ')')
            , $msg);
        return $msg;
    }
}