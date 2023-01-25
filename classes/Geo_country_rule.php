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

class Geo_country_rule extends ObjectModel{
    public $id_rule;
    public $id_country_rule;

    public static $definition = array(
        'table' => 'ets_geo_country_rule',
        'primary' => 'id_rule',
        'fields' => array(
            'id_rule' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'id_country_rule' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
        ),
    );

    public static function setRuleOfCountry($id_rule){
        if (!Validate::isUnsignedId($id_rule)) {
            return false;
        }
    }
    
}