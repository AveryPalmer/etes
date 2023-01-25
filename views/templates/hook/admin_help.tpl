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

<div class="panel ets-geo-panel{if isset($class)} {$class|escape:'html':'UTF-8'}{/if}">
    <div class="panel-heading">
        <span class="heading_title">{l s='Geolocation Help' mod='ets_geolocation'}</span>
    </div>
    <div class="panel-content panel-content-help">
        <p>{l s='Thank you for using' mod='ets_geolocation'}<strong> {l s='Geolocation' mod='ets_geolocation'}</strong>, {l s='the auto-detect location module for Prestashop.' mod='ets_geolocation'}
        </p>
        <p>{l s='Below are some points you should pay attention to when using' mod='ets_geolocation'}<strong> {l s='Geolocation' mod='ets_geolocation'}:</strong></p>
        <ul>
            <li>
                {l s='Read the user-guide document (attached to your download) carefully to understand how to use the module as well as find out solution for the problem you may meet when using' mod='ets_geolocation'}<strong> {l s='Geolocation' mod='ets_geolocation'}.</strong>
            </li>
            <br>
            <li>
                {l s='In order to use' mod='ets_geolocation'}<strong> {l s='Geolocation' mod='ets_geolocation'}</strong>, {l s='please download' mod='ets_geolocation'}
                <a style="color: #00B0F0;" href="{$link_download nofilter}" target="_blank" rel="noreferrer noopener">
                {if isset($is_17) && $is_17}
                    GeoLite2-City package
                {else}
                    GeoLite-City package
                {/if}
                </a>
                {l s='and extract it into the' mod='ets_geolocation'}
                <strong> /app/Resources/geoip/ </strong>
                {l s='directory (for Prestashop 1.7) or' mod='ets_geolocation'}
                <strong> /tools/geoip/ </strong>
                {l s='directory (for Prestashop 1.6)' mod='ets_geolocation'}.
            </li>
            <br>
            <li>
                {l s='To enable your customer to be automatically redirected to their language and currency, please make sure you have imported the respective localization pack of the customer location. To import localization pack, follow this short guide:' mod='ets_geolocation'}
                <br><br>
                <p><strong>{l s='Step 1:' mod='ets_geolocation'} </strong>{l s='On Prestashop back office dashboard, navigate to' mod='ets_geolocation'}<strong> {l s='International > Localization' mod='ets_geolocation'}</strong></p>
                
                <p><strong>{l s='Step 2:' mod='ets_geolocation'} </strong>{l s='Select localization pack you want to import > select the content to import' mod='ets_geolocation'}</p>
                
                <p><strong>{l s='Step 3:' mod='ets_geolocation'} </strong>{l s='Click "Import" button' mod='ets_geolocation'}</p>
            </li>
            <br>
            <li>
                {l s='You should turn on [1]Ignore bots[/1] option on [1]Settings[/1] tab. This option is to not redirect search engine bots (Google, Bing, Yahoo, etc.) to another URL therefore your SEO optimization will not be affected.' tags=['<strong>'] mod='ets_geolocation'}
            </li>
            <br>
            <li>
                <a href="https://addons.prestashop.com/en/contact-us?id_product=45015" target="_blank" rel="noreferrer noopener">{l s='Contact us' mod='ets_geolocation'}</a> {l s='if you get into any troubles. We are happy to help, we will try to get back to you within 24 hours or as soon as possible.' mod='ets_geolocation'}
            </li>
        </ul>
    </div>
</div>