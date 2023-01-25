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
<div class="ets_geo_tabwrapper">
    <div class="with_tabs">
        <div class="page_head_tabs">
            <ul>
                {if $list}
                    {foreach from=$list item='tab'}
                        <li>
                            <a class="{if $active == $tab.id }current{/if} list-tab-item" href="{$tab.url|escape:'html':'UTF-8'}" id="{$tab.id|escape:'html':'UTF-8'}">
                                <i class="icon_{$tab.label|escape:'html':'UTF-8'}"></i>
                                {$tab.label|escape:'html':'UTF-8'}
                                {if isset($tab.total_result) && $tab.total_result} ({$tab.total_result|intval}){/if}
                            </a>
                        </li>
                    {/foreach}
                {/if}
                {if isset($intro) && $intro}
                <li class="li_othermodules "><a class="link_othermodules" data-href="{$other_modules_link|escape:'html':'UTF-8'}">
                        <span class="tab-title">{l s='Other modules' mod='ets_geolocation'}</span>
                        <span class="tab-sub-title">{l s='Made by ETS-Soft' mod='ets_geolocation'}</span>
                    </a></li>
                {/if}
            </ul>
        </div>
    </div>
</div>
<div class="ets_geo_tabspace"></div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.ets_geo_tabspace').css('height',$('.ets_geo_tabwrapper').height()+'px');
        $(window).resize(function(){
            setTimeout(function(){ $('.ets_geo_tabspace').css('height',$('.ets_geo_tabwrapper').height()+'px'); }, 1000);
            
        });
        $(document).on('click','.menu-collapse',function(){
            $('.ets_geo_tabspace').css('height',$('.ets_geo_tabwrapper').height()+'px');
        });
        $(window).load(function(){
            $('.ets_geo_tabspace').css('height',$('.ets_geo_tabwrapper').height()+'px');
        });
    });
    
</script>