{*
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
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{if isset($msg) && $msg}{assign var="is_hide_popup" value=isset($geo_hide_notification) && $geo_hide_notification}
<div class="ets_geo_popup{if !$is_hide_popup} is_notification{/if}">
    <div class="ets_geo_popup_wrapper">
        <div class="ets_geo_popup_entry">
            <div class="ets_geo_popup_content">
                {if !$is_hide_popup}<div class="ets_geo_popup_header">
                    <span class="header_text">{l s='Are you coming from' mod='ets_geolocation'}{if isset($country_name) && $country_name} {$country_name|escape:'html':'UTF-8'}{/if}{l s=' ?' mod='ets_geolocation'}</span>
                    <div class="ets_geo_close_popup" title="{l s='Close' mod='ets_geolocation'}">
                        <span class="ets_icon_close"></span>
                    </div>
                </div>{/if}
                <div class="ets_geo_popup_content_{if $is_hide_popup}notific{else}confirm{/if}ation">
                    <div class="ets_geo_{if $is_hide_popup}content_notification{else}popup_body{/if}">
                        {$msg nofilter}
                        {if !$is_hide_popup}<div class="ets_geo_popup_group_button">
                            <a class="yes_ok" href="{if isset($btn_link) && $btn_link}{$btn_link|escape:'html':'UTF-8'}{else}javascript:void(0){/if}">{l s='Yes, please do that!' mod='ets_geolocation'}</a>
                            <a class="no_ok" href="javascript:void(0)">{l s='No, keep default settings' mod='ets_geolocation'}</a>
                        </div>{/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>{/if}