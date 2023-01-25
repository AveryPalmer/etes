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
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <contact@etssoft.net>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<div class="ets-mod hidden" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;z-index: 9999999;">
    <div class="ets-mod-inner">
        <div class="ets-mod-header">
            <div class="ets-mod-logo">
                <a href="{$ets_profile_url|escape:'html':'UTF-8'}" target="_blank" class="mod-link">
                    <img src="{$img_dir|escape:'html':'UTF-8'}other/logo.jpg" alt="ETS-Soft"/>
                </a>
                <img style="display: none;"  src="{$shortlink|escape:'html':'UTF-8'}logo.jpg" alt="ETS-Soft" />
            </div>
            <div class="ets-mod-intro">
                {if isset($intro) && $intro}
                    {assign var='intro' value=sprintf($intro,$module_name)}
                    {$intro nofilter}
                {/if}
            </div>
            <div class="ets-mod-badges">
                <div class="ets-badge-superhero">
                    <img src="{$img_dir|escape:'html':'UTF-8'}other/ets-superhero-1.png"/>
                    <span>{$trans.txt_superhero|escape:'html':'UTF-8'}</span>
                </div>
                <div class="ets-badge-partner">
                    <img src="{$img_dir|escape:'html':'UTF-8'}other/ets-partner-badge.png"/>
                    <span>{$trans.txt_partner|escape:'html':'UTF-8'}</span>
                </div>
            </div>
        </div>
        <div class="ets-body">
            <div class="ets-mod-left">
                {if $categories}
                    <div class="ets-mod-cats_mobile">
                        <h4>&nbsp;</h4>
                    </div>
                    <ul class="ets-mod-cats">
                    {foreach from=$categories item='cat'}
                        <li {if $cat.id==0}class="active"{/if} data-id="{$cat.id|intval}">{$cat.title|escape:'html':'UTF-8'}</li>
                    {/foreach}
                    </ul>
                {/if}
            </div>
            <div class="ets-mod-right">
                {if $modules && $enabled}
                    <ul class="ets-mod-list">
                        {foreach from=$modules item='mod'}
                            {if isset($mod.link) && $mod.link && isset($mod.title) && $mod.title}
                                {assign var='modcats' value=array_unique(array_map('intval',explode(',',$mod.categories)))}
                                <li class="mod-item cat-0 {if $modcats}{foreach from=$modcats item='catid'}cat-{$catid|intval} {/foreach}{/if}">
                                    <a target="_blank" href="{$mod.link nofilter}"  class="mod-link">
                                        {if isset($mod.icon) && $mod.icon}
                                            <div class="mod-image">
                                                <img src="{$mod.icon|escape:'html':'UTF-8'}" title="{$mod.title nofilter}">
                                            </div>
                                        {/if}
                                        <div class="mod-title">{$mod.title nofilter}</div>
                                        {if isset($mod.promo) && $mod.promo}<div class="mod-promo">{$mod.promo|escape:'html':'UTF-8'}</div>{/if}
                                        {if isset($mod.must_have) && $mod.must_have}<div class="mod-must-have">{$trans.txt_must_have|escape:'html':'UTF-8'}</div>{/if}
                                        {if isset($mod.desc) && $mod.desc}
                                            <div class="mod-desc">{$mod.desc nofilter}</div>
                                        {/if}
                                        {if isset($mod.badge) && $mod.badge}
                                            <div class="mod-badge {$mod.badge|escape:'html':'UTF-8'}">
                                                {if $mod.badge=='fav'}
                                                    {$trans.txt_fav|escape:'html':'UTF-8'}
                                                {else}
                                                    {$trans.txt_elected|escape:'html':'UTF-8'}
                                                {/if}
                                            </div>
                                        {elseif isset($mod.download) && $mod.download >= 500}
                                            <div class="mod-download">{$mod.download|intval}+ {$trans.txt_downloads|escape:'html':'UTF-8'}</div>
                                        {/if}
                                        {if isset($mod.rating) && $mod.rating || isset($mod.price) && $mod.price}
                                            <div class="mod-footer">
                                                {if isset($mod.ratingClass) && $mod.ratingClass && isset($mod.rating_count) && $mod.rating_count}
                                                    <div class="mod-rating {$mod.ratingClass|escape:'html':'UTF-8'}">({$mod.rating_count|intval})</div>
                                                {/if}
                                                {if isset($mod.price) && $mod.price}
                                                    <div class="mod-price">
                                                        <span class="mod-price-int">{$mod.price|intval}</span>
                                                        <span class="mod-price-unit">&euro;</span>
                                                        <span class="mod-price-dec">99</span>
                                                    </div>
                                                {/if}
                                            </div>
                                        {/if}
                                    </a>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                    <div class="ets-mod-external">
                        <a href="{$ets_profile_url|escape:'html':'UTF-8'}" target="_blank" class="ets-mod-external-link btn btn-default mod-link">{$trans.txt_view_all|escape:'html':'UTF-8'}</a>
                    </div>
                {/if}
            </div>
        </div>
        {if $contactUrl}<div class="ets-mod-contact"><a class="mod-link" href="{$contactUrl|escape:'html':'UTF-8'}" target="_blank" title="{$trans.txt_contact|escape:'html':'UTF-8'}"><span>{$trans.txt_contact|escape:'html':'UTF-8'}</span></a></div>{/if}
        <div class="ets-mod-close">{$trans.txt_close|escape:'html':'UTF-8'}</div>
    </div>
</div>