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
<div class="ets_geo_popup_choose">
    <div class="ets_geo_popup_wrapper">
        <div class="ets_geo_popup_entry">
            <div class="ets_geo_popup_content">
                <div class="ets_geo_close_popup" title="{l s='Close' mod='ets_geolocation'}">
                    <span class="ets_icon_close"></span>
                </div>
                <div class="ets_geo_popup_content_confirmation">
                    <div class="ets_geo_popup_header">
                        {l s='Choose your location' mod='ets_geolocation'}
                    </div>
                    <div class="ets_geo_popup_body">
                        {if isset($content_choose) && $content_choose}
                            {$content_choose nofilter}
                        {/if}
                    </div>
                    <div class="ets_btn_signin">
                        <a class="link_to_signin" href="{if isset($url_cart_page) && $url_cart_page}{$url_cart_page|escape:'html':'UTF-8'}{else}#{/if}">
                            <i class="fa fa-sign-in" aria-hidden="true"></i>
                            {l s='Sign in to see your addresses' mod='ets_geolocation'}
                        </a>
                    </div>
                    <span class="ets_geo_line"><span>{l s='Or' mod='ets_geolocation'}</span></span>
                    <div class="ets_geo_wrap_prosess">
                        <div class="ets_gep_entry_find">
                            <select data-placeholder="{l s='Choose a Country...' mod='ets_geolocation'}" class="ets_chosen-select" tabindex="2">
                                <option value=""></option>
                                {if isset($list_country) && $list_country}
                                    {foreach from=$list_country item='value' key='key'}
                                        <option
                                                imgflag="{if isset($value.icon_image) && $value.icon_image}{$value.icon_image|escape:'html':'UTF-8'}{/if}"
                                                isocode="{$value.iso_code|strtolower|escape:'html':'UTF-8'}"
                                                value="{$value.id_country|intval}"
                                                {if $value.id_country == $current_country_id}selected="selected"{/if}>
                                            {$value.name|escape:'html':'UTF-8'}
                                        </option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="ets_geo_wrap_zipcode">
                        <span class="ets_geo_btn_submit_apply">{l s='Apply' mod='ets_geolocation'}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>