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
{if $ets_geolocation_error_message}
    {$ets_geolocation_error_message nofilter}
{/if}
{if isset($res_visit) && $res_visit}
<script type="text/javascript">
    var res_visit = '{$res_visit|json_encode}';
    var res_visit_color = '{$res_visit_color|json_encode}';
</script>
{/if}
{if isset($doughnut_data) && $doughnut_data}
<script type="text/javascript">
    var datasets_tron = '{$datasets_tron|json_encode}';
    var labels_tron = '{$labels_tron|json_encode}';
    var doughnut_data = '{$doughnut_data|json_encode}';
    var visit_text = '{$visit_text nofilter}';
</script>
{/if}
{if isset($label_value) && $label_value}
<script type="text/javascript">
    var label_value = '{$label_value|escape:'html':'UTF-8'}';
</script>
{/if}
{if isset($control) && $control}
    <script type="text/javascript">
        var control = '{$control|escape:'html':'UTF-8'}';
    </script>
{/if}

{if isset($linechar_data) && $linechar_data}
    <script type="text/javascript">
        var linechar_data = '{$linechar_data|json_encode}';
        var data_label = '{$data_label|json_encode}';
        var data_filter = '{$data_filter|escape:'html':'UTF-8'}';
        var data_filter_label = '{$data_filter_label|escape:'html':'UTF-8'}';
    </script>
{/if}
<script type="text/javascript"> 
    var ets_geolocation_ajax_url = '{$ets_geolocation_ajax_url nofilter}'; 
    var ets_geolocation_author_ajax_url ='{$ets_geolocation_author_ajax_url nofilter}';
    var ets_geolocation_default_lang = {$ets_geolocation_default_lang|intval};
    var ets_geolocation_is_updating = {$ets_geolocation_is_updating|intval};                            
    var ets_geolocation_is_config_page = {$ets_geolocation_is_config_page|intval};
    var ets_geolocation_invalid_file = '{$ets_geolocation_invalid_file|escape:'html':'UTF-8'}';
    var send_mail_label='{l s='Also send this response to customer via email' js=1 mod='ets_geolocation'}';
</script>
<script type="text/javascript" src="{$ets_geolocation_module_dir|escape:'html':'UTF-8'}views/js/admin.js"></script>
<div class="bootstrap back_end_ets_geo">
    <div class="ets_geolocation_form_content_admin {if $control} ets_geolocation_form_{$control|escape:'html':'UTF-8'}{/if}">
        {$ets_geolocation_sidebar nofilter}
        <div class="geo_center_content {if $control} ets_geolocation{$control|escape:'html':'UTF-8'}{/if}">
            {$ets_geolocation_body_html nofilter}
        </div>
    </div>
</div>