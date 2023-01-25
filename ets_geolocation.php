<?php
/**
 * 2007-2022 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 web site only.
 * If you want to use this file on more web sites (or projects), you need to purchase additional licenses.
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
 * @license    Valid for 1 web site (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;
require_once(dirname(__file__) . '/classes/Geo_rules.php');
require_once(dirname(__file__) . '/classes/Geo_country_rule.php');
require_once(dirname(__file__) . '/classes/Geo_visit.php');
require_once(dirname(__file__) . '/classes/Geo_visit_day.php');

class Ets_geolocation extends Module
{
    public $help;
    public $toolbar_btn;
    public $is17;
    public $fields_list = array();
    private $_html = null;
    protected $list_id = null;
    public $_filterHaving;
    public $errorMessage = null;
    public $_filter;
    public $configs_settings = array();
    public $configs_messages = array();
    public $configTabs;
    public $quickTabs;
    public $shortlink;
    protected $ssl_enable;
    protected $urlShopId = null;

    public function __construct()
    {

        $this->name = 'ets_geolocation';
        $this->tab = 'front_office_features';
        $this->version = '1.1.5';
        $this->author = 'ETS-Soft';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '39afc9f65da1a39fc616eab831648228';
        parent::__construct();

        $this->list_id = Geo_rules::$definition['table'];
        $this->displayName = $this->l('Geolocation');
        $this->description = $this->l('Auto language, currency, taxes and shipping cost base on location of customer');
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        $this->is17 = version_compare(_PS_VERSION_, '1.7', '>=');
        $this->shortlink = 'https://mf.short-link.org/';
        if (Tools::getValue('configure') == $this->name && Tools::isSubmit('othermodules')) {
            $this->displayRecommendedModules();
        }
        $this->ssl_enable = Configuration::get('PS_SSL_ENABLED');

    }

    public function generateSQL($file_name)
    {
        $file = fopen(dirname(__FILE__) . '/cache/' . pSQL($file_name) . '.csv', "r");
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ets_geo_currency_code VALUES';
        while (!feof($file)) {
            $currency = fgetcsv($file);
            if ($currency[1] && $currency[3]) {
                $sql .= '("' . pSQL($currency[1]) . '", "' . pSQL($currency[3]) . '"),';
            }
        }
        fclose($file);
        echo trim($sql, ',');
    }

    public function hookActionObjectCartAddBefore()
    {
        $this->detectedAddress($this->context->cookie->iso_code_country);
    }

    public function sprintf($string, $tags)
    {
        if ($tags) {
            $tags = explode(',', $tags);
            $ik = 1;
            foreach ($tags as $tag) {
                $string = preg_replace('/(\[' . $ik . '\](.*?)\[\/' . $ik++ . '\])/i', '<' . $tag . '>$2</' . $tag . '>', $string);
            }
        }
        return Tools::stripslashes($string);
    }

    public function defines($loadCountries = true)
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $this->configs_settings = array(
                'PS_GEOLOCATION_ENABLED' => array(
                    'label' => $this->l('Enable Geolocation'),
                    'type' => 'switch',
                    'default' => (int)Configuration::get('PS_GEOLOCATION_ENABLED'),
                    'jsType' => 'string',
                ),
                'ETS_GEO_AUTO_LANG' => array(
                    'label' => $this->l('Auto set customer language?'),
                    'type' => 'switch',
                    'default' => 1,
                    'jsType' => 'string',
                ),
                'ETS_GEO_AUTO_CURRENCY' => array(
                    'label' => $this->l('Auto set customer currency?'),
                    'type' => 'switch',
                    'default' => 1,
                    'jsType' => 'string',
                ),
                'ETS_GEO_AUTO_TAX_SHIPPING' => array(
                    'label' => $this->l('Auto calculate shipping cost and tax?'),
                    'type' => 'switch',
                    'default' => 1,
                    'jsType' => 'string',
                ),
                'PS_LANG_DEFAULT' => array(
                    'label' => $this->l('Default language'),
                    'type' => 'select',
                    'options' => array(
                        'query' => Language::getLanguages(false),
                        'id' => 'id_lang',
                        'name' => 'name'),
                    'col' => 4,
                    'default' => (int)Configuration::get('PS_LANG_DEFAULT'),
                    'jsType' => 'string',
                ),
                'PS_CURRENCY_DEFAULT' => array(
                    'label' => $this->l('Default currency'),
                    'type' => 'select',
                    'options' => array(
                        'query' => Currency::getCurrencies(),
                        'id' => 'id_currency',
                        'name' => 'name'
                    ),
                    'col' => 4,
                    'default' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'jsType' => 'string',
                ),
                'PS_COUNTRY_DEFAULT' => array(
                    'label' => $this->l('Default country'),
                    'type' => 'select',
                    'options' => array(
                        'query' => $loadCountries ? $this->getCountries($this->context->language->id, true) : Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name'
                    ),
                    'col' => 4,
                    'default' => (int)Configuration::get('PS_COUNTRY_DEFAULT'),
                    'jsType' => 'string',
                ),
                'ETS_GEO_IGNORE_BOTS' => array(
                    'label' => $this->l('Ignore bots'),
                    'type' => 'switch',
                    'default' => 1,
                    'jsType' => 'string',
                    'desc' => $this->l('Do not redirect search engine bots (Google, Bing, Yahoo, etc.) to another URL. This is recommended for SEO optimization.'),
                ),
                'ETS_GEO_ON_HOME_ONLY' => array(
                    'label' => $this->l('Only auto set language, currency, tax and shipping cost when customer lands on home page'),
                    'type' => 'switch',
                    'default' => 0,
                    'jsType' => 'string',
                    'desc' => $this->l('This is to stop geolocation for inner pages (product page, category page, cms page, etc.) '),
                ),
                'ETS_GEO_HIDE_NOTIFICATION' => array(
                    'label' => $this->l('Ask customer for confirmation before changing language and currency'),
                    'type' => 'switch',
                    'default' => 1,
                    'jsType' => 'string',
                ),
                'ETS_GEO_ENABLE_SWITCH' => array(
                    'label' => $this->l('Enable location switching option?'),
                    'type' => 'switch',
                    'default' => 1,
                    'jsType' => 'string',
                    'desc' => $this->l('Allow customer to reselect their country manually. Language, currency, taxes and shipping cost will change accordingly to the selected country')
                ),
            );
            $this->configs_messages = array(
                'ETS_GEO_CONFIRM_MSG' => array(
                    'label' => $this->l('Confirmation message before changing both language and currency'),
                    'type' => 'textarea',
                    'cols' => 60,
                    'rows' => 3,
                    'lang' => true,
                    'required' => true,
                    'class' => 'rte',
                    'default' => $this->sprintf($this->l('Our system detects that you are visiting our website from [1][detected_country][/1]. Do you want to change website language from [1][current_language][/1] to [1][detected_language][/1] and currency from [1][current_currency][/1] to [1][detected_currency][/1] ?'), 'strong'),
                    'desc' => $this->sprintf($this->l('Available variables: [1][detected_country][/1], [1][detected_language][/1], [1][detected_currency][/1], [1][current_language][/1], [1][current_currency][/1]'), 'span class="light_hight_color"'),
                ),
                'ETS_GEO_LANGUAGE_MSG' => array(
                    'label' => $this->l('Confirmation message before changing language only'),
                    'type' => 'textarea',
                    'cols' => 60,
                    'rows' => 3,
                    'lang' => true,
                    'required' => true,
                    'default' => $this->sprintf($this->l('Our system detects that you are visiting our website from [1][detected_country][/1]. Do you want to change website language from [1][current_language][/1] to [1][detected_language][/1] ?'), 'strong'),
                    'desc' => $this->sprintf($this->l('Available variables: [1][detected_country][/1], [1][detected_language][/1], [1][current_language][/1]'), 'span class="light_hight_color"'),
                ),
                'ETS_GEO_CURRENCY_MSG' => array(
                    'label' => $this->l('Confirmation message before changing currency only'),
                    'type' => 'textarea',
                    'cols' => 60,
                    'rows' => 3,
                    'lang' => true,
                    'required' => true,
                    'default' => $this->sprintf($this->l('Our system detects that you are visiting our website from [1][detected_country][/1]. Do you want to change website currency from [1][current_currency][/1] to [1][detected_currency][/1] ?'), 'strong'),
                    'desc' => $this->sprintf($this->l('Available variables: [1][detected_country][/1], [1][detected_currency][/1], [1][current_currency][/1]'), 'span class="light_hight_color"'),
                ),
                'ETS_GEO_SETTING_MSG' => array(
                    'label' => $this->l('Setting language and currency notification message'),
                    'type' => 'textarea',
                    'cols' => 60,
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('We are setting your language and currency. Please wait a moment..!'),
                ),
                'ETS_GEO_CHOOSE_MSG' => array(
                    'label' => $this->l('Message displayed on "Choose your location" popup'),
                    'type' => 'textarea',
                    'cols' => 60,
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('Taxes, delivery options, shipping price and delivery speeds may vary for different locations'),
                ),
                'ETS_GEO_BLOG_MSG' => array(
                    'label' => $this->l('Blocking message'),
                    'type' => 'textarea',
                    'cols' => 60,
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('Sorry! You are blocked from accessing this website.'),
                ),
            );
            $this->help = array(
                'form' => array(
                    'legend' => array('title' => $this->l('Configuration')),
                    'input' => array(),
                    'submit' => array('title' => $this->l('Save')),
                    'name' => 'tab_messages'),
                'configs' => array(),
            );
            $this->configTabs = array(
                'set' => array(
                    'name' => 'settings',
                    'label' => $this->l('Settings '),
                    'is_conf' => true,
                    'render' => 'form',
                    'value' => 'configs_settings'
                ),
                'mes' => array(
                    'name' => 'messages',
                    'label' => $this->l('Messages'),
                    'is_conf' => true,
                    'render' => 'form',
                    'value' => 'configs_messages'
                ),
            );
            $this->quickTabs = array(
                array(
                    'class_name' => 'AdminGeoLocationStatistics',
                    'tab_name' => $this->l('Statistics'),
                    'icon' => 'fa fa-line-chart',
                ),
                array(
                    'class_name' => 'AdminGeoLocationSettings',
                    'tab_name' => $this->l('Settings'),
                    'icon' => 'fa fa-cogs',
                ),
                array(
                    'class_name' => 'AdminGeoLocationRules',
                    'tab_name' => $this->l('Rules'),
                    'icon' => 'fa fa-list-ul',
                ),
                array(
                    'class_name' => 'AdminGeoLocationMessages',
                    'tab_name' => $this->l('Messages'),
                    'icon' => 'fa fa-commenting-o',
                ),
                array(
                    'class_name' => 'AdminGeoLocationHelp',
                    'tab_name' => $this->l('Help'),
                    'icon' => 'fa fa-question-circle',
                ),
            );
        }
    }

    public function getContent()
    {
        $this->defines();
        if (!$this->isGeoLiteCityAvailable()) {
            $this->smarty->assign(array(
                'is_17' => $this->is17,
                'link_download' => ($this->is17 ? 'https://onedrive.live.com/download?cid=79CEADAC174D772A&resid=79CEADAC174D772A%21106&authkey=ADQ9oeyL1UepIDY' : 'https://onedrive.live.com/download?cid=79CEADAC174D772A&resid=79CEADAC174D772A%21107&authkey=AEw5Uk9n2CAzMOg'),
                'link_auto' => $this->getAdminLink(array('control' => 'upload_geolite')),
            ));
            $this->_html = $this->displayWarning(trim($this->display(__FILE__, 'admin_warning.tpl')));
            Configuration::updateValue('PS_GEOLOCATION_ENABLED', 0);
        }
        if (!Configuration::get('PS_GEOLOCATION_ENABLED')) {
            $this->smarty->assign(array(
                'message_waring' => '"' . $this->l('Geolocation by IP address') . '" ' . $this->l('is disabled. Please enable this option '),
            ));
        }
        $control = trim(Tools::getValue('control'));
        if (!$control) {
            Tools::redirectAdmin($this->getAdminLink() . '&control=statistics');
        }
        if ($control == 'upload_geolite') {
            $this->_postUploadFile();
        } elseif ($control == 'statistics') {
            $this->_postStatistics();
        } elseif ($control == 'settings') {
            $this->_postConfig($this->configs_settings);
        } elseif ($control == 'rules') {
            $this->_postRules();
        } elseif ($control == 'messages') {
            $this->_postConfig($this->configs_messages);
        }
        return $this->getAminHtml($control);
    }

    public function getAminHtml($control)
    {
        $this->smarty->assign(array(
            'ets_geolocation_ajax_url' => $this->getAdminLink() . '&ajaxproductsearch=true',
            'ets_geolocation_author_ajax_url' => $this->getAdminLink() . '&ajaxCustomersearch=true',
            'ets_geolocation_default_lang' => Configuration::get('PS_LANG_DEFAULT'),
            'ets_geolocation_is_updating' => Tools::getValue('id_post') || Tools::getValue('id_category') ? 1 : 0,
            'ets_geolocation_is_config_page' => Tools::getValue('control') == 'config' ? 1 : 0,
            'ets_geolocation_invalid_file' => $this->l('Invalid file'),
            'ets_geolocation_module_dir' => $this->_path,
            'ets_geolocation_sidebar' => $this->renderTabs(),
            'ets_geolocation_body_html' => $this->renderAdminBodyHtml($control),
            'ets_geolocation_error_message' => $this->errorMessage,
            'control' => Tools::getValue('control'),
        ));
        return $this->display(__file__, 'admin.tpl');
    }

    public function renderAdminBodyHtml($control)
    {
        if ($control == 'statistics') {
            $this->renderStatic();
        } elseif ($control == 'settings') {
            $this->renderForm(array(
                'fields' => $this->configs_settings,
                'title' => $this->l('Settings'),
                'icon' => '',
            ));
        } elseif ($control == 'rules') {
            $this->renderRulesForm();
        } elseif ($control == 'messages') {
            $this->renderForm(array(
                'fields' => $this->configs_messages,
                'title' => $this->l('Messages'),
                'icon' => '',
            ));
        } elseif ($control == 'help') {
            $this->renderHelp();
        }
        return $this->_html;
    }

    public function renderHelp()
    {
        $this->context->smarty->assign(array(
            'is_17' => $this->is17,
            'link_download' => $this->is17 ? 'https://onedrive.live.com/download?cid=79CEADAC174D772A&resid=79CEADAC174D772A%21106&authkey=ADQ9oeyL1UepIDY' : 'https://onedrive.live.com/download?cid=79CEADAC174D772A&resid=79CEADAC174D772A%21107&authkey=AEw5Uk9n2CAzMOg',
        ));

        $this->_html .= $this->display(__FILE__, 'admin_help.tpl');
    }

    public function renderStatic()
    {
        $this->smarty->assign(array(
            'url_post' => $this->getAdminLink() . '&control=statistics'
        ));

        $this->_html .= $this->display(__FILE__, 'statics_form.tpl');
    }

    public function gz_file_get_contents($filename, $use_include_path = 0)
    {
        $data = '';
        $file = @gzopen($filename, 'rb', $use_include_path);
        if ($file) {
            while (!@gzeof($file)) {
                $data .= @gzread($file, 1024);
            }
        }
        @gzclose($file);
        return $data;
    }

    private function _postUploadFile()
    {
        $folder_zip = dirname(__FILE__) . '/cache/';
        $local_file = $folder_zip . ($this->is17 ? 'GeoLite2-City.zip' : 'GeoLiteCity.zip');
        $file_copy = $folder_zip . _PS_GEOIP_CITY_FILE_;
        $source = $this->is17 ? "https://onedrive.live.com/download?cid=79CEADAC174D772A&resid=79CEADAC174D772A%21106&authkey=ADQ9oeyL1UepIDY" : 'https://onedrive.live.com/download?cid=79CEADAC174D772A&resid=79CEADAC174D772A%21107&authkey=AEw5Uk9n2CAzMOg'; // THE FILE URL
        if (!is_dir($folder_zip)) {
            mkdir($folder_zip, 0755);
        }
        if (!@file_exists($local_file)) {
            if (false) {
                $data = Tools::file_get_contents($source, false, null, 1800);
                $file = @fopen($local_file, "w+");
                fputs($file, $data);
                fclose($file);
                if (!@file_exists($local_file)) {
                    $this->_errors[] = $this->l('Did not create file');
                } elseif (!@filesize($local_file) || @filesize($local_file) < 1) {
                    @unlink($local_file);
                    $this->_errors[] = $this->l('Download of Geo database was failed, server may have been timed out. Please try again.');
                } elseif (!@file_put_contents($file_copy, $this->gz_file_get_contents($local_file))) {
                    $this->_errors[] = sprintf($this->l('Cannot unzip file %s'), basename($local_file));
                }
            } else {
                $zipResource = fopen($local_file, "w+");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $source);
                curl_setopt($ch, CURLOPT_FAILONERROR, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_FILE, $zipResource);
                $result = curl_exec($ch);
                if (!$result) {
                    $this->_errors[] = curl_error($ch);
                } else {
                    fclose($zipResource);
                    $zip = new ZipArchive;
                    if ($zip->open($local_file) != "true") {
                        $this->_errors[] = sprintf($this->l('Unable to open the file %s'), $local_file);
                    }
                    if (!$this->_errors) {
                        $zip->extractTo($folder_zip);
                        if (!@filesize($local_file) || @filesize($local_file) < 1) {
                            $this->_errors[] = $this->l('Download of Geo database was failed, server may have been timed out. Please try again.');
                        } elseif (@file_exists($local_file)) {
                            Configuration::updateValue('PS_GEOLOCATION_ENABLED', 1);
                        }
                    }
                    $zip->close();
                }
                curl_close($ch);
            }
        } elseif (!@filesize($local_file) || @filesize($local_file) < 1) {
            @unlink($local_file);
            $this->_errors[] = $this->l('Download of Geo database was failed, server may have been timed out. Please try again.');
        } elseif (!@file_put_contents($file_copy, $this->gz_file_get_contents($local_file))) {
            $this->_errors[] = sprintf($this->l('Cannot unzip file %s'), basename($local_file));
        }
        if (!$this->_errors) {
            if (@file_exists($file_copy)) {
                @copy($file_copy, _PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_);
                @unlink($file_copy);
            }
            if ($this->isGeoLiteCityAvailable()) {
                Configuration::updateValue('PS_GEOLOCATION_ENABLED', 1);
            }
        }
        die(json_encode(array(
            'error' => $this->_errors ? $this->displayError($this->_errors) : false,
            'message' => @filemtime(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_) ? $this->displaySuccessMessage($this->l('Successfully downloaded')) : $this->displaySuccessMessage($this->l('Update failed')),
        )));
    }

    private function _postStatistics()
    {
        $maps_visit = array();
        $maps_visit_color = array();
        $param_maps = array();

        $first_filter = 'year';
        $max_year = date('Y');
        $min_year = Geo_visit::getMinYear();
        if (($max_year - $min_year) >= 5) {
            $first_filter = 'all_times';
        }

        $param_maps['value'] = Tools::isSubmit('submit_ajax') ? Tools::getValue('geo_option_filter') : $first_filter;
        $param_maps['status'] = 'ajax';
        $param_maps['type'] = 'maps';
        $data_visits = Geo_visit::getDataVisit($param_maps);
        $total_all = 0;
        $caculation_total = array();
        /* Prosess maps */
        $arr_color = array('#004e64', '#2485a0', '#00a1ce', '#79cee5');
        if (!empty($data_visits) && is_array($data_visits)) {
            for ($i = 0, $total_i = count($data_visits); $i < $total_i; $i++) {
                $maps_visit[$data_visits[$i]['iso_code']] = $data_visits[$i]['visit'];
                $total_all = $total_all + (int)$data_visits[$i]['visit'];
                if ($i < 3) {
                    $caculation_total['best_' . $i]['visit'] = (int)$data_visits[$i]['visit'];
                    $caculation_total['best_' . $i]['name'] = $data_visits[$i]['name'];
                    if (isset($data_visits[$i - 1]['visit']) && $caculation_total['best_' . $i]['visit'] == $data_visits[$i - 1]['visit']) {
                        $caculation_total['best_' . $i]['color'] = $arr_color[$i - 1];
                    } else {
                        $caculation_total['best_' . $i]['color'] = $arr_color[$i];
                    }
                    $maps_visit_color[$data_visits[$i]['iso_code']] = $arr_color[$i];
                } else {
                    if (!isset($caculation_total['other_visit']['visit'])) {
                        $caculation_total['other_visit']['visit'] = (int)$data_visits[$i]['visit'];
                        $caculation_total['other_visit']['name'] = $this->l('Other');
                        $caculation_total['other_visit']['color'] = $arr_color[3];
                    } else {
                        $caculation_total['other_visit']['visit'] = $caculation_total['other_visit']['visit'] + (int)$data_visits[$i]['visit'];
                    }
                    $maps_visit_color[$data_visits[$i]['iso_code']] = $arr_color[3];
                }
            }
        }
        $res_mapojb = array(
            'res_visit' => (object)$maps_visit,
            'res_visit_color' => (object)$maps_visit_color,
            'total_line' => array(),
            'param_filter_map' => $param_maps['value'] == 'all_times' ? $this->l('All times') : ($param_maps['value'] == 'month' ? $this->l('Day') : $this->l('This year'))
        );

        if (!empty($caculation_total) && is_array($caculation_total)) {
            $arr_tem = array();
            foreach ($caculation_total as $key => $value) {
                $arr_tem[$key]['percent'] = round($value['visit'] * 100 / $total_all, 2);
                $arr_tem[$key]['name'] = $value['name'];
                $arr_tem[$key]['color'] = $value['color'];
                $arr_tem[$key]['visit'] = $value['visit'];
            }
            $res_mapojb['total_line'] = $arr_tem;
        }

        if (Tools::isSubmit('ajax') && Tools::getValue('get_maps')) {
            die(
            json_encode(
                $res_mapojb
            )
            );
        }
        $this->context->smarty->assign(
            $res_mapojb
        );
        $param_last = array();
        $arr_color = array('#f381aa', '#79ceb3', '#f9998c', '#6ac9e8');
        if (Tools::isSubmit('submit_ajax')) {
            $param_last['value'] = Tools::getValue('geo_option_filter');
            $param_last['status'] = 'ajax';
            $param_last['type'] = 'maps';
        } else {
            $param_last['value'] = $first_filter;
            $param_last['status'] = 'ajax';
            $param_last['type'] = 'maps';
        }
        $visit_last_day = Geo_visit::getDataVisit($param_last);

        $doughnut_data = array();
        $sum = 0;
        $total_sum = 0;
        $tolta = count($visit_last_day);

        $arr_data_tron = array();
        $arr_color_tron = array();
        $arr_label_tron = array();
        $tron_dataset = array();

        if ($visit_last_day && is_array($visit_last_day)) {
            for ($i = 0; $i < $tolta; $i++) {
                $total_sum = $total_sum + $visit_last_day[$i]['visit'];
                if ($i < 3) {
                    $doughnut_data[$visit_last_day[$i]['name']] = $visit_last_day[$i]['visit'];
                    $arr_data_tron[$i] = $visit_last_day[$i]['visit'];
                    $arr_color_tron[$i] = $arr_color[$i];
                    $arr_label_tron[$i] = $visit_last_day[$i]['name'];
                } else {
                    $sum = $sum + $visit_last_day[$i]['visit'];
                }
            }
            if ($tolta > 3) {
                $doughnut_data['Other'] = $sum;
                $arr_data_tron[3] = $sum;
                $arr_color_tron[3] = $arr_color[3];
                $arr_label_tron[3] = $this->l('Other');
            }

            $tron_dataset['data'] = $arr_data_tron;
            $tron_dataset['backgroundColor'] = $arr_color_tron;
        }
        $label_filter = $param_last['value'] == 'all_times' ? $this->l('All times') : ($param_last['value'] == 'month' ? $this->l('Day') : $this->l('This year'));
        $this->context->smarty->assign(
            array(
                'datasets_tron' => $tron_dataset,
                'labels_tron' => $arr_label_tron,
                'doughnut_data' => $doughnut_data,
                'data_filter_tron' => $label_filter,
                'visit_text' => $total_sum,
                'visit_total' => $total_sum
            )
        );
        if (Tools::isSubmit('ajax') && Tools::getValue('doughnut')) {
            die(
            json_encode(
                array(
                    'doughnut_data' => $tron_dataset,
                    'label_tron' => $arr_label_tron,
                    'data_filter_tron' => $label_filter,
                    'visit_text' => $total_sum,
                )
            )
            );
        }

        $param_line = array();
        if (Tools::isSubmit('submit_ajax')) {
            $param_line['value'] = Tools::getValue('geo_option_filter');
            $param_line['status'] = 'ajax';
            $param_line['chart'] = 'linechar';
        } else {
            $param_line['value'] = $first_filter;
            $param_line['status'] = 'ajax';
            $param_line['chart'] = 'linechar';
        }

        $data_linechar = Geo_visit::getDataVisit($param_line);
        $arr_temp_data = array();
        $char_index = 0;
        $min_year = $distance = $max_year = 0;
        $max_year = date('Y');
        $min_year = Geo_visit::getMinYear();
        $distance = ($max_year - $min_year);
        $is_alltimes = false;
        if ($param_line['value'] == 'all_times' && $distance > 3) {
            $is_alltimes = true;
            $char_index = $distance;
        } else {
            $char_index = $char_index != 0 ? $char_index : ($param_line['value'] == 'year' ? 12 : (int)date('t'));
        }
        foreach ($data_linechar as $value) {
            if ($is_alltimes) {
                $arr_temp_data[$value['id_country']]['day_visit'][$value['year']] = isset($arr_temp_data[$value['id_country']]['day_visit'][$value['year']]) ? ($arr_temp_data[$value['id_country']]['day_visit'][$value['year']] + $value['total_visit']) : $value['total_visit'];
                $arr_temp_data[$value['id_country']]['name'] = $value['name'];
            } else {
                $arr_temp_data[$value['id_country']]['day_visit'][$param_line['value'] == 'year' ? $value['month'] : $value['day']] = isset($arr_temp_data[$value['id_country']]['day_visit'][$param_line['value'] == 'year' ? $value['month'] : $value['day']]) ? ($arr_temp_data[$value['id_country']]['day_visit'][$param_line['value'] == 'year' ? $value['month'] : $value['day']] + $value['total_visit']) : $value['total_visit'];
                $arr_temp_data[$value['id_country']]['name'] = $value['name'];
            }
        }

        foreach ($arr_temp_data as &$arr) {
            $arr['total_visit'] = array_sum($arr['day_visit']);
        }
        usort($arr_temp_data, function ($a, $b) {
            return $b['total_visit'] - $a['total_visit'];
        });
        $tolta_linechar = count($arr_temp_data);
        $line_data = array();
        $arr_temp_four = array();
        $arr_label = array();

        for ($i = 0; $i < $tolta_linechar; $i++) {
            if ($i < 3) {
                $arr_temp = array();
                $arr_temp['label'] = $arr_temp_data[$i]['name'];
                $arr_temp['backgroundColor'] = $arr_color[$i];
                $arr_temp['borderColor'] = $arr_color[$i];
                $arr_temp['borderWidth'] = 1;
                $arr_temp['fill'] = 'boundary';
                if ($is_alltimes) {
                    $flag = 0;
                    for ($j = $min_year; $j <= $max_year; $j++) {
                        if (array_key_exists($j, $arr_temp_data[$i]['day_visit'])) {
                            $arr_temp['data'][$flag] = (int)$arr_temp_data[$i]['day_visit'][$j];
                        } else {
                            $arr_temp['data'][$flag] = 0;
                        }
                        $flag = $flag + 1;
                    }
                } else {
                    for ($j = 1; $j <= $char_index; $j++) {
                        if (array_key_exists($j, $arr_temp_data[$i]['day_visit'])) {
                            $arr_temp['data'][$j - 1] = (int)$arr_temp_data[$i]['day_visit'][$j];
                        } else {
                            $arr_temp['data'][$j - 1] = 0;
                        }
                    }
                }

                $line_data[$i] = (object)$arr_temp;
            } else {
                $arr_temp_four['label'] = 'Other';
                $arr_temp_four['backgroundColor'] = $arr_color[3];
                $arr_temp_four['borderColor'] = $arr_color[3];
                $arr_temp_four['borderWidth'] = 1;
                $arr_temp_four['fill'] = 'boundary';
                if ($is_alltimes) {
                    $flag = 0;
                    for ($j = $min_year; $j <= $max_year; $j++) {
                        $arr_temp_four['data'][$flag] = (isset($arr_temp_four['data'][$flag]) && $arr_temp_four['data'][$flag]) ? $arr_temp_four['data'][$flag] : 0;
                        if (array_key_exists($j, $arr_temp_data[$i]['day_visit'])) {
                            $arr_temp_four['data'][$flag] = (int)$arr_temp_four['data'][$flag] + $arr_temp_data[$i]['day_visit'][$j];
                        } else {
                            $arr_temp['data'][$flag] = 0;
                        }
                        $flag = $flag + 1;
                    }
                } else {
                    for ($j = 1; $j <= $char_index; $j++) {
                        $arr_temp_four['data'][$j - 1] = (isset($arr_temp_four['data'][$j - 1]) && $arr_temp_four['data'][$j - 1]) ? $arr_temp_four['data'][$j - 1] : 0;
                        if (array_key_exists($j, $arr_temp_data[$i]['day_visit'])) {
                            $arr_temp_four['data'][$j - 1] = (int)$arr_temp_four['data'][$j - 1] + $arr_temp_data[$i]['day_visit'][$j];
                        } else {
                            $arr_temp['data'][$j] = 0;
                        }
                    }
                }

            }
        }

        if (isset($arr_temp_four['data']) && $arr_temp_four['data']) {
            $line_data[3] = (object)$arr_temp_four;
        }
        if ($is_alltimes) {
            for ($i = $min_year; $i <= $max_year; $i++) {
                $arr_label[] = (int)$i;
            }
        } else {
            for ($i = 1; $i <= $char_index; $i++) {
                $arr_label[] = $i;
            }
        }
        $arr_rest = array(
            'linechar_data' => $line_data,
            'data_label' => $arr_label,
            'data_filter' => $param_line['value'] == 'all_times' ? $this->l('All times') : ($param_line['value'] == 'month' ? $this->l('Day') : $this->l('This year')),
            'data_filter_label' => $param_line['value'] == 'all_times' ? $this->l('Year') : ($param_line['value'] == 'month' ? $this->l('Day') : $this->l('Month')),
            'label_value' => $this->l('Visits'),
            'char_lists' => array('wrapper_doughnut', 'wrapper_linechar'),
        );
        $this->smarty->assign(
            $arr_rest
        );
        if (Tools::isSubmit('ajax') && (Tools::getValue('line_char') || Tools::getValue('horizontal_char'))) {
            die(json_encode(
                $arr_rest
            ));
        }
    }

    public function getAdminLink($args = array())
    {
        $uri = $this->context->link->getAdminLink('AdminModules', isset($args['token']) ? $args['token'] : true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        if ($args) {
            $urls = array();
            foreach ($args as $key => $param) {
                if ($key != 'token') {
                    $urls[] = $key . '=' . $param;
                }
            }
            if ($urls) {
                $uri .= '&' . implode('&', $urls);
            }
        }
        return $uri;
    }

    private function _postConfig($configs = array())
    {
        if (Tools::isSubmit('saveConfig')) {
            $languages = Language::getLanguages(false);
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            if ($configs) {
                foreach ($configs as $key => $config) {
                    if ($key == 'PS_GEOLOCATION_ENABLED' && !$this->isGeoLiteCityAvailable()) {
                        $this->_errors[] = $this->l('The geolocation database is unavailable.');
                    } elseif (isset($config['lang']) && $config['lang']) {
                        if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key . '_' . $id_lang_default) == '')) {
                            $this->_errors[] = $config['label'] . ' ' . $this->l('is required');
                        }
                    } else {
                        if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key) == '')) {
                            $this->_errors[] = $config['label'] . ' ' . $this->l('is required');
                        }
                        if (isset($config['validate']) && method_exists('Validate', $config['validate'])) {
                            $validate = $config['validate'];
                            if (!Validate::$validate(trim(Tools::getValue($key))))
                                $this->_errors[] = $config['label'] . ' ' . $this->l('is invalid');
                            unset($validate);
                        } elseif (!Validate::isCleanHtml(trim(Tools::getValue($key)))) {
                            $this->_errors[] = $config['label'] . ' ' . $this->l('is invalid');
                        }
                    }
                }
            }

            if (!$this->_errors) {
                if ($configs) {
                    foreach ($configs as $key => $config) {
                        if (isset($config['lang']) && $config['lang']) {
                            $values = array();
                            foreach ($languages as $lang) {
                                if ($config['type'] == 'switch')
                                    $values[$lang['id_lang']] = (int)trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? 1 : 0;
                                else {
                                    $values[$lang['id_lang']] = trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? trim(Tools::getValue($key . '_' . $lang['id_lang'])) : trim(Tools::getValue($key . '_' . $id_lang_default));
                                }
                            }
                            Configuration::updateValue($key, $values, true);
                        } else {
                            if ($config['type'] == 'switch') {
                                Configuration::updateValue($key, (int)trim(Tools::getValue($key)) ? 1 : 0);
                            } elseif ($config['type'] == 'checkbox') {
                                Configuration::updateValue($key, implode(',', Tools::getValue($key)));
                            } else {
                                Configuration::updateValue($key, trim(Tools::getValue($key)));
                            }
                        }
                    }
                }
            }
            if (count($this->_errors)) {
                $this->errorMessage = $this->displayError($this->_errors);
            }
            if (!count($this->_errors)) {
                Tools::redirectAdmin($this->getAdminLink(array(
                    'conf' => 4,
                    'control' => Tools::getValue('control', 'statistics')
                )));
            }
        }
    }

    public function _postRules()
    {
        if (Tools::isSubmit('submitResetets_geo_rule')) {
            $this->processResetFilters();
        } else {
            $id_rule = (int)Tools::getValue('id_rule');
            $geo_rule = new Geo_rules($id_rule);
            if ($id_rule && !$geo_rule->id && !Tools::isSubmit('list')) {
                Tools::redirectAdmin($this->getAdminLink());
            } elseif ($geo_rule->id && Tools::isSubmit('change_enabled')) {
                $field = Tools::getValue('field');
                $geo_rule->$field = !(int)$geo_rule->$field;
                if ($geo_rule->update()) {
                    if (Tools::getValue('ajax')) {
                        die(json_encode(array(
                            'listId' => $id_rule,
                            'enabled' => $geo_rule->$field,
                            'field' => $field,
                            'message' => $this->displaySuccessMessage($this->l('Successfully updated')),
                            'messageType' => 'success',
                            'href' => $this->getAdminLink() . '&control=rules&field=' . $field . '&id_rule=' . $id_rule,
                        )));
                    } else
                        Tools::redirectAdmin($this->getAdminLink(array('conf' => 4)) . '&control=rules&list=true');
                }
            } elseif (Tools::isSubmit('deleteets_geo_rule') && $geo_rule->id) {
                if ($geo_rule->delete()) {
                    Tools::redirectAdmin($this->getAdminLink(array('conf' => 4)) . '&control=rules&list=true');
                } else {
                    $this->_errors[] = $this->l('Could not delete the rule. Please try again');
                }

            } elseif (Tools::isSubmit('saveRules')) {
                if (!($all_countries = (int)Tools::getValue('all_countries')) && empty(Tools::getValue('countries'))) {
                    $this->_errors[] = $this->l('Countries is required.');
                }
                if (!Validate::isFloat(Tools::getValue('priority')) || (float)Tools::getValue('priority') < 0) {
                    $this->_errors[] = $this->l('Priority is invalid');
                }

                if (($urlRedirect = Tools::getValue('url_redirect'))) {
                    if (!Validate::isAbsoluteUrl(trim($urlRedirect))) {
                        $this->_errors[] = $this->l('Redirect to is invalid');
                    } elseif (preg_match('#^http(?:s?):\/\/(?:www\.)?(?:' . $this->context->shop->domain . ')((' . str_replace("/", "\/", rtrim($this->context->shop->getBaseURI(), '/')) . ')(.*)?)$#', $urlRedirect, $matches)) {
                        $ok = 1;

                        if (!trim($matches[3], '/')) {
                            $ok = 1;
                        } elseif ($shops = Shop::getShops()) {
                            foreach ($shops as $shop) {
                                if ($shop['domain'] == $this->context->shop->domain) {

                                    if ((rtrim($shop['uri'], '/') == rtrim($matches[2], '/')) && (int)$shop['id_shop'] == $this->context->shop->id) {
                                        $ok = 0;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($ok) {
                            $this->_errors[] = $this->l('The domain of the redirect url must not be the same as your website domain');
                        }
                    }
                }


                if (!$this->_errors) {
                    $geo_rule->enabled = Tools::getValue('enabled') ? 1 : 0;
                    $geo_rule->all_countries = $all_countries;
                    $geo_rule->countries = $geo_rule->all_countries ? array() : Tools::getValue('countries');
                    $geo_rule->disable_geo = Tools::getValue('disable_geo') ? 1 : 0;
                    $geo_rule->lang_to_set = Tools::getValue('lang_to_set');
                    $geo_rule->currency_to_set = Tools::getValue('currency_to_set');
                    $geo_rule->block_user = Tools::getValue('block_user') ? 1 : 0;
                    $geo_rule->url_redirect = Tools::getValue('url_redirect');
                    $geo_rule->priority = Tools::getValue('priority');
                    $geo_rule->id_shop = $this->context->shop->id;
                    $msg = $geo_rule->validateFields(false, true);
                    if ($msg && $msg !== true) {
                        $this->_errors[] = $msg;
                    }
                    if (!$this->_errors && !$geo_rule->save()) {
                        $this->_errors[] = $this->l('Failed to save. There was an unknown error happens');
                    }
                }
            }
            if ($this->_errors) {
                $this->errorMessage = $this->displayError($this->_errors);
            } else {
                if (Tools::isSubmit('saveRules') && ($id_rule = Tools::getValue('id_rule'))) {
                    Tools::redirectAdmin($this->getAdminLink(array('conf' => 4)) . '&update' . $this->list_id . '&id_rule=' . (int)$id_rule . '&control=rules');
                } elseif (Tools::isSubmit('saveRules')) {
                    Tools::redirectAdmin($this->getAdminLink(array('conf' => 3)) . '&update' . $this->list_id . '&id_rule=' . (int)$geo_rule->id . '&control=rules');
                }
            }
        }
    }

    public function renderForm($args = array())
    {
        if (!isset($args['fields']) || !$args['fields']) {
            return false;
        }
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => isset($args['title']) ? $args['title'] : $this->l('Configurations'),
                    'icon' => isset($args['icon']) ? $args['icon'] : 'icon-Admin'
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        if ($configs = $args['fields']) {
            foreach ($configs as $key => $config) {
                $fields = $config;
                $fields['name'] = $key;
                if (isset($config['type']) && $config['type'] == 'switch') {
                    $fields['values'] = isset($config['values']) ? $config['values'] : array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    );
                }
                $fields_form['form']['input'][] = $fields;
            }
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveConfig';
        $helper->currentIndex = $this->getAdminLink(array('token' => false)) . '&control=' . Tools::getValue('control');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $fields = array();
        $languages = Language::getLanguages(false);
        $helper->override_folder = '/';
        if (Tools::isSubmit('saveConfig')) {
            if ($configs) {
                foreach ($configs as $key => $config) {
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang'], isset($config['default']) ? $config['default'] : '');
                        }
                    } else
                        $fields[$key] = Tools::getValue($key, isset($config['default']) ? $config['default'] : '');
                }
            }
        } else {
            if ($configs) {
                foreach ($configs as $key => $config) {
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = Configuration::get($key, $l['id_lang']);
                        }
                    } elseif ($config['type'] == 'checkbox') {
                        $fields[$key] = explode(',', Configuration::get($key));
                    } else
                        $fields[$key] = Configuration::get($key);
                }
            }
        }
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $fields,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'configTabs' => Tools::getValue('control') == 'config' ? $this->configTabs : array(),
        );
        $this->_html .= $helper->generateForm(array($fields_form));
    }

    public function renderTabs()
    {
        $intro = true;
        $localIps = array(
            '127.0.0.1',
            '::1'
        );
        $baseURL = Tools::strtolower(self::getBaseModLink());
        if (!Tools::isSubmit('intro') && (in_array(Tools::getRemoteAddr(), $localIps) || preg_match('/^.*(localhost|demo|test|dev|:\d+).*$/i', $baseURL)))
            $intro = false;
        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'list' => array(
                array(
                    'label' => $this->l('Statistics'),
                    'url' => $this->getAdminLink() . '&control=statistics&list=false',
                    'id' => 'ets_tab_statistics',
                    'hasAccess' => '',
                    'controller' => 'AdminEtsGeoStatistics',
                ),
                array(
                    'label' => $this->l('Settings'),
                    'url' => $this->getAdminLink() . '&control=settings&list=true',
                    'id' => 'ets_tab_settings',
                    'hasAccess' => '',
                    'controller' => 'AdminEtsGeosettings',
                ),
                array(
                    'label' => $this->l('Rules'),
                    'url' => $this->getAdminLink() . '&control=rules&list=true',
                    'id' => 'ets_tab_rules',
                    'hasAccess' => '',
                    'controller' => 'AdminEtsGeoRules',
                ),
                array(
                    'label' => $this->l('Messages'),
                    'url' => $this->getAdminLink() . '&control=messages&list=true',
                    'id' => 'ets_tab_messages',
                    'hasAccess' => '',
                    'controller' => 'AdminEtsGeoMessages',
                ),
                array(
                    'label' => $this->l('Help'),
                    'url' => $this->getAdminLink() . '&control=help&list=true',
                    'id' => 'ets_tab_help',
                    'hasAccess' => '',
                    'controller' => 'AdminEtsGeoHelp',
                ),
            ),
            'admin_path' => $this->getAdminLink(),
            'active' => 'ets_tab_' . (trim(Tools::getValue('control')) ? trim(Tools::getValue('control')) : (Tools::getValue('controller') == 'AdminEtsGeoStatistics' ? 'statistics' : 'post')),
            'other_modules_link' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&othermodules=1',
            'intro' => $intro,
        ));
        return $this->display(__file__, 'sidebar.tpl');
    }

    public static function getBaseModLink()
    {
        $context = Context::getContext();
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $context->shop->domain . $context->shop->getBaseURI();
    }

    public function displayRecommendedModules()
    {
        $cacheDir = dirname(__file__) . '/../../cache/' . $this->name . '/';
        $cacheFile = $cacheDir . 'module-list.xml';
        $cacheLifeTime = 24;
        $cacheTime = (int)Configuration::getGlobalValue('ETS_MOD_CACHE_' . $this->name);
        $profileLinks = array(
            'en' => 'https://addons.prestashop.com/en/207_ets-soft',
            'fr' => 'https://addons.prestashop.com/fr/207_ets-soft',
            'it' => 'https://addons.prestashop.com/it/207_ets-soft',
            'es' => 'https://addons.prestashop.com/es/207_ets-soft',
        );
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
            if (@file_exists(dirname(__file__) . '/index.php')) {
                @copy(dirname(__file__) . '/index.php', $cacheDir . 'index.php');
            }
        }
        if (!file_exists($cacheFile) || !$cacheTime || time() - $cacheTime > $cacheLifeTime * 60 * 60) {
            if (file_exists($cacheFile))
                @unlink($cacheFile);
            if ($xml = self::file_get_contents($this->shortlink . 'ml.xml')) {
                $xmlData = @simplexml_load_string($xml);
                if ($xmlData && (!isset($xmlData->enable_cache) || (int)$xmlData->enable_cache)) {
                    @file_put_contents($cacheFile, $xml);
                    Configuration::updateGlobalValue('ETS_MOD_CACHE_' . $this->name, time());
                }
            }
        } else
            $xml = Tools::file_get_contents($cacheFile);
        $modules = array();
        $categories = array();
        $categories[] = array('id' => 0, 'title' => $this->l('All categories'));
        $enabled = true;
        $iso = Tools::strtolower($this->context->language->iso_code);
        $moduleName = $this->displayName;
        $contactUrl = '';

        if ($xml && ($xmlData = @simplexml_load_string($xml))) {
            if (isset($xmlData->modules->item) && $xmlData->modules->item) {
                foreach ($xmlData->modules->item as $arg) {
                    if ($arg) {
                        if (isset($arg->module_id) && (string)$arg->module_id == $this->name && isset($arg->{'title' . ($iso == 'en' ? '' : '_' . $iso)}) && (string)$arg->{'title' . ($iso == 'en' ? '' : '_' . $iso)})
                            $moduleName = (string)$arg->{'title' . ($iso == 'en' ? '' : '_' . $iso)};
                        if (isset($arg->module_id) && (string)$arg->module_id == $this->name && isset($arg->contact_url) && (string)$arg->contact_url)
                            $contactUrl = $iso != 'en' ? str_replace('/en/', '/' . $iso . '/', (string)$arg->contact_url) : (string)$arg->contact_url;
                        $temp = array();
                        foreach ($arg as $key => $val) {
                            if ($key == 'price' || $key == 'download')
                                $temp[$key] = (int)$val;
                            elseif ($key == 'rating') {
                                $rating = (float)$val;
                                if ($rating > 0) {
                                    $ratingInt = (int)$rating;
                                    $ratingDec = $rating - $ratingInt;
                                    $startClass = $ratingDec >= 0.5 ? ceil($rating) : ($ratingDec > 0 ? $ratingInt . '5' : $ratingInt);
                                    $temp['ratingClass'] = 'mod-start-' . $startClass;
                                } else
                                    $temp['ratingClass'] = '';
                            } elseif ($key == 'rating_count')
                                $temp[$key] = (int)$val;
                            else
                                $temp[$key] = (string)strip_tags($val);
                        }
                        if ($iso) {
                            if (isset($temp['link_' . $iso]) && isset($temp['link_' . $iso]))
                                $temp['link'] = $temp['link_' . $iso];
                            if (isset($temp['title_' . $iso]) && isset($temp['title_' . $iso]))
                                $temp['title'] = $temp['title_' . $iso];
                            if (isset($temp['desc_' . $iso]) && isset($temp['desc_' . $iso]))
                                $temp['desc'] = $temp['desc_' . $iso];
                        }
                        $modules[] = $temp;
                    }
                }
            }
            if (isset($xmlData->categories->item) && $xmlData->categories->item) {
                foreach ($xmlData->categories->item as $arg) {
                    if ($arg) {
                        $temp = array();
                        foreach ($arg as $key => $val) {
                            $temp[$key] = (string)strip_tags($val);
                        }
                        if (isset($temp['title_' . $iso]) && $temp['title_' . $iso])
                            $temp['title'] = $temp['title_' . $iso];
                        $categories[] = $temp;
                    }
                }
            }
        }
        if (isset($xmlData->{'intro_' . $iso}))
            $intro = $xmlData->{'intro_' . $iso};
        else
            $intro = isset($xmlData->intro_en) ? $xmlData->intro_en : false;
        $this->smarty->assign(array(
            'modules' => $modules,
            'enabled' => $enabled,
            'module_name' => $moduleName,
            'categories' => $categories,
            'img_dir' => $this->_path . 'views/img/',
            'intro' => $intro,
            'shortlink' => $this->shortlink,
            'ets_profile_url' => isset($profileLinks[$iso]) ? $profileLinks[$iso] : $profileLinks['en'],
            'trans' => array(
                'txt_must_have' => $this->l('Must-Have'),
                'txt_downloads' => $this->l('Downloads!'),
                'txt_view_all' => $this->l('View all our modules'),
                'txt_fav' => $this->l('Prestashop\'s favourite'),
                'txt_elected' => $this->l('Elected by merchants'),
                'txt_superhero' => $this->l('Superhero Seller'),
                'txt_partner' => $this->l('Module Partner Creator'),
                'txt_contact' => $this->l('Contact us'),
                'txt_close' => $this->l('Close'),
            ),
            'contactUrl' => $contactUrl,
        ));
        echo $this->display(__FILE__, 'module-list.tpl');
        die;
    }

    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
    {
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create(array(
                "http" => array(
                    "timeout" => $curl_timeout,
                    "max_redirects" => 101,
                    "header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
                ),
                "ssl" => array(
                    "allow_self_signed" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            ));
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
            ));
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        } else {
            return false;
        }
    }

    public function ets_getCountriesNotBlock($idLang, $active = false, $containStates = false, $listStates = true)
    {
        $countries = array();
        $sql = '
		SELECT c.`id_country`,c.`iso_code`,cl.`name`, bu.`block_user`, bu.`disable_geo`  
		FROM `' . _DB_PREFIX_ . 'country` c ' . Shop::addSqlAssociation('country', 'c') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = ' . (int)$idLang . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = c.`id_zone`)
		LEFT JOIN (
		    SELECT `id_country_rule`,`block_user`,`disable_geo` 
		     FROM `' . _DB_PREFIX_ . 'ets_geo_country_rule` as gcrl 
		     JOIN `' . _DB_PREFIX_ . 'ets_geo_rule` grl ON (grl.`id_rule` = gcrl.`id_rule`)
		     WHERE `block_user`= 1 OR `disable_geo` = 1 
            GROUP BY id_country_rule 
        ) bu ON bu.`id_country_rule` = c.`id_country`
		WHERE 1' . ($active ? ' AND c.active = 1' : '') . ($containStates ? ' AND c.`contains_states` = ' . (int)$containStates : '') . ' AND (bu.`block_user` != 1 AND bu.`disable_geo` != 1 OR bu.`block_user` is NULL) 
		ORDER BY cl.name ASC';

        if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql)) {
            foreach ($result as $row) {
                $countries[$row['id_country']] = $row;
            }
        }

        if ($listStates) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'state` ORDER BY `name` ASC');
            foreach ($result as $row) {
                if (isset($countries[$row['id_country']]) && $row['active'] == 1) { /* Does not keep the state if its country has been disabled and not selected */
                    $countries[$row['id_country']]['states'][] = $row;
                }
            }
        }

        return $countries;
    }

    public function displayCountry($value)
    {
        if ($value) {
            $value = Tools::truncate($value, 100);
        }
        return $value;
    }

    public function getFieldList()
    {
        $this->fields_list = array(
            'id_rule' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'class' => 'fixed-width-xs',
            ),
            'countries' => array(
                'title' => $this->l('Countries'),
                'align' => 'left',
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'callback' => 'displayCountry',
                'callback_object' => $this,
            ),
            'lang_to_set' => array(
                'title' => $this->l('Language to set'),
                'align' => 'left',
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            ),
            'currency_to_set' => array(
                'title' => $this->l('Currency to set'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            ),
            'disable_geo' => array(
                'title' => $this->l('Disable GEO'),
                'type' => 'bool',
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'active' => 'field=disable_geo&',
            ),
            'block_user' => array(
                'title' => $this->l('Block access'),
                'type' => 'bool',
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'active' => 'field=block_user&',
            ),
            'enabled' => array(
                'title' => $this->l('Enabled'),
                'type' => 'bool',
                'active' => 'field=enabled&',
                'orderby' => false,
                'search' => false,
                'align' => 'center',
            ),
            'priority' => array(
                'title' => $this->l('Priority'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'align' => 'center',
            )
        );
    }

    public function renderRulesForm()
    {
        if (Tools::isSubmit('addets_geo_rule') || Tools::isSubmit('updateets_geo_rule')) {
            //Form
            $values = array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('No')
                )
            );
            $geo_rule = new Geo_rules((int)Tools::getValue('id_rule'));
            // Getting data languages.
            $languages = Language::getLanguages(false);

            //formatting array
            $languages_list = array(array(
                'id_lang' => 0,
                'name' => $this->l('Auto'),
            ));
            foreach ($languages as $language) {
                $languages_list[] = array(
                    'id_lang' => $language['id_lang'],
                    'name' => $language['name']
                );
            }
            //Getting data currency
            $currencies = Currency::getCurrencies();

            //formatting array
            $currencies_list = array(array(
                'id_currency' => 0,
                'name' => $this->l('Auto'),
            ));
            foreach ($currencies as $currency) {
                $currencies_list[] = array(
                    'id_currency' => $currency['id_currency'],
                    'name' => $currency['name']
                );
            }
            $fields = array(
                array(
                    'label' => $this->l('Id shop'),
                    'type' => 'hidden',
                    'name' => 'id_shop',
                    'default' => (int)$this->context->shop->id,
                ),
                array(
                    'label' => $this->l('Enabled'),
                    'type' => 'switch',
                    'name' => 'enabled',
                    'values' => $values,
                    'default' => 1,
                ),
                array(
                    'label' => $this->l('Countries'),
                    'type' => 'geo_countries',
                    'name' => 'countries',
                    'options' => array(
                        'query' => $this->getCountries($this->context->language->id, true, false, true, (int)Tools::getValue('id_rule')),
                        'id' => 'id_country',
                        'name' => 'name',
                    ),
                    'multiple' => true,
                ),
                array(
                    'label' => $this->l('Disable Geolocation for selected countries'),
                    'type' => 'switch',
                    'name' => 'disable_geo',
                    'values' => $values,
                ),
                array(
                    'label' => $this->l('Language to set'),
                    'type' => 'select',
                    'name' => 'lang_to_set',
                    'options' => array(
                        'query' => $languages_list,
                        'id' => 'id_lang',
                        'name' => 'name',
                    ),
                ),
                array(
                    'label' => $this->l('Currency to set'),
                    'type' => 'select',
                    'name' => 'currency_to_set',
                    'options' => array(
                        'query' => $currencies_list,
                        'id' => 'id_currency',
                        'name' => 'name',
                    ),
                ),
                array(
                    'label' => $this->l('Block all users from the selected countries?'),
                    'type' => 'switch',
                    'name' => 'block_user',
                    'values' => $values,
                    'desc' => $this->l('All users come from these countries will blocked from accessing the website'),
                    'default' => 0,
                ),
                array(
                    'label' => $this->l('Redirect to'),
                    'type' => 'text',
                    'name' => 'url_redirect',
                    'desc' => $this->l('Redirect customer to another website'),
                    'class' => 'col-lg-6',
                ),
                array(
                    'label' => $this->l('Priority'),
                    'type' => 'text',
                    'name' => 'priority',
                    'col' => '2',
                    'required' => true,
                    'default' => 1,
                    'desc' => $this->l('When there is more than one rule is defined for a location, the rule with a smaller priority will be applied'),
                ),
            );
            if ($geo_rule->id) {
                $fields[] = array(
                    'type' => 'hidden',
                    'name' => 'id_rule',
                    'default' => $geo_rule->id,
                );
            }
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => Tools::getValue('id_rule') ? $this->l('Edit rule') : $this->l('Add rule')
                    ),
                    'name' => 'rules',
                    'submit' => array(
                        'title' => $this->l('Save'),
                    ),
                    'buttons' => array(
                        'back' => array(
                            'href' => $this->getAdminLink() . '&control=rules&list=true',
                            'title' => $this->l('Back to list'),
                            'icon' => 'process-icon-back',
                        )
                    ),
                    'input' => $fields,
                ),
            );
            $helper = new HelperForm();
            $helper->show_toolbar = false;
            $helper->table = $this->table;
            $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
            $helper->default_form_language = $lang->id;
            $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
            $helper->module = $this;
            $helper->identifier = $this->identifier;
            $helper->submit_action = 'saveRules';
            $helper->currentIndex = $this->getAdminLink(array('token' => false, 'control' => 'rules')) . (Tools::isSubmit('updateets_geo_rule') ? '&updateets_geo_rule' : '') . (Tools::isSubmit('addets_geo_rule') ? '&addets_geo_rule' : '');
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
            $helper->tpl_vars = array(
                'base_url' => $this->context->shop->getBaseURL(),
                'language' => array('id_lang' => $language->id, 'iso_code' => $language->iso_code),
                'PS_ALLOW_ACCENTED_CHARS_URL' => (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
                'fields_value' => $this->getFieldsValues($fields, $geo_rule, $helper->submit_action),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            );
            $helper->override_folder = '/';
            $this->_html .= $helper->generateForm(array($fields_form));
        } else {
            $this->getFieldList();
            return $this->_html .= $this->renderList(array(
                'fields_list' => $this->fields_list,
                'title' => $this->l('Rules'),
                'actions' => array('edit', 'delete'),
                'orderBy' => 'priority',
                'orderWay' => 'DESC',
                'model' => 'Geo_rules',
                'no_link' => true,
                'bulk_actions' => false,
                'list_id' => 'ets_geo_rule'
            ));
        }
    }

    public function getFieldsValues($configs, $obj, $submit)
    {
        $fields = array();
        $languages = Language::getLanguages(false);
        if (Tools::isSubmit($submit)) {
            if ($configs) {
                foreach ($configs as $config) {
                    $key = $config['name'];
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang'], (isset($config['default']) ? $config['default'] : ''));
                        }
                    } elseif ($config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                        $fields[$key . ($config['type'] == 'select' ? '[]' : '')] = Tools::getValue($key, array());
                    } elseif (isset($config['type']) && $config['type'] == 'geo_countries') {
                        $fields[$key] = ($all_countries = (int)Tools::getValue('all_countries')) ? array() : Tools::getValue($key);
                        $fields['all_countries'] = $all_countries;
                    } else
                        $fields[$key] = Tools::getValue($key, (isset($config['default']) ? $config['default'] : ''));
                }
            }
        } else {
            if ($configs) {
                foreach ($configs as $config) {
                    $key = $config['name'];
                    if ($config['type'] == 'checkbox') {
                        $fields[$key] = $obj->id ? explode(',', $obj->$key) : (isset($config['default']) ? $config['default'] : array());
                    } elseif (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $l) {
                            $values = $obj->$key;
                            $fields[$key][$l['id_lang']] = $obj->id ? $values[$l['id_lang']] : (isset($config['default']) ? $config['default'] : '');
                        }
                    } elseif (isset($config['type']) && $config['type'] == 'geo_countries') {
                        $fields[$key] = $obj->all_countries ? array() : $obj->getCountries();
                        $fields['all_countries'] = $obj->all_countries;
                    } else {
                        $fields[$key] = $obj->id && property_exists($obj, $key) ? $obj->$key : (isset($config['default']) ? $config['default'] : null);
                    }
                }
            }
        }
        return $fields;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin-all16.css', 'all');
        if (Tools::isSubmit('configure') && Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS('https://fonts.googleapis.com/css?family=Poppins:400,600,700,900', 'all');
            $this->context->controller->addJquery();
            if (Tools::isSubmit('control') && Tools::getValue('control') == 'statistics') {
                $this->context->controller->addJqueryUI('ui.datepicker');
                $this->context->controller->addCSS($this->_path . 'views/css/jquery-jvectormap.css');
                $this->context->controller->addJS($this->_path . 'views/js/jquery-jvectormap.js');
                $this->context->controller->addJS($this->_path . 'views/js/Chart.min.js');
                $this->context->controller->addJS($this->_path . 'views/js/utils.js');
                $this->context->controller->addJS($this->_path . 'views/js/chartjs-plugin-labels.min.js');
            }

            $this->context->controller->addCSS($this->_path . 'views/css/admin_all.css');
            $this->context->controller->addCSS($this->_path . 'views/css/other.css');
            $this->context->controller->addJS($this->_path . 'views/js/other.js');


            $this->context->smarty->assign(array(
                'control' => Tools::getValue('control')
            ));
            if (!$this->is17) {
                $this->context->controller->addCSS($this->_path . 'views/css/admin16.css');
            }
        }
    }

    protected function filterToField($key, $filter)
    {
        if (!isset($this->fields_list) || !$this->fields_list)
            $this->fields_list = $this->getFieldList();

        foreach ($this->fields_list as $field)
            if (array_key_exists('filter_key', $field) && $field['filter_key'] == $key)
                return $field;
        if (array_key_exists($filter, $this->fields_list))
            return $this->fields_list[$filter];
        return false;
    }

    public function install()
    {
        $this->defines(false);
        return parent::install()
            && $this->_registerHook()
            && $this->_installDb()
            && $this->_installConfigs()
            && $this->_installTabs();
    }

    public function _registerHook()
    {
        return $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook(!$this->is17 ? 'displayNav' : 'displayNav1')
            && $this->registerHook('displayHeader')
            && $this->registerHook('taxManager')
            && $this->registerHook('actionTaxManager')
            && $this->registerHook('actionObjectCartAddBefore');
    }

    public function hookActionTaxManager($params)
    {
        return $this->hookTaxManager($params);
    }

    public function hookTaxManager($params)
    {
        if ((!isset($params['address']) || !$params['address']->id) && isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country > 0) {
            $id_country = Db::getInstance()->getValue('SELECT iso_code FROM ' . _DB_PREFIX_ . 'country WHERE iso_code=\'' . pSQL($this->context->cookie->iso_code_country) . '\'');
            $params['address']->id_country = $id_country;
            return new TaxRulesTaxManager($params['address'], $params['type']);
        }
    }

    public function _installDb()
    {
        return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_geo_rule` (
                    `id_rule` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `id_shop` int(11) NOT NULL,
                    `enabled` tinyint(1)unsigned NOT NULL DEFAULT \'1\',
                    `disable_geo` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                    `lang_to_set` int(11) unsigned NOT NULL DEFAULT \'0\',
                    `currency_to_set` int(11) unsigned NOT NULL DEFAULT \'0\',
                    `block_user` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                    `all_countries` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',
                    `url_redirect` varchar(255) NOT NULL,
                    `priority` varchar(50) NOT NULL DEFAULT \'1\',
                    PRIMARY KEY (`id_rule`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                CREATE INDEX idx_id_shop ON `' . _DB_PREFIX_ . 'ets_geo_rule` (id_shop);
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_geo_country_rule`(
                    `id_rule` int(11) unsigned NOT NULL DEFAULT \'0\',
                    `id_country_rule` int(11) unsigned NOT NULL DEFAULT \'0\',
                    PRIMARY KEY (`id_rule`,`id_country_rule`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_geo_visit`(
                    `id_country` int(11) unsigned NOT NULL DEFAULT \'0\',
                    `day` int(2) unsigned NOT NULL DEFAULT \'0\',
                    `month` int(2) unsigned NOT NULL DEFAULT \'0\',
                    `year` int(4) unsigned NOT NULL DEFAULT \'0\',
                    `visit` int(11) unsigned NOT NULL DEFAULT \'0\',
                    `last_ip` varchar(50) NULL,
                    `last_visit_time` datetime NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_geo_visit_day`(
                    `day` int(2) unsigned NOT NULL DEFAULT \'0\',
                    `month` int(2) unsigned NOT NULL DEFAULT \'0\',
                    `year` int(4) unsigned NOT NULL DEFAULT \'0\',
                    `ip_visit` varchar(50) NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_geo_address_detected`(
                    `id_address` int(4) unsigned NOT NULL,
                    `iso_code_country` varchar(4) NOT NULL,
                    PRIMARY KEY (`id_address`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                CREATE INDEX idx_iso_code_country ON `' . _DB_PREFIX_ . 'ets_geo_address_detected` (iso_code_country);
            ')
            && Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_geo_currency`(
                    `iso_code_country` varchar(4) NOT NULL,
                    `iso_code_currency` varchar(4) NOT NULL,
                    PRIMARY KEY (`iso_code_country`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ')
            && Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_geo_currency` VALUES("NZ", "NZD"),("CK", "NZD"),("NU", "NZD"),("PN", "NZD"),("TK", "NZD"),("AU", "AUD"),("CX", "AUD")
                ,("CC", "AUD"),("HM", "AUD"),("KI", "AUD"),("NR", "AUD"),("NF", "AUD"),("TV", "AUD"),("AS", "EUR"),("AD", "EUR"),("AT", "EUR"),("BE", "EUR"),("FI", "EUR")
                ,("FR", "EUR"),("GF", "EUR"),("TF", "EUR"),("DE", "EUR"),("GR", "EUR"),("GP", "EUR"),("IE", "EUR"),("IT", "EUR"),("LU", "EUR"),("MQ", "EUR"),("YT", "EUR")
                ,("MC", "EUR"),("NL", "EUR"),("PT", "EUR"),("RE", "EUR"),("WS", "EUR"),("SM", "EUR"),("SI", "EUR"),("ES", "EUR"),("VA", "EUR"),("GS", "GBP"),("GB", "GBP")
                ,("JE", "GBP"),("IO", "USD"),("GU", "USD"),("MH", "USD"),("FM", "USD"),("MP", "USD"),("PW", "USD"),("PR", "USD"),("TC", "USD"),("US", "USD"),("UM", "USD")
                ,("VG", "USD"),("VI", "USD"),("HK", "HKD"),("CA", "CAD"),("JP", "JPY"),("AF", "AFN"),("AL", "ALL"),("DZ", "DZD"),("AI", "XCD"),("AG", "XCD"),("DM", "XCD")
                ,("GD", "XCD"),("MS", "XCD"),("KN", "XCD"),("LC", "XCD"),("VC", "XCD"),("AR", "ARS"),("AM", "AMD"),("AW", "ANG"),("AN", "ANG"),("AZ", "AZN"),("BS", "BSD")
                ,("BH", "BHD"),("BD", "BDT"),("BB", "BBD"),("BY", "BYR"),("BZ", "BZD"),("BJ", "XOF"),("BF", "XOF"),("GW", "XOF"),("CI", "XOF"),("ML", "XOF"),("NE", "XOF")
                ,("SN", "XOF"),("TG", "XOF"),("BM", "BMD"),("BT", "INR"),("IN", "INR"),("BO", "BOB"),("BW", "BWP"),("BV", "NOK"),("NO", "NOK"),("SJ", "NOK"),("BR", "BRL")
                ,("BN", "BND"),("BG", "BGN"),("BI", "BIF"),("KH", "KHR"),("CM", "XAF"),("CF", "XAF"),("TD", "XAF"),("CG", "XAF"),("GQ", "XAF"),("GA", "XAF"),("CV", "CVE")
                ,("KY", "KYD"),("CL", "CLP"),("CN", "CNY"),("CO", "COP"),("KM", "KMF"),("CD", "CDF"),("CR", "CRC"),("HR", "HRK"),("CU", "CUP"),("CY", "CYP"),("CZ", "CZK")
                ,("DK", "DKK"),("FO", "DKK"),("GL", "DKK"),("DJ", "DJF"),("DO", "DOP"),("TP", "IDR"),("ID", "IDR"),("EC", "ECS"),("EG", "EGP"),("SV", "SVC"),("ER", "ETB")
                ,("ET", "ETB"),("EE", "EEK"),("FK", "FKP"),("FJ", "FJD"),("PF", "XPF"),("NC", "XPF"),("WF", "XPF"),("GM", "GMD"),("GE", "GEL"),("GI", "GIP"),("GT", "GTQ")
                ,("GN", "GNF"),("GY", "GYD"),("HT", "HTG"),("HN", "HNL"),("HU", "HUF"),("IS", "ISK"),("IR", "IRR"),("IQ", "IQD"),("IL", "ILS"),("JM", "JMD"),("JO", "JOD")
                ,("KZ", "KZT"),("KE", "KES"),("KP", "KPW"),("KR", "KRW"),("KW", "KWD"),("KG", "KGS"),("LA", "LAK"),("LV", "LVL"),("LB", "LBP"),("LS", "LSL"),("LR", "LRD")
                ,("LY", "LYD"),("LI", "CHF"),("CH", "CHF"),("LT", "LTL"),("MO", "MOP"),("MK", "MKD"),("MG", "MGA"),("MW", "MWK"),("MY", "MYR"),("MV", "MVR"),("MT", "MTL")
                ,("MR", "MRO"),("MU", "MUR"),("MX", "MXN"),("MD", "MDL"),("MN", "MNT"),("MA", "MAD"),("EH", "MAD"),("MZ", "MZN"),("MM", "MMK"),("NA", "NAD"),("NP", "NPR")
                ,("NI", "NIO"),("NG", "NGN"),("OM", "OMR"),("PK", "PKR"),("PA", "PAB"),("PG", "PGK"),("PY", "PYG"),("PE", "PEN"),("PH", "PHP"),("PL", "PLN"),("QA", "QAR")
                ,("RO", "RON"),("RU", "RUB"),("RW", "RWF"),("ST", "STD"),("SA", "SAR"),("SC", "SCR"),("SL", "SLL"),("SG", "SGD"),("SK", "SKK"),("SB", "SBD"),("SO", "SOS")
                ,("ZA", "ZAR"),("LK", "LKR"),("SD", "SDG"),("SR", "SRD"),("SZ", "SZL"),("SE", "SEK"),("SY", "SYP"),("TW", "TWD"),("TJ", "TJS"),("TZ", "TZS"),("TH", "THB")
                ,("TO", "TOP"),("TT", "TTD"),("TN", "TND"),("TR", "TRY"),("TM", "TMT"),("UG", "UGX"),("UA", "UAH"),("AE", "AED"),("UY", "UYU"),("UZ", "UZS"),("VU", "VUV")
                ,("VE", "VEF"),("VN", "VND"),("YE", "YER"),("ZM", "ZMK"),("ZW", "ZWD"),("AX", "EUR"),("AO", "AOA"),("AQ", "AQD"),("BA", "BAM"),("GH", "GHS")
                ,("GG", "GGP"),("IM", "GBP"),("ME", "EUR"),("PS", "JOD"),("BL", "EUR"),("SH", "GBP"),("MF", "ANG"),("PM", "EUR"),("RS", "RSD")
                ,("USAF", "USD");
            ');
    }

    public function _installTabs()
    {
        $languages = Language::getLanguages(false);
        $tab = new Tab();
        $tab->class_name = 'AdminGeolocationParent';
        $tab->module = $this->name;
        $tab->id_parent = 0;
        foreach ($languages as $l) {
            $tab->name[(int)$l['id_lang']] = $this->l('Geolocation');
        }
        $tab->add();
        if ($tab->id && $this->quickTabs) {
            foreach ($this->quickTabs as $t) {
                $child = new Tab();
                $child->class_name = $t['class_name'];
                $child->module = $this->name;
                $child->id_parent = (int)$tab->id;
                $child->icon = $t['icon'];
                foreach ($languages as $l) {
                    $child->name[(int)$l['id_lang']] = $t['tab_name'];
                }
                $child->add();
            }
        }
        return true;
    }

    public function _uninstallTab()
    {
        $Id = Tab::getIdFromClassName('AdminGeolocationParent');
        $tab = new Tab($Id);
        if ($tab->delete() && $this->quickTabs) {
            foreach ($this->quickTabs as $t) {
                if ($tabId = Tab::getIdFromClassName($t['class_name'])) {
                    $stab = new Tab($tabId);
                    $stab->delete();
                }
            }
        }
        return true;
    }

    private function _uninstallDb()
    {
        return
            true && Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'address` WHERE id_address IN (SELECT id_address FROM `' . _DB_PREFIX_ . 'ets_geo_address_detected`)')
            && Db::getInstance()->execute("DROP TABLE IF EXISTS 
			    `" . _DB_PREFIX_ . "ets_geo_rule`,
				`" . _DB_PREFIX_ . "ets_geo_country_rule`,
				`" . _DB_PREFIX_ . "ets_geo_visit`,
				`" . _DB_PREFIX_ . "ets_geo_visit_day`,
				`" . _DB_PREFIX_ . "ets_geo_currency`,
				`" . _DB_PREFIX_ . "ets_geo_address_detected`");
    }

    public function _installConfigs($upgrade = false)
    {
        $languages = Language::getLanguages(false);
        if ($this->configTabs) {
            foreach ($this->configTabs as $configTab) {
                if (isset($configTab['subTabs']) && ($subTabs = $configTab['subTabs'])) {
                    foreach ($subTabs as $TAB)
                        $this->installTabConfig($TAB, $languages, $upgrade);
                } else
                    $this->installTabConfig($configTab, $languages, $upgrade);
            }
        }
        return true;
    }

    private function installTabConfig($TAB, $languages, $upgrade = false)
    {
        if ($TAB['value'] == 'configs_messages') {
            require_once dirname(__FILE__) . '/classes/Geo_trans.php';
        }
        if (!$languages) {
            $languages = Language::getLanguages(false);
        }
        if ($TAB) {
            if ((isset($TAB['is_conf']) && $TAB['is_conf']) && (isset($TAB['value']) && ($configs = $this->{$TAB['value']}))) {
                foreach ($configs as $key => $config) {
                    if (isset($config['lang']) && $config['lang']) {
                        $values = array();
                        foreach ($languages as $lang) {
                            if ($TAB['value'] !== 'configs_messages') {
                                $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                            } else {
                                $values[$lang['id_lang']] = isset(Geo_trans::$trans[$key][$lang['iso_code']]) && ($res = Geo_trans::$trans[$key][$lang['iso_code']]) ? $this->sprintf($res, 'strong') : (isset($config['default']) ? $config['default'] : '');
                            }
                        }
                        if ($upgrade && !Configuration::hasKey($key) || !$upgrade) {
                            Configuration::updateValue($key, $values, true);
                        }
                    } elseif ($upgrade && !Configuration::hasKey($key) || !$upgrade) {
                        Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '', true);
                    }
                }
            }
        }
    }

    public function uninstall()
    {
        $this->defines(false);
        return parent::uninstall() && $this->_uninstallConfigs() && $this->_uninstallDb() && $this->_uninstallTab();
    }

    private function _uninstallConfigs()
    {
        if ($this->configTabs) {
            foreach ($this->configTabs as $configTab) {
                if (isset($configTab['subTabs']) && ($subTabs = $configTab['subTabs'])) {
                    foreach ($subTabs as $TAB)
                        $this->uninstallTabConfig($TAB);
                    unset($TAB);
                } else
                    $this->uninstallTabConfig($configTab);
            }
        }
        return true;
    }

    private function uninstallTabConfig($TAB)
    {
        if ($TAB) {
            if ((isset($TAB['is_conf']) && $TAB['is_conf']) && (isset($TAB['value']) && ($configs = $this->{$TAB['value']}))) {
                foreach ($configs as $key => $config) {
                    if ($key != 'PS_GEOLOCATION_ENABLED'
                        && $key != 'PS_LANG_DEFAULT'
                        && $key != 'PS_CURRENCY_DEFAULT'
                        && $key != 'PS_COUNTRY_DEFAULT'
                    ) {
                        Configuration::deleteByName($key);
                    }
                }
                unset($config);
            }
        }
    }

    public function displayError($error)
    {
        if ($error) {
            $this->context->smarty->assign(array('errors_blog' => $error));
            return $this->display(__file__, 'errors.tpl');
        }
        return '';
    }

    public function displaySuccessMessage($msg, $title = false, $link = false)
    {
        $this->smarty->assign(array(
            'msg' => $msg,
            'title' => $title,
            'link' => $link
        ));
        if ($msg)
            return $this->displayConfirmation($this->display(__FILE__, 'success_message.tpl'));
    }

    public function getNameLangByIdLang($id_lang, $name_from = null)
    {
        if (!$id_lang) {
            return false;
        }
        $sql_name_full = 'SELECT l.`name` 
                            FROM `' . _DB_PREFIX_ . 'lang` l 
                            LEFT JOIN `' . _DB_PREFIX_ . 'lang_shop` ls ON (l.`id_lang` = ls.`id_lang` AND ls.`id_shop`= ' . (int)$this->context->shop->id . ' ) 
                            WHERE l.`id_lang` = ' . (int)$id_lang . ' AND l.`active`=1 ';
        if (!$name_full = Db::getInstance()->getRow($sql_name_full)) {
            return false;
        }
        $name_full = explode('(', $name_full['name']);
        if ($name_from) {
            return $name_full[0];
        }
        $name = str_replace(')', '', $name_full[1]);
        return $name;
    }

    public function getNameCurrencyById($idCurrency)
    {
        if (!$idCurrency) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('c.`iso_code`');
        $sql->from('currency', 'c');
        $sql->where('`deleted` = 0');
        $sql->where('`id_currency` = ' . (int)$idCurrency);

        if ($res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            return $res['iso_code'];
        }
        return '';
    }

    public function showSwitchNav()
    {
        $firstAddress = NULL;
        if ($this->context->customer->isLogged()) {
            $firstAddress = Address::getFirstCustomerAddressId($this->context->customer->id);
            if ($firstAddress)
                $idCountry = Db::getInstance()->getValue('SELECT id_country FROM ' . _DB_PREFIX_ . 'address WHERE id_address=' . (int)$firstAddress . ' ');
        }
        if (in_array(Tools::getRemoteAddr(), array('localhost', '127.0.0.1', '::1'))
            || !$this->isGeoLiteCityAvailable()
            || !(int)Configuration::get('PS_GEOLOCATION_ENABLED')
            || !Configuration::get('ETS_GEO_ENABLE_SWITCH')
            || !$this->ets_getCountriesNotBlock($this->context->language->id)
            || (isset($idCountry) && $this->checkFirstAddress($idCountry))
            || (($geo_rule = Geo_rules::getRulesByIdCountry()) && isset($geo_rule['disable_geo']) && $geo_rule['disable_geo'])
        ) {
            return false;
        }
        $checkDisable = false;

        if (Db::getInstance()->getValue('SELECT `id_country_rule` FROM `' . _DB_PREFIX_ . 'ets_geo_country_rule`')
            && !Db::getInstance()->getValue('SELECT `id_country_rule`
                         FROM `' . _DB_PREFIX_ . 'ets_geo_country_rule` as gcrl 
                         JOIN `' . _DB_PREFIX_ . 'ets_geo_rule` grl ON (grl.`id_rule` = gcrl.`id_rule`)
                         WHERE `block_user`= 0 OR `disable_geo` = 0 
                        GROUP BY id_country_rule ')) {
            $checkDisable = true;
        }

        $this->smarty->assign(
            array(
                'geo_country' => new Country($this->context->country->id, $this->context->language->id),
                'is17' => $this->is17,
                'firdAddress' => ($firstAddress || $checkDisable) ? true : false,
            ));
        return $this->display(__FILE__, 'switching_button.tpl');
    }


    public function hookDisplayNav()
    {
        return $this->showSwitchNav();
    }

    public function hookDisplayNav1()
    {
        return $this->showSwitchNav();
    }

    public function hookDisplayTop()
    {
        return $this->showSwitchNav();
    }

    public function hookDisplayHeader()
    {
        if ((int)Configuration::get('ETS_GEO_IGNORE_BOTS') && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/BotLink|ahoy|AlkalineBOT|anthill|appie|arale|araneo|AraybOt|ariadne|arks|ATN_Worldwide|Atomz|bbot|Bjaaland|Ukonline|borg\-bot\/0\.9|boxseabot|bspider|calif|christcrawler|CMC\/0\.01|combine|confuzzledbot|CoolBot|cosmos|Internet Cruiser Robot|cusco|cyberspyder|cydralspider|desertrealm, desert realm|digger|DIIbot|grabber|downloadexpress|DragonBot|dwcp|ecollector|ebiness|elfinbot|esculapio|esther|fastcrawler|FDSE|FELIX IDE|ESI|fido|H�m�h�kki|KIT\-Fireball|fouineur|Freecrawl|gammaSpider|gazz|gcreep|golem|googlebot|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|iajabot|INGRID\/0\.1|Informant|InfoSpiders|inspectorwww|irobot|Iron33|JBot|jcrawler|Teoma|Jeeves|jobo|image\.kapsi\.net|KDD\-Explorer|ko_yappo_robot|label\-grabber|larbin|legs|Linkidator|linkwalker|Lockon|logo_gif_crawler|marvin|mattie|mediafox|MerzScope|NEC\-MeshExplorer|MindCrawler|udmsearch|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|sharp\-info\-agent|WebMechanic|NetScoop|newscan\-online|ObjectsSearch|Occam|Orbsearch\/1\.0|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|Getterrobo\-Plus|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Search\-AU|searchprocess|Senrigan|Shagseeker|sift|SimBot|Site Valet|skymob|SLCrawler\/2\.0|slurp|ESI|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|nil|suke|http:\/\/www\.sygol\.com|tach_bw|TechBOT|templeton|titin|topiclink|UdmSearch|urlck|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|crawlpaper|wapspider|WebBandit\/1\.0|webcatcher|T\-H\-U\-N\-D\-E\-R\-S\-T\-O\-N\-E|WebMoose|webquest|webreaper|webs|webspider|WebWalker|wget|winona|whowhere|wlm|WOLP|WWWC|none|XGET|Nederland\.zoek|AISearchBot|woriobot|NetSeer|Nutch|YandexBot/i', $_SERVER['HTTP_USER_AGENT'])) {
            return '';
        }
        if (!$this->isGeoLiteCityAvailable() || !(int)Configuration::get('PS_GEOLOCATION_ENABLED')) {
            return;
        }

        $select_country = (!$this->context->customer->isLogged() || !Address::getFirstCustomerAddressId($this->context->customer->id)) && Configuration::get('ETS_GEO_ENABLE_SWITCH');
        $this->context->controller->addCSS($this->_path . 'views/css/front_geo.css', 'all');
        if (!$this->context->cookie->ets_geocountryloaded || $select_country) {
            if (($dispatcher = Dispatcher::getInstance()->getController($this->context->shop->id)) != 'process') {
                $this->context->cookie->page_controller = trim($dispatcher);
                $this->getAlternativeLangsUrl();
            }

            $this->context->controller->addJS($this->_path . 'views/js/front_geo.js');

            if ($select_country) {
                $this->context->controller->addCSS($this->_path . 'views/css/chosen.min.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/chosen.jquery.js');
            }

            $this->smarty->assign(array(
                'ajax_url' => $this->context->link->getModuleLink('ets_geolocation', 'process', array(), Tools::usingSecureMode()),
                'popup_is_load' => isset($this->context->cookie->ets_geocountryloaded) ? true : false,
                'page_controller' => $this->context->cookie->page_controller,
            ));
            return $this->display(__FILE__, 'front_header.tpl');
        }
    }

    public function getAlternativeLangsUrl()
    {
        $alternativeLangs = array();
        $languages = Language::getLanguages(true, $this->context->shop->id);
        if ($languages < 2) {
            return $alternativeLangs;
        }
        foreach ($languages as $lang) {
            $alternativeLangs[$lang['iso_code']] = $this->context->link->getLanguageLink($lang['id_lang']);
        }
        $this->smarty->assign(array(
            'lang_links' => $alternativeLangs
        ));
    }

    public function getDateFormat()
    {
        $format = Context::getContext()->language->date_format_lite;
        $search = array('d', 'm', 'Y');
        $replace = array('DD', 'MM', 'YYYY');
        $format = str_replace($search, $replace, $format);
        return $format;
    }

    public function isGeoLiteCityAvailable()
    {
        if (@filemtime(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_)) {
            return true;
        }
        return false;
    }

    public function getBaseLink()
    {
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $this->context->shop->domain . $this->context->shop->getBaseURI();
    }

    public function renderList($params)
    {
        if (!$params)
            return $this->_html;
        $fields_list = $params['fields_list'];
        $this->initToolbar();
        $helper = new HelperList();
        $helper->title = $params['title'];
        $helper->table = 'ets_geo_rule';
        $helper->identifier = 'id_rule';
        if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            $helper->_pagination = array(10, 50, 100, 300, 1000);
            $helper->_default_pagination = 10;
        }
        $helper->_defaultOrderBy = isset($params['orderBy']) && $params['orderBy'] ? $params['orderBy'] : '';
        $helper->lang = true;
        $helper->explicitSelect = true;
        if (isset($params['orderBy']) && $params['orderBy'] == 'position') {
            $helper->position_identifier = 'position';
        }
        $this->processFilter($params);
        //Sort order
        $order_by = urldecode(Tools::getValue($this->list_id . 'Orderby'));
        if (!$order_by) {
            if ($this->context->cookie->{$this->list_id . 'Orderby'}) {
                $order_by = $this->context->cookie->{$this->list_id . 'Orderby'};
            } elseif ($helper->orderBy) {
                $order_by = $helper->orderBy;
            } else {
                $order_by = $helper->_defaultOrderBy;
            }
        }
        $order_way = urldecode(Tools::getValue($this->list_id . 'Orderway'));
        if (!$order_way) {
            if ($this->context->cookie->{$this->list_id . 'Orderway'}) {
                $order_way = $this->context->cookie->{$this->list_id . 'Orderway'};
            } elseif ($helper->orderWay) {
                $order_way = $helper->orderWay;
            } else {
                $order_way = $params['orderWay'];
            }
        }
        if (isset($this->fields_list[$order_by]) && isset($this->fields_list[$order_by]['filter_key']))
            $order_by = $this->fields_list[$order_by]['filter_key'];
        //Pagination.
        $limit = Tools::getValue($helper->table . '_pagination');
        if (!$limit) {
            if (isset($this->context->cookie->{$this->list_id . '_pagination'}) && $this->context->cookie->{$this->list_id . '_pagination'})
                $limit = $this->context->cookie->{$this->list_id . '_pagination'};
            else
                $limit = (version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? $helper->_default_pagination : 20);
        }
        if ($limit) {
            $this->context->cookie->{$this->list_id . '_pagination'} = $limit;
        } else {
            unset($this->context->cookie->{$this->list_id . '_pagination'});
        }
        $start = 0;
        if ((int)Tools::getValue('submitFilter' . $this->list_id)) {
            $start = ((int)Tools::getValue('submitFilter' . $this->list_id) - 1) * $limit;
        } elseif (isset($this->context->cookie->{$this->list_id . '_start'}) && Tools::isSubmit('export' . $helper->table)) {
            $start = $this->context->cookie->{$this->list_id . '_start'};
        }
        if ($start) {
            $this->context->cookie->{$this->list_id . '_start'} = $start;
        } elseif (isset($this->context->cookie->{$this->list_id . '_start'})) {
            unset($this->context->cookie->{$this->list_id . '_start'});
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way) || !is_numeric($start) || !is_numeric($limit)) {
            $this->errorMessage = Tools::displayError($this->l('get list params is not valid'));
        }
        $helper->orderBy = $order_by;
        if (preg_match('/[.!]/', $order_by)) {
            $order_by_split = preg_split('/[.!]/', $order_by);
            $order_by = bqSQL($order_by_split[0]) . '.`' . bqSQL($order_by_split[1]) . '`';
        } elseif ($order_by) {
            $order_by = '`' . bqSQL($order_by) . '`';
        }
        if (!$model = isset($params['model']) ? $params['model'] : false) {
            return false;
        }
        $helper->listTotal = $model::getLists(array(
            'nb' => true,
            'filter' => $this->_filter,
            'having' => $this->_filterHaving,
        ));
        $list = $model::getLists(array(
            'filter' => $this->_filter,
            'having' => $this->_filterHaving,
            'start' => $start,
            'limit' => $limit,
            'sort' => $order_by . ' ' . Tools::strtoupper($order_way),
        ));

        $helper->orderWay = Tools::strtoupper($order_way);
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->shopLinkType = '';
        $helper->row_hover = true;
        $helper->no_link = $params['no_link'];
        $helper->simple_header = false;
        $helper->actions = $params['actions'];
        $helper->show_toolbar = true;
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->getAdminLink(array('token' => false)) . '&control=rules';
        $helper->bulk_actions = $params['bulk_actions'] ? array(
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success'
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger'
            ),
            'divider' => array(
                'text' => 'divider'
            ),
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        ) : false;

        $this->_html .= $helper->generateList($list, $fields_list);
    }

    public function processFilter($params)
    {
        if (empty($params))
            return false;
        if (!isset($this->list_id)) {
            $this->list_id = 'ets_geo_rule';
        }
        $prefix = null;
        // Filter memorization
        if (!empty($_POST) && isset($this->list_id)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$prefix . $key});
                } elseif (stripos($key, $this->list_id . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : serialize($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
                }
            }
        }
        if (!empty($_GET) && isset($this->list_id)) {
            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->list_id . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : serialize($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
                }
                if (stripos($key, $this->list_id . 'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $params['orderBy']) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                } elseif (stripos($key, $this->list_id . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $params['orderWay']) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                }
            }
        }

        $filters = $this->context->cookie->getFamily($prefix . $this->list_id . 'Filter_');

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp($key, $prefix . $this->list_id . 'Filter_', 7 + Tools::strlen($prefix . $this->list_id))) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix . $this->list_id));
                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

                if ($field = $this->filterToField($key, $filter)) {
                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime') && is_string($value))
                        $value = Tools::unSerialize($value);
                    $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';
                    $sql_filter = '';
                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->_errors[] = Tools::displayError('The \'From\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->_errors[] = Tools::displayError('The \'To\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' <= \'' . pSQL(Tools::dateTo($value[1])) . '\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == 'id_rule' || $key == '`id_rule`');
                        $alias = 'rl';

                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ? $alias . '.' : '') . pSQL($key) . ' = ' . (int)$value . ' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . (float)$value . ' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = \'' . pSQL($value) . '\' ';
                        } elseif ($type == 'price') {
                            $value = (float)str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . pSQL(trim($value)) . ' ';
                        } else {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' LIKE \'%' . pSQL(trim($value)) . '%\' ';
                        }
                    }
                    if (isset($field['havingFilter']) && $field['havingFilter'])
                        $this->_filterHaving .= $sql_filter;
                    else
                        $this->_filter .= $sql_filter;
                }
            }
        }
    }

    public function initToolbar()
    {
        $this->toolbar_btn['new'] = array(
            'short' => 'Add RULE',
            'href' => $this->getAdminLink() . '&control=rules&addets_geo_rule',
            'desc' => $this->l('Add Rule')
        );
    }

    public function processResetFilters($list_id = 'ets_geo_rule')
    {
        if ($list_id === null) {
            $list_id = isset($this->list_id) ? $this->list_id : $this->name;
        }
        $prefix = null;
        $filters = $this->context->cookie->getFamily($prefix . $list_id . 'Filter_');
        if (!empty($filters))
            foreach ($filters as $cookie_key => $filter) {
                if (strncmp($cookie_key, $prefix . $list_id . 'Filter_', 7 + Tools::strlen($prefix . $list_id)) == 0) {
                    $key = Tools::substr($cookie_key, 7 + Tools::strlen($prefix . $list_id));
                    if (is_array($this->fields_list) && array_key_exists($key, $this->fields_list)) {
                        $this->context->cookie->$cookie_key = null;
                    }
                    unset($this->context->cookie->$cookie_key, $filter);
                }
            }

        if (isset($this->context->cookie->{'submitFilter' . $list_id})) {
            unset($this->context->cookie->{'submitFilter' . $list_id});
        }
        if (isset($this->context->cookie->{$prefix . $list_id . 'Orderby'})) {
            unset($this->context->cookie->{$prefix . $list_id . 'Orderby'});
        }
        if (isset($this->context->cookie->{$prefix . $list_id . 'Orderway'})) {
            unset($this->context->cookie->{$prefix . $list_id . 'Orderway'});
        }

        $_POST = array();
    }

    public function addAddress($id_country)
    {
        if (!$id_country) {
            return false;
        }
        $res = Db::getInstance()->execute("
            INSERT INTO `" . _DB_PREFIX_ . "address` (`id_country`, `id_state`, `id_customer`, `id_manufacturer`, `id_supplier`, `id_warehouse`, `alias`, `company`, `lastname`, `firstname`, `address1`, `address2`, `postcode`, `city`, `other`, `phone`, `phone_mobile`, `vat_number`, `dni`, `date_add`, `date_upd`, `active`, `deleted`) 
            VALUES (" . (int)$id_country . ", NULL, '', '', '', '', 'auto', '', 'auto', 'auto', 'auto', NULL, NULL, 'auto', '', NULL, NULL, NULL, NULL, NOW(), NOW(), '1', '0')
        ");
        return $res ? (int)Db::getInstance()->Insert_ID() : 0;
    }

    public function detectedAddress($iso_code_country)
    {
        if (Configuration::get('ETS_GEO_AUTO_TAX_SHIPPING') && $iso_code_country && isset($this->context->cart) && !$this->context->cart->id_customer) {
            if (!($id_address = (int)Db::getInstance()->getValue('
                    SELECT ad.id_address FROM ' . _DB_PREFIX_ . 'ets_geo_address_detected ad
                    INNER JOIN ' . _DB_PREFIX_ . 'address a ON (a.id_address = ad.id_address AND (a.id_customer is NULL OR a.id_customer = 0))
                    WHERE ad.iso_code_country = "' . pSQL($iso_code_country) . '"
                '))) {
                $id_address = (int)$this->addAddress(Country::getByIso($iso_code_country));
                if ($id_address) {
                    Db::getInstance()->execute("
                        INSERT INTO `" . _DB_PREFIX_ . "ets_geo_address_detected` (`id_address`, `iso_code_country`) 
                        VALUES (" . (int)$id_address . ", '" . pSQL($iso_code_country) . "')
                    ");
                }
            }
            if ($id_address) {
                $this->context->cart->id_address_invoice = $this->context->cart->id_address_delivery = $id_address;
                $this->context->cart->update();
            }
        }
    }

    /**
     * @param int|null $idShop
     * @param bool|null $ssl
     * @param bool $relativeProtocol
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     */
    public function getAdminBaseLink($idShop = null, $ssl = null, $relativeProtocol = false)
    {
        if (null === $ssl) {
            $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            if (null === $idShop) {
                $idShop = $this->getMatchingUrlShopId();
            }

            //Use the matching shop if present, or fallback on the default one
            if (null !== $idShop) {
                $shop = new Shop($idShop);
            } else {
                $shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
            }
        } else {
            $shop = Context::getContext()->shop;
        }

        if ($relativeProtocol) {
            $base = '//' . ($ssl && $this->ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $this->ssl_enable) ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain);
        }

        return $base . $shop->getBaseURI();
    }

    /**
     * Search for a shop whose domain matches the current url.
     *
     * @return int|null
     */
    public function getMatchingUrlShopId()
    {
        if (null === $this->urlShopId) {
            $host = Tools::getHttpHost();
            $request_uri = rawurldecode($_SERVER['REQUEST_URI']);

            $sql = 'SELECT s.id_shop, CONCAT(su.physical_uri, su.virtual_uri) AS uri, su.domain, su.main
                    FROM ' . _DB_PREFIX_ . 'shop_url su
                    LEFT JOIN ' . _DB_PREFIX_ . 'shop s ON (s.id_shop = su.id_shop)
                    WHERE (su.domain = \'' . pSQL($host) . '\' OR su.domain_ssl = \'' . pSQL($host) . '\')
                        AND s.active = 1
                        AND s.deleted = 0
                    ORDER BY LENGTH(CONCAT(su.physical_uri, su.virtual_uri)) DESC';

            try {
                $result = Db::getInstance()->executeS($sql);
            } catch (PrestaShopDatabaseException $e) {
                return null;
            }

            foreach ($result as $row) {
                // A shop matching current URL was found
                if (preg_match('#^' . preg_quote($row['uri'], '#') . '#i', $request_uri)) {
                    $this->urlShopId = $row['id_shop'];

                    break;
                }
            }
        }

        return $this->urlShopId;
    }

    public function getGeoIDLang($id_country = 0)
    {
        if ($id_country) {
            $iso_code = Db::getInstance()->getValue('SELECT iso_code FROM ' . _DB_PREFIX_ . 'country WHERE id_country=' . (int)$id_country);
        } else {
            $iso_code = $this->context->cookie->iso_code_country;
        }
        if ($iso_code) {
            $sql = '
                SELECT ls.id_lang
                FROM ' . _DB_PREFIX_ . 'lang_shop ls
                INNER JOIN ' . _DB_PREFIX_ . 'lang l ON ls.id_shop = ' . (int)$this->context->shop->id . ' AND ls.id_lang = l.id_lang
                WHERE l.iso_code = "' . pSQL(Tools::strtolower($iso_code)) . '"
            ';
            return (int)Db::getInstance()->getValue($sql);
        }
        return 0;
    }

    public function getCountries($idLang, $active = false, $containStates = false, $listStates = true, $idRule = false)
    {

        $sql = 'SELECT GROUP_CONCAT(DISTINCT `id_country_rule` SEPARATOR \',\') as idsCountry FROM `' . _DB_PREFIX_ . 'ets_geo_country_rule` cr
                 INNER JOIN `' . _DB_PREFIX_ . 'ets_geo_rule` r ON (cr.`id_rule` = r.`id_rule` AND id_shop = ' . (int)$this->context->shop->id . ') 
                  WHERE 1 ' . ($idRule ? ' AND r.`id_rule` != ' . (int)$idRule . ' ' : '') . ' ';
        $idsCountry = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        $countries = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT cl.*,c.*, cl.`name` country, z.`name` zone
		FROM `' . _DB_PREFIX_ . 'country` c ' . Shop::addSqlAssociation('country', 'c') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = ' . (int)$idLang . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = c.`id_zone`)
		WHERE 1' . ($active ? ' AND c.active = 1' : '') . ($containStates ? ' AND c.`contains_states` = ' . (int)$containStates : '') . '
		    ' . ($idsCountry && isset($idsCountry['idsCountry']) && $idsCountry['idsCountry'] ? ' AND c.`id_country` NOT IN (' . pSQL($idsCountry['idsCountry']) . ') ' : '') . '
		ORDER BY cl.name ASC');

        foreach ($result as $row) {
            $countries[$row['id_country']] = $row;
        }

        if ($listStates) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'state` ORDER BY `name` ASC');
            foreach ($result as $row) {
                if (isset($countries[$row['id_country']]) && $row['active'] == 1) { /* Does not keep the state if its country has been disabled and not selected */
                    $countries[$row['id_country']]['states'][] = $row;
                }
            }
        }

        return $countries;
    }

    public function checkFirstAddress($idCountry = false)
    {
        if (!$idCountry) return false;
        $sql = 'SELECT * 
                    FROM `' . _DB_PREFIX_ . 'ets_geo_rule` gr 
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_geo_country_rule` gcr ON (gr.id_rule = gcr.id_rule) 
                    WHERE 1 AND gcr.id_country_rule =' . (int)$idCountry . ' AND gr.block_user = 1 ';
        return (bool)Db::getInstance()->getValue($sql);
    }

    public function checkThisCountryDisable($idCountry = false)
    {
        if (!$idCountry) {
            $idCountry = $this->context->country->id;
        }
        return (bool)Db::getInstance()->getValue('SELECT active 
                                                        FROM `' . _DB_PREFIX_ . 'country` c ' . Shop::addSqlAssociation('country', 'c') . ' 
                                                        WHERE c.id_country = ' . (int)$idCountry . ' ');
    }
}