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

Class Geo_visit_day extends ObjectModel{
    public $day;
    public $month;
    public $year;
    public $ip_visit;

    public static $definition = array(
        'table' => 'ets_geo_visit_day',
        'primary' => 'ip_visit',
        'fields' => array(
            'day' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'month' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'year' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'ip_visit' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
        ),
    );
    public function __construct($id = null, Context $context = null) {
        parent::__construct($id);
        $this->day = (int)date('j');
        $this->month = (int)date('n');
        $this->year = (int)date('Y');
        unset($context);
    }

    public function deleteOtherDay(){
        $sql = 'SELECT `ip_visit` 
                  FROM `'._DB_PREFIX_.'ets_geo_visit_day` 
                  WHERE `day` = '.(int)$this->day.' 
                        AND `month` = '.(int)$this->month.' 
                        AND `year` = '.(int)$this->year.' 
                  ';

        if ( ! Db::getInstance()->getRow($sql) ){
            return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_geo_visit_day`');
        }
        return true;
    }
}