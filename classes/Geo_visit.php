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

Class Geo_visit extends ObjectModel{
    public $id_country;
    public $day;
    public $month;
    public $year;
    public $visit;
    public $last_ip;
    public $last_visit_time;

    public static $definition = array(
        'table' => 'ets_geo_visit',
        'primary' => 'id_country',
        'fields' => array(
            'id_country' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'day' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'month' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'year' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'visit' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId'),
            'last_ip' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'last_visit_time' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function __construct($id = null, Context $context = null) {
        parent::__construct($id);
        if ($id){
            $this->id_country = $id;
        }
        $this->day = (int)date('j');
        $this->month = (int)date('n');
        $this->year = (int)date('Y');
        unset($context);
    }

    public static function checkIpVisitToDay($ip){
        $sql = 'SELECT v.`id_country`   
        FROM `'._DB_PREFIX_.'ets_geo_visit` v 
        LEFT JOIN `'._DB_PREFIX_.'ets_geo_visit_day` vd ON vd.`ip_visit` = \''.pSQL($ip).'\' 
        WHERE vd.`day` = '.(int)date('j').' AND vd.`month`='.(int)date('n').' AND vd.`year` ='.(int)date('Y').'  ';
        if ( Db::getInstance()->getRow($sql)){
            return true;
        }
        return false;
    }

    public static function getCountryVisitToDay($id_country){
        $sql = 'SELECT `id_country`
        FROM `'._DB_PREFIX_.'ets_geo_visit` 
        WHERE `day` = '.(int)date('j').' AND `month`='.(int)date('n').' AND `year` ='.(int)date('Y').' AND `id_country`= '.(int)$id_country.'  ';
        if ( Db::getInstance()->getRow($sql) ){
            return true;
        }
        return false;
    }

    public function update_custom(){
        $sql_up = 'UPDATE `'._DB_PREFIX_.'ets_geo_visit` 
                            SET 
                                day =\''.(int)$this->day.'\', 
                                month =\''.(int)$this->month.'\',
                                year = \''.(int)$this->year.'\', 
                                visit =visit+1,
                                last_ip = \''.$this->last_ip.'\',
                                last_visit_time = \''.date('Y-m-d H:i:s').'\'
                            WHERE id_country =\''.(int)$this->id_country.'\' AND day =\''.(int)$this->day.'\' AND month =\''.(int)$this->month.'\' AND year = \''.(int)$this->year.'\' ';
        return (bool)Db::getInstance()->execute($sql_up);
    }

    public static function getDataVisit($params){
        $where = 'WhERE 1 ';
        $group_by = '';
        $sql = '';
        if ( $params['status'] == 'ajax' ){
            if ( $params['value'] == 'month' ){
                $where .=' AND gv.`month`= \''.(int)date('m').'\' AND gv.`year`= \''.(int)date('Y').'\'  ';
            }
            if ( $params['value'] == 'year' ){
                $where .=' AND gv.`year`= \''.(int)date('Y').'\'  ';
                if ( !isset($params['type']) || (isset($params['type']) && $params['type'] != 'maps') ){
                    $group_by .= ' gv.`id_country`,gv.`month` ';
                }
            }
            if ( $params['value'] == 'all_times' ){
                if ( isset($params['type']) && $params['type']== 'maps' ){
                    $where = '';
                }else{
                    $max_year = date('Y');
                    $min_year = self::getMinYear();
                    $distance = ($max_year - $min_year);

                    if ( $distance < 5){
                        $where .=' AND gv.`year`= \''.(int)$max_year.'\'  ';
                    }else{

                        $group_by .= ' gv.`id_country`,gv.`year` ';
                    }
                }
            }
        }
        if ( $params['status'] == '30day' ){
            $where .='DATE_FORMAT( STR_TO_DATE(CONCAT(gv.`day`,\'/\',gv.`month`,\'/\',gv.`year`),\'%d/%m/%Y\'),\'%Y-%m-%d\') > now() - interval '.(int)$params['value'].' day ';
        }

        $sql_join ='LEFT JOIN `'._DB_PREFIX_.'country` ct ON ( gv.`id_country` = ct.`id_country` )
                LEFT JOIN `'._DB_PREFIX_.'country_lang` ctl ON ( ctl.`id_country` = ct.`id_country` AND ctl.`id_lang` = '.(int)Context::getContext()->language->id.' )
                LEFT JOIN `'._DB_PREFIX_.'country_shop` cts ON ( cts.`id_country` = ct.`id_country` AND cts.`id_shop` = '.(int)Context::getContext()->shop->id.' )';


        if ( isset($params['chart']) && $params['chart']== 'linechar' ){
            $sql = 'SELECT gv.`id_country`,gv.`month`, SUM(gv.`visit`) as `total_visit`, ct.`iso_code` ,ctl.`name`,gv.`day`,gv.`year`    
                FROM `'._DB_PREFIX_.'ets_geo_visit` gv
                '.$sql_join.'
                '.$where.' 
                GROUP BY '.( $group_by  ? $group_by : 'gv.`id_country`, gv.`day` ').'
                ORDER BY `last_visit_time` DESC,`total_visit` DESC
            ';
        }elseif (isset($params['map_total']) && $params['map_total']){
            $sql = 'SELECT gv.`id_country`,gv.`month`, SUM(gv.`visit`) as `total_visit`, ct.`iso_code` ,ctl.`name`,gv.`day`   
                FROM `'._DB_PREFIX_.'ets_geo_visit` gv
                '.$sql_join.'
                '.$where.' 
                GROUP BY '.($group_by ? $group_by : 'gv.`id_country`').'
                ORDER BY `last_visit_time` DESC,`total_visit` DESC
            ';
        }else{
            $sql = 'SELECT gv.`id_country`,gv.`month`, SUM(gv.`visit`) as `visit`,gv.`year`, ct.`iso_code` ,ctl.`name` '.( (isset($params['chart']) && $params['chart'] =='linechar') ? ',GROUP_CONCAT(gv.`day`) as day, GROUP_CONCAT( gv.`visit`) as sum_visit' :'').' 
                FROM `'._DB_PREFIX_.'ets_geo_visit` gv
                '.$sql_join.'
                '.$where.' 
                GROUP BY '.($group_by ? $group_by : 'gv.`id_country`').'
                ORDER BY `visit` DESC,`last_visit_time` DESC
            ';
        }

        return Db::getInstance()->executeS($sql);
    }

    public function tinyArray( $arr_data = null){
        $resule = array();
        if( empty($arr_data) ){
            return;
        }

        for ( $i = 0, $total = count($arr_data); $i< $total; $i++ ){
            $resule[] = $arr_data[$i]['visit'];
        }
        return $resule;
    }

    public static function getMinYear(){
        return Db::getInstance()->getValue('SELECT MIN(`year`) as `min_year` FROM `'. _DB_PREFIX_ .'ets_geo_visit`');
    }
}