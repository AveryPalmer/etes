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
{if isset($lang_links) && $lang_links}{foreach from=$lang_links key='iso' item='link'}
	<link rel="geolocation" href="{$link nofilter}" hreflang="{$iso|escape:'html':'utf-8'}">
{/foreach}{/if}
<script type="text/javascript">
{if isset($popup_is_load) && $popup_is_load}var popup_is_load='{$popup_is_load|intval}';{/if}
{if isset($ajax_url) && $ajax_url}var ajax_url='{$ajax_url nofilter}';{/if}
{if isset($page_controller) && $page_controller}var page_controller='{$page_controller|escape:'html':'utf-8'}';{/if}
</script>

