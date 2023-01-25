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
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}

{extends file="helpers/form/form.tpl"}
{block name="input"}
    {if $input.type == 'switch'}
    	<span class="switch prestashop-switch fixed-width-lg">
    		{foreach $input.values as $value}
    		<input type="radio" name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_off"{/if} value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>
    		{strip}
    		<label {if $value.value == 1} for="{$input.name|escape:'html':'UTF-8'}_on"{else} for="{$input.name|escape:'html':'UTF-8'}_off"{/if}>
    			{$value.label|escape:'html':'UTF-8'}
    		</label>
    		{/strip}
    		{/foreach}
    		<a class="slide-button btn"></a>
    	</span>
    {else}
        {$smarty.block.parent}               
    {/if}            
{/block}
{block name="field"}
    {if $input.type == 'geo_countries'}
        <div class="well margin-form wrap_country">
            <table class="table" style="border-spacing : 0; border-collapse : collapse;">
                <thead>
	                <tr>{if isset($fields_value['all_countries'])}{assign var="all_countries" value=$fields_value['all_countries']|intval}{else}{assign var="all_countries" value=0}{/if}
	                    <th><input type="checkbox" name="all_countries" value="1"{if $all_countries} checked="checked"{/if} onclick="checkDelBoxes(this.form, 'countries[]', this.checked)" /></th>
	                    <th>{l s='All' mod='ets_geolocation'}</th>
	                </tr>
                </thead>
                <tbody>
                {assign var="id_option" value=$input.options.id}
                {foreach $input.options.query as $option}
                    <tr>
                        <td><input type="checkbox" name="countries[]" id="item{$option.$id_option|escape:'html':'UTF-8'}" value="{$option.$id_option|escape:'html':'UTF-8'}" {if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && in_array($option.$id_option, $fields_value[$input.name]) || $all_countries}checked="checked"{/if}/></td>
                        <td><label for="item{$option.$id_option|escape:'html':'UTF-8'}">{$option.name|escape:'html':'UTF-8'}</label></td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
    {$smarty.block.parent}
{/block}