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

{if isset($is_17)}
    {l s='In order to use Geolocation, please manually download' mod='ets_geolocation'} <a href="{$link_download nofilter}" target="_blank" rel="noreferrer noopener">{l s='this file (GEO database)' mod='ets_geolocation'}</a> {l s='and extract it into the' mod='ets_geolocation'}
    {if $is_17}/app/Resources/geoip/{else}/tools/geoip/{/if} {l s='or' mod='ets_geolocation'}<a class="auto_upload" href="{$link_auto nofilter}" target="_blank"> {l s='click here' mod='ets_geolocation'}</a>
    {l s='to do that automatically' mod='ets_geolocation'}
{/if}