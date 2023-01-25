/**
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
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2022 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
if (typeof PS_ALLOW_ACCENTED_CHARS_URL === 'undefined')
    PS_ALLOW_ACCENTED_CHARS_URL = false;

var ADMIN_GEO = {
    init: function () {
        this.custom_click();
        this.change_status();
        this.canvas_map();
        this.canvas_doughnut();
        this.canvas_linechar();
        this.canvas_horizontal();
        this.add_active_tabs();
        this.click_download_file();

    },
    custom_click: function () {
        $(document).on('click', '.ets_geolocation_form_statistics .dropdown-menu a', function () {
            $(this).closest('.dropdown-menu').prev().html($(this).html());
        });
    },
    add_active_tabs: function () {
        if (typeof control != "undefined" && control) {
            if (control == 'statistics') {
                $('#subtab-AdminGeoLocationStatistics').addClass('active -active');
            }
            if (control == 'settings') {
                $('#subtab-AdminGeoLocationSettings').addClass('active -active');
            }
            if (control == 'rules') {
                $('#subtab-AdminGeoLocationRules').addClass('active -active');
            }
            if (control == 'messages') {
                $('#subtab-AdminGeoLocationMessages').addClass('active -active');
            }
            if (control == 'help') {
                $('#subtab-AdminGeoLocationHelp').addClass('active -active');
            }
        }
    },
    canvas_horizontal: function () {
        if (!$('#geo_chart_horizontal').length > 0) {
            return;
        }
        var horizontal_data_obj;
        var horizontal_label;
        if (typeof linechar_data != "undefined" && linechar_data) {
            horizontal_data_obj = jQuery.parseJSON(linechar_data);
            horizontal_label = jQuery.parseJSON(data_label);
        }
        var horizontalBarChartData = {
            labels: horizontal_label,
            datasets: horizontal_data_obj
        };
        var ctx = document.getElementById('geo_chart_horizontal').getContext('2d');
        var myHorizontalBar = new Chart(ctx, {
            type: 'horizontalBar',
            data: horizontalBarChartData,
            options: {
                // Elements options apply to all of the options unless overridden in a dataset
                // In this case, we are setting the border of each horizontal bar to be 2px wide
                maintainAspectRatio: false,
                elements: {
                    rectangle: {
                        borderWidth: 2,
                    }
                },
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                    text: 'Chart.js Horizontal Bar Chart'
                },
                plugins: {
                    filler: {
                        propagate: false
                    }
                },
                scales: {
                    xAxes: [{
                        display: true,
                        ticks: {
                            min: 0,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            },
                        },
                        scaleLabel: {
                            display: true,
                            labelString: typeof label_value != "undefined" ? label_value : 'Visits'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: (typeof data_filter_label != "undefined") ? data_filter_label : ''
                        }
                    }]
                }
            }
        });

        $(document).on('click', '.wrap_horizontal .geo_filter_dropdown .dropdown-menu a', function () {

            var element_select = $(this);
            if (element_select.hasClass('disable')) {
                return false;
            }
            element_select.addClass('disable');
            $('.wrap_horizontal').addClass('loadding');
            $url_action = $('.form_static').attr('action');
            $.ajax({
                url: $url_action + '&submit_ajax=1&ajax=1',
                data: {
                    horizontal_char: true,
                    geo_option_filter: $(this).attr('data-value')
                },
                type: 'post',
                dataType: 'json',
                success: function (json) {
                    if (json.linechar_data && json.linechar_data.length !== 0) {
                        $('.wrap_horizontal .label_filter').empty().html(json.data_filter);
                        $('.wrap_horizontal').removeClass('no_data');
                        myHorizontalBar.data.labels = json.data_label;
                        myHorizontalBar.data.datasets = json.linechar_data;
                        myHorizontalBar.options.scales.yAxes[0].scaleLabel.labelString = json.data_filter;
                        myHorizontalBar.update();
                    } else {
                        $('.wrap_horizontal').addClass('no_data');
                    }
                    $('.wrap_horizontal').removeClass('loadding');
                    element_select.removeClass('disable');
                },
                error: function (error) {

                }
            });
        });
    },
    canvas_linechar: function () {
        if (!$('#geo_chart_linechar').length > 0) {
            return;
        }
        var linechar_data_obj;
        var linechar_label;
        if (typeof linechar_data != "undefined" && linechar_data) {
            linechar_data_obj = jQuery.parseJSON(linechar_data);
            linechar_label = jQuery.parseJSON(data_label);
        }
        var linechar_config = {
            type: 'line',
            data: {
                labels: linechar_label,
                datasets: linechar_data_obj
            },
            spanGaps: true,
            options: {
                maintainAspectRatio: false,
                spanGaps: false,
                responsive: true,
                elements: {},
                plugins: {
                    filler: {
                        propagate: false
                    }
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: typeof data_filter_label != "undefined" ? data_filter_label : ''
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            min: 0,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            },
                        },
                        scaleLabel: {
                            display: true,
                            labelString: typeof label_value != "undefined" ? label_value : 'Visits'
                        }
                    }]
                },
                legend: {
                    fullWidth: true,
                    position: 'top',
                },
                tooltips: {
                    mode: 'point',
                    intersect: true,
                }
            }
        };

        var ctx = document.getElementById('geo_chart_linechar').getContext('2d');
        var myLineChar = new Chart(ctx, linechar_config);

        $(document).on('click', '.wrapper_linechar .geo_filter_dropdown .dropdown-menu a', function () {
            var element_select = $(this);
            if (element_select.hasClass('disable')) {
                return false;
            }
            element_select.addClass('disable');
            $('.wrapper_linechar').addClass('loadding');
            $url_action = $('.form_static').attr('action');
            $.ajax({
                url: $url_action + '&submit_ajax=1&ajax=1',
                data: {
                    line_char: true,
                    geo_option_filter: $(this).attr('data-value')
                },
                type: 'post',
                dataType: 'json',
                success: function (json) {
                    if (json.linechar_data && json.linechar_data.length !== 0) {
                        $('.wrapper_linechar .label_filter').empty().html(json.data_filter);
                        $('.wrapper_linechar').removeClass('no_data');
                        myLineChar.data.labels = json.data_label;
                        myLineChar.data.datasets = json.linechar_data;
                        myLineChar.options.scales.xAxes[0].scaleLabel.labelString = json.data_filter_label;
                        myLineChar.update();
                    } else {
                        $('.wrapper_linechar').addClass('no_data');
                    }
                    $('.wrapper_linechar').removeClass('loadding');
                    element_select.removeClass('disable');
                },
                error: function (error) {

                }
            });
        });
    },
    change_status: function () {
        $(document).on('click', '.ets_geo_rule .list-action-enable', function (evt) {
            evt.preventDefault();
            var btn = $(this);
            if (!btn.hasClass('loading')) {
                btn.addClass('loading');
                $.ajax({
                    url: btn.attr('href') + '&change_enabled&ajax=1',
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        btn.removeClass('loading');
                        if (json) {
                            if (json.enabled) {
                                btn.removeClass('action-disabled').addClass('action-enabled');
                                btn.html('<i class="icon-check"></i>');
                            } else {
                                btn.removeClass('action-enabled').addClass('action-disabled');
                                btn.html('<i class="icon-remove"></i>');
                            }
                            btn.attr('href', json.href);
                            btn.removeClass('disabled');
                            if (json.title) {
                                $('.list-item-' + json.listId + '.field-' + json.field).attr('title', json.title);
                            }
                            if (json.messageType && json.message) {
                                ADMIN_GEO.showSaveMessage(json.message, json.messageType);
                            }
                        }
                    },
                    error: function (error) {
                        btn.removeClass('loading');
                    }
                });
            }
            return false;
        });
    }, // change_status
    canvas_doughnut: function () {

        if (!$('#geo_chart_doughnut').length > 0) {
            return;
        }
        var tron_datasets = [];
        var tron_label = [];
        if ((typeof datasets_tron !== "undefined") && datasets_tron) {
            tron_datasets = jQuery.parseJSON(datasets_tron);
            tron_label = jQuery.parseJSON(labels_tron);
        }
        var doughnut_config = {
            type: 'doughnut',
            data: {
                datasets: [tron_datasets],
                labels: tron_label,
            },
            options: {
                circumference : 2*Math.PI,
                rotation : Math.PI,
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                    text: 'Chart.js Doughnut Chart'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                tooltips: {
                    enabled: true
                },
                elements: {
                    center: {
                        text: typeof visit_text !== "undefined" ? visit_text : 'Visit',
                        color: '#FF6384', // Default is #000000
                        fontStyle: 'Arial', // Default is Arial
                        sidePadding: 20, // Defualt is 20 (as a percentage)
                    },
                    arc: {
                        borderWidth: 0,
                    },
                    borderWidth: 1
                },
                cutoutPercentage: 60,
                plugins: {
                    labels: {
                        // render 'label', 'value', 'percentage', 'image' or custom function, default is 'percentage'
                        render: 'value',

                        // precision for percentage, default is 0
                        precision: 0,

                        // identifies whether or not labels of value 0 are displayed, default is false
                        showZero: true,

                        // font size, default is defaultFontSize
                        fontSize: 14,

                        // font color, can be color array for each data or function for dynamic color, default is defaultFontColor
                        fontColor: '#fff',

                        // font style, default is defaultFontStyle
                        fontStyle: 'normal',

                        // font family, default is defaultFontFamily
                        fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

                    }
                }
            }
        };
        var ctx = document.getElementById('geo_chart_doughnut').getContext('2d');
        var myDoughnut = new Chart(ctx, doughnut_config);
        $(document).on('click', '.wrapper_doughnut .geo_filter_dropdown .dropdown-menu a', function () {
            var element_select = $(this);
            if (element_select.hasClass('disable')) {
                return false;
            }
            element_select.addClass('disable');
            $('.wrapper_doughnut').addClass('loadding');

            $url_action = $('.form_static').attr('action');
            $.ajax({
                url: $url_action + '&submit_ajax=1&ajax=1',
                data: {
                    doughnut: true,
                    geo_option_filter: $(this).attr('data-value')
                },
                type: 'post',
                dataType: 'json',
                success: function (json) {
                    if (json.doughnut_data && json.doughnut_data.length !== 0) {
                        var data_json = [];
                        var labels_json = [];
                        $('.wrapper_doughnut .label_filter').empty().html(json.data_filter);
                        $('.wrapper_doughnut').removeClass('no_data');
                        $.each(json.doughnut_data, function (key, value) {
                            //data_json.push(parseInt(value));
                            //labels_json.push(key);
                        });
                        doughnut_config.data.datasets = [json.doughnut_data];
                        myDoughnut.data.labels = json.label_tron;
                        doughnut_config.options.elements.center.text = json.visit_text;
                        myDoughnut.update();
                    } else {
                        $('.wrapper_doughnut').addClass('no_data');
                    }
                    $('.wrapper_doughnut').removeClass('loadding');
                    element_select.removeClass('disable');
                },
                error: function (error) {
                    $('.wrapper_doughnut').removeClass('loadding');
                    element_select.removeClass('disable');
                }
            });
        });

        Chart.pluginService.register({
            beforeDraw: function (chart) {
                if (chart.config.options.elements.center) {
                    //Get ctx from string
                    var ctx = chart.chart.ctx;

                    //Get options from the center object in options
                    var centerConfig = chart.config.options.elements.center;
                    var fontStyle = centerConfig.fontStyle || 'Arial';
                    var txt = centerConfig.text;
                    var color = centerConfig.color || '#000';
                    var sidePadding = centerConfig.sidePadding || 20;
                    var sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2);
                    //Start with a base font of 30px
                    ctx.font = "40px " + fontStyle;

                    //Get the width of the string and also the width of the element minus 10 to give it 5px side padding
                    var stringWidth = ctx.measureText(txt).width;
                    var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

                    // Find out how much the font can grow in width.
                    var widthRatio = elementWidth / stringWidth;
                    var newFontSize = Math.floor(20 * widthRatio);
                    var elementHeight = (chart.innerRadius * 2);

                    // Pick a new font size so it will not be larger than the height of label.
                    var fontSizeToUse = Math.min(newFontSize, elementHeight);

                    //Set font settings to draw it correctly.
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                    var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
                    ctx.font = fontSizeToUse + "px " + fontStyle;
                    ctx.fillStyle = color;

                    //Draw text in center
                    ctx.fillText(txt, centerX, centerY);
                }
            }
        });
    },
    canvas_map: function () {
        if (!$('#geo_map_canvas').length > 0) {
            return;
        }
        var datas = {};
        var values_static = [];
        if ((typeof res_visit !== "undefined") && res_visit) {
            datas = jQuery.parseJSON(res_visit);
            values_static = jQuery.parseJSON(res_visit_color);
        }

        var map_static = new jvm.Map({
            container: $('#geo_map_canvas'),
            map: 'world_mill_en',
            panOnDrag: true,
            focusOn: {
                x: 0.5,
                y: 0.5,
                scale: 1,
                animate: true
            },
            series: {
                regions: [{
                    attribute: 'fill',
                    normalizeFunction: 'polynomial',
                    values: values_static
                }]
            },
            regionStyle: {
                initial: {
                    fill: '#AAAAAA',
                    "fill-opacity": 1,
                    stroke: 'none',
                    "stroke-width": 0,
                    "stroke-opacity": 1
                },
                hover: {
                    "fill-opacity": 0.8,
                    cursor: 'pointer'
                },

            },
            backgroundColor: '#fff',
            onRegionTipShow: function (event, tip, code) {
                tip.html(tip.html() + '(' + (typeof datas[code] === "undefined" ? '0' : datas[code]) + ')');
            },
        });

        $(document).on('click', '.geo_maps_wrapper .geo_filter_dropdown .dropdown-menu a', function (e) {
            e.preventDefault();
            var form_static = $('.geo_maps_wrapper');
            var select_click = $(this);
            if (form_static.length > 0) {
                if (form_static.hasClass('loadding')) {
                    return false;
                }
                form_static.addClass('loadding');
                select_click.addClass('disable');
                var i = {
                    get_maps: true,
                };
                var geo_map_filter = $(this).attr('data-value');
                $.extend(i, {geo_option_filter: geo_map_filter});
                $url_action = $('.form_static').attr('action');
                $.ajax({
                    url: $url_action + '&submit_ajax=1&ajax=1',
                    data: i,
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if (json.res_visit && json.res_visit.length !== 0) {
                            datas = json.res_visit;
                            var res_visit_color = json.res_visit_color;
                            map_static.series.regions[0].clear();
                            map_static.series.regions[0].setValues(res_visit_color);
                            form_static.removeClass('loadding');
                            select_click.removeClass('disable');
                            $('.wrap_maps_filter').removeClass('no_data');
                        }
                        if (json.total_line && json.total_line.length !== 0) {
                            $('.maps_total_statics').removeClass('no_data');
                            var html = '';
                            html += '<div class="maps_total_statics">';
                            $.each(json.total_line, function (key, value) {
                                html += '<div class="box_best">';
                                html += '<span class="label_total_map">' + value.name + '</span> ' + value.percent + '%';
                                html += '<div class="box_total_maps">';
                                html += '<span class="line_main"></span>';
                                html += '<span class="extra" style="width:' + value.percent + '%;background-color:' + value.color + ';"></span>';
                                html += '</div>';
                                html += '</div>';
                            });
                            html += '</div>';
                            $('.maps_total_statics').replaceWith(html);
                        } else {
                            $('.wrap_maps_filter').addClass('no_data');
                        }
                    },
                    error: function (error) {
                    }
                });
            }
        });
    },
    showSaveMessage: function (message, type) {

        if ($('.ets_geo_alert').length > 0) {
            $('.ets_geo_alert').remove();
        }

        if ($('.ets_geo_alert').length <= 0) {
            $('.back_end_ets_geo').append('<div class="ets_geo_alert hidden"></div>');
        }
        $('.ets_geo_alert').addClass('hidden').removeClass('error').removeClass('success').addClass(type == 'error' ? 'error' : 'success alert alert-success').html(message).removeClass('hidden');
        if (type != 'error') {
            setTimeout(function () {
                $('.ets_geo_alert').addClass('hidden');
            }, 10000);
        }
    },
    click_download_file: function () {
        $(document).on('click', '.back_end_ets_geo .auto_upload', function (ev) {
            ev.preventDefault();
            var element_click = $(this);
            var wrap_backend = $('.back_end_ets_geo');
            if (!element_click.hasClass('disable') && $(this).attr('href')) {
                $('.ets_geolocationsettings .error_download').remove();
                wrap_backend.addClass('loadding_wrap');
                element_click.addClass('disable');
                $.ajax({
                    url: $(this).attr('href') + '&ajax=true',
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if (!json.error) {
                            ADMIN_GEO.showSaveMessage(json.message, 'success');
                            $('.ets_geolocationsettings .bootstrap').remove();
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        } else if ($('.ets_geolocationsettings .defaultForm').length > 0) {
                            $('.ets_geolocationsettings .defaultForm').before(json.error).prev('.bootstrap').addClass('error_download');
                        }
                        element_click.removeClass('disable');
                        wrap_backend.removeClass('loadding_wrap');
                    },
                    error: function (error) {
                        element_click.removeClass('disable');
                        wrap_backend.removeClass('loadding_wrap');
                    }
                });
            }

        });
    }

};

var ADMIN_READY = {
    init: function () {
        this.show_hidden_swich();
        this.tick_check_group();
    },
    show_hidden_swich: function () {
        ADMIN_READY.check_switch_logged();
        $(document).on('change', 'input[name="ETS_GEO_ENABLE_SWITCH"]', function () {
            ADMIN_READY.check_switch_logged();
        });
    },
    check_switch_logged: function () {
        if (parseInt($('input[name="ETS_GEO_ENABLE_SWITCH"]:checked').val()) == 1) {
            $('input[name="ETS_GEO_DISABLE_SWITCH_LOGGED"]').closest('.form-group').first().show();
        } else {
            $('input[name="ETS_GEO_DISABLE_SWITCH_LOGGED"]').closest('.form-group').first().hide();
        }
    },
    check_tick_all: function () {
        var input_group = $('input[name="countries[]"]');
        if (input_group.length > 0) {
            var check = true;
            input_group.each(function (index, value) {
                if (!$(this).is(":checked")) {
                    check = false;
                }
            });

            if (check) {
                $('input[name="all_countries"]').prop('checked', true);
            } else {
                $('input[name="all_countries"]').prop('checked', false);
            }
        }
    },
    tick_check_group: function () {
        var input_group = $('input[name="countries[]"]');
        if (input_group.length > 0) {
            $(document).on('change', input_group, function () {
                ADMIN_READY.check_tick_all();
            });
        }
    },
};

$(document).ready(function () {
    ADMIN_READY.init();
    if ($('.bootstrap .alert.alert-success').length > 0) {
        setTimeout(function () {
            $('.bootstrap .alert.alert-success').hide();
        }, 3500);
    }
});
$(window).load(function () {
    ADMIN_GEO.init();
});
