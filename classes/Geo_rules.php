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

class Geo_rules extends ObjectModel
{
    public $id_shop;
    public $enabled;
    public $countries;
    public $all_countries;
    public $disable_geo;
    public $lang_to_set;
    public $currency_to_set;
    public $block_user;
    public $priority;
    public $url_redirect;

    public static $definition = array(
        'table' => 'ets_geo_rule',
        'primary' => 'id_rule',
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'all_countries' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'disable_geo' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'lang_to_set' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'currency_to_set' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'block_user' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'url_redirect' => array('type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'),
            'priority' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
        ),
    );

    public function __construct($id_rule = null)
    {
        parent::__construct($id_rule);
        if ($this->id) {
            $this->countries = !$this->all_countries? $this->getCountries() : array();
        }
    }

    public function save($null_values = false, $auto_date = true)
    {
        if ($res = parent::save($null_values, $auto_date) && Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_geo_country_rule` WHERE `id_rule` = ' . (int)$this->id)) {
            $sql = null;
            if ($this->countries) {
                foreach ($this->countries as $id_country)
                    $sql .= '(' . (int)$this->id . ', ' . (int)$id_country . '),';
                $res &= ($sql ? Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_geo_country_rule` (`id_rule`,`id_country_rule`) VALUES ' . trim($sql, ',')) : true);
            }
        }
        return $res;
    }

    public function delete()
    {
        if ($res = parent::delete()) {
            $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_geo_country_rule` WHERE `id_rule` = ' . (int)$this->id);
        }
        return $res;
    }

    public static function getRulesByIdCountry($id_country = 0)
    {
        if (!$id_country) {
            $id_country = (int)Context::getContext()->country->id;
        }

        $sql = '
            SELECT r.*   
            FROM `' . _DB_PREFIX_ . 'ets_geo_rule` r 
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_geo_country_rule` cr ON (r.`id_rule` = cr.`id_rule` AND cr.`id_country_rule` = ' . (int)$id_country . ')  
            WHERE r.`enabled` = 1  AND (cr.id_rule is NOT NULL) AND r.id_shop = '.(int)Context::getContext()->shop->id.'
            GROUP BY r.`id_rule` 
            ORDER BY r.`priority`, r.`id_rule` ASC 
        ';

        return Db::getInstance()->getRow($sql);
    }

    public function getCountries()
    {
        if (!$res = Db::getInstance()->getValue('SELECT GROUP_CONCAT(`id_country_rule` SEPARATOR ",") FROM `' . _DB_PREFIX_ . 'ets_geo_country_rule` WHERE `id_rule` = ' . (int)$this->id)) {
            return array();
        }
        return explode(',', $res);
    }

    public static function getLists($params)
    {
        $is1760 = version_compare(_PS_VERSION_, '1.7.6', '>=');
        $context = Context::getContext();
        $sql = 'SELECT rl.* , crl.`id_country_rule`, IF(rl.all_countries != 1, GROUP_CONCAT(DISTINCT cl.name SEPARATOR ","), "'.self::l('All').'") as `countries`, IF(l.name is NOT NULL, l.name, "' . self::l('Auto') . '") as `lang_to_set`, IF('.($is1760 ? 'currency_lang.name' : 'currency.name').' is NOT NULL, '.($is1760 ? 'currency_lang.name' : 'currency.name').', "' . self::l('Auto') . '") as `currency_to_set`
                    FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` rl 
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_geo_country_rule` crl ON rl.`id_rule` = crl.`id_rule` 
                    LEFT JOIN `' . _DB_PREFIX_ . 'lang` l ON (l.id_lang = rl.lang_to_set)
                    LEFT JOIN `' . _DB_PREFIX_ . 'country` c ON (c.id_country = crl.id_country_rule)
                    LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.id_country = c.id_country AND cl.id_lang = ' . (int)$context->language->id . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'currency` currency  ON (currency.id_currency = rl.currency_to_set)
                    '.($is1760 ? ' LEFT JOIN `' . _DB_PREFIX_ . 'currency_lang` currency_lang  ON (currency.id_currency = currency_lang.id_currency) ' : '').'
                    WHERE (1 AND rl.`id_shop` ='.(int)$context->shop->id. ' AND crl.`id_rule` is NOT NULL OR rl.all_countries = 1) ' . (isset($params['filter']) && $params['filter'] ? $params['filter'] : '') . '';
        $sql .= ' GROUP BY rl.`id_rule` ';
        if (isset($params['nb']) && $params['nb'])
            return ($nb = Db::getInstance()->executeS($sql)) ? count($nb) : 0;
        $sql .= ' ORDER BY ' . (isset($params['sort']) && $params['sort'] ? $params['sort'] : 'priority')
            . ((isset($params['start']) && $params['start'] !== false) && (isset($params['limit']) && $params['limit']) ? " LIMIT " . (int)$params['start'] . ", " . (int)$params['limit'] : '');

        $res = Db::getInstance()->executeS($sql);

        return $res;
    }

    public static function l($string)
    {
        return Translate::getModuleTranslation('ets_geolocation', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
}
