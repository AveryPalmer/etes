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

class Geo_trans
{
    public static $trans = array(
        'ETS_GEO_CONFIRM_MSG' => array(
            'en' => 'Our system detects that you are visiting our website from [1][detected_country][/1]. Do you want to change website language from [1][current_language][/1] to [1][detected_language][/1] and currency from [1][current_currency][/1] to [1][detected_currency][/1] ?',
        ),
        'ETS_GEO_LANGUAGE_MSG' => array(
            'en' => 'Our system detects that you are visiting our website from [1][detected_country][/1]. Do you want to change website language from [1][current_language][/1] to [1][detected_language][/1] ?',
        ),
        'ETS_GEO_CURRENCY_MSG' => array(
            'en' => 'Our system detects that you are visiting our website from [1][detected_country][/1]. Do you want to change website currency from [1][current_currency][/1] to [1][detected_currency][/1] ?',
        ),
        'ETS_GEO_SETTING_MSG' => array(
            'en' => 'We are setting your language and currency. Please wait a moment..!',
        ),
        'ETS_GEO_CHOOSE_MSG' => array(
            'en' => 'Taxes, delivery options, shipping price and delivery speeds may vary for different locations',
        ),
        'ETS_GEO_BLOG_MSG' => array(
            'en' => 'Sorry! You are blocked from accessing this website.',
        ),
    );
}