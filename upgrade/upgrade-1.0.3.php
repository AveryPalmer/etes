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
function upgrade_module_1_0_3($object)
{
    $res = $object->registerHook('actionObjectCartAddBefore');
    $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_geo_address_detected`(
                `id_address` int(4) unsigned NOT NULL,
                `iso_code_country` varchar(4) NOT NULL,
                PRIMARY KEY (`id_address`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8
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
        ')
        && Db::getInstance()->execute('
            ALTER TABLE `' . _DB_PREFIX_ . 'ets_geo_rule` 
                ADD `all_countries` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `block_user`;
        ');
	$res &= $object->_installConfigs(true);
    return $res;
}
