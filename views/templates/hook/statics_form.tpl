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

<form id="module_form" class="defaultForm form-horizontal form_static" action="{if isset($url_post)}{$url_post|escape:'html':'UTF-8'}{/if}">
    <div class="panel pane_top">
        <div class="panel-heading">
            {l s='Visitor map' mod='ets_geolocation'}
        </div>
        <div class="form-wrapper">
            <div class="geo_maps_wrapper">

                <div class="col-lg-8">
                    <div class="geo_canvas_maps">
                        <div id="geo_map_canvas" class="geo_map_canvas">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="wrap_maps_filter {if isset($total_line) && !$total_line}no_data{/if}">
                        <div class="wrap_filter">
                            <h3 class="geo_filter_title">{l s='Percentage by country' mod='ets_geolocation'}</h3>
                            <div class="geo_filter_dropdown">
                                <div class="dropdown">
                                    <button class="geo_btn_filter dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {if isset($param_filter_map) && $param_filter_map}{$param_filter_map|escape:'html':'UTF-8'}{/if}
                                    </button>
                                    <div class="dropdown-menu">
                                        <li><a class="dropdown-item" href="javascript:void(0)" data-value="all_times">{l s='All time' mod='ets_geolocation'}</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" data-value="month">{l s='This month' mod='ets_geolocation'}</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" data-value="year">{l s='This year' mod='ets_geolocation'}</a></li>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="maps_total_statics">
                            {if isset($total_line) && $total_line}
                                {foreach from=$total_line item=value}
                                    <div class="box_best">
                                        <p><span class="label_total_map">{$value.name|escape:'html':'UTF-8'}</span> {$value.percent|escape:'html':'UTF-8'}%</p>
                                        <div class="box_total_maps">
                                            <span class="line_main"></span>
                                            <span class="extra" style="width:{$value.percent|escape:'html':'UTF-8'}%;background-color: {$value.color|escape:'html':'UTF-8'};"></span>
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                        <div class="nodata">
                            <span>{l s='No data available' mod='ets_geolocation'}</span>
                        </div>
                    </div>
                </div>
            </div> {* .geo_maps_wrapper *}
        </div>
    </div>
    <div class="static_bottom">
        <div class="geo_total_wrapper">
            <div class="col-lg-4">
                <div class="bottom_wrapper wrapper_doughnut {if isset($doughnut_data) && ! $doughnut_data}no_data{/if}">
                    <div class="wrap_filter">
                        <h3 class="geo_filter_title">{l s='Visitor ratio' mod='ets_geolocation'}</h3>
                        <div class="geo_filter_dropdown">
                            <div class="dropdown">
                                <button class="geo_btn_filter dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {if isset($data_filter_tron) && $data_filter_tron}{$data_filter_tron|escape:'html':'UTF-8'}{/if}
                                </button>
                                <div class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="all_times">{l s='All time' mod='ets_geolocation'}</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="month">{l s='This month' mod='ets_geolocation'}</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="year">{l s='This year' mod='ets_geolocation'}</a></li>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content_canvas">
                        <div id="geo_canvas_doughnut" class="box_canvast geo_canvas_doughnut" style="height: 350px;width: 320%;max-width: 100%;">
                            <canvas id="geo_chart_doughnut" class="geo_chart_doughnut" style="height: 350px;width: 320%;max-width: 100%;" width="494" height="350"></canvas>
                        </div>
                    </div>
                    <div class="nodata">
                        <span>{l s='No data available' mod='ets_geolocation'}</span>
                    </div>
                </div>
            </div>{*.col-lg-4*}
            <div class="col-lg-4">
                <div class="bottom_wrapper wrapper_linechar {if isset($linechar_data) && ! $linechar_data}no_data{/if}">
                    <div class="wrap_filter">
                        <h3 class="geo_filter_title">{l s='Visitor growth' mod='ets_geolocation'}</h3>
                        <div class="geo_filter_dropdown">
                            <div class="dropdown">
                                <button class="geo_btn_filter dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {if isset($data_filter) && $data_filter}{$data_filter|escape:'html':'UTF-8'}{/if}
                                </button>
                                <div class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="all_times">{l s='All time' mod='ets_geolocation'}</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="month">{l s='This month' mod='ets_geolocation'}</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="year">{l s='This year' mod='ets_geolocation'}</a></li>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content_canvas">
                        <div id="geo_canvas_linechar" class="box_canvast">
                            <canvas id="geo_chart_linechar" class="geo_chart_linechar"></canvas>
                        </div>
                    </div>
                    <div class="nodata">
                        <span>{l s='No data available' mod='ets_geolocation'}</span>
                    </div>

                </div>
            </div>
            <div class="col-lg-4">
                <div class="bottom_wrapper wrap_horizontal {if isset($linechar_data) && ! $linechar_data}no_data{/if}">
                    <div class="wrap_filter">
                        <h3 class="geo_filter_title">{l s='Visitor comparison' mod='ets_geolocation'}</h3>
                        <div class="geo_filter_dropdown">
                            <div class="dropdown">
                                <button class="geo_btn_filter dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {if isset($data_filter) && $data_filter}{$data_filter|escape:'html':'UTF-8'}{/if}
                                </button>
                                <div class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="all_times">{l s='All time' mod='ets_geolocation'}</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="month">{l s='This month' mod='ets_geolocation'}</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-value="year">{l s='This year' mod='ets_geolocation'}</a></li>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content_canvas">
                        <div id="geo_canvas_horizontal" class="box_canvast">
                            <canvas id="geo_chart_horizontal" class="geo_chart_horizontal"></canvas>
                        </div>
                    </div>
                    <div class="nodata">
                        <span>{l s='No data available' mod='ets_geolocation'}</span>
                    </div>
                </div>
            </div>{* .col-lg-4*}
        </div>
    </div>
</form>