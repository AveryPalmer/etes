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
var popup_is_load = popup_is_load || false,
    ajax_url = ajax_url || false,
    page_controller = page_controller || 'index';

var FRONT_GEO = {
    init: function () {
        if (page_controller != 'block') {
            this.geo_process();
            this.isMobile();
        }
    },
    request: function (newUrl) {
        var url = newUrl || ajax_url;
        return url + (url.indexOf('?') != -1 ? '&' : '?');
    },
    geo_process: function () {
        if (!ajax_url || popup_is_load) {
            return false;
        }
        $.ajax({
            url: FRONT_GEO.request() + 'geo_auto_processing=1',
            type: 'post',
            dataType: 'json',
            success: function (json) {
                if (json) {
                    if (json.link_block) {
                        window.location.href = json.link_block;
                    } else {
                        if (json.html) {
                            FRONT_GEO.show_popup(json.html, 'ets_geo_popup');
                        }
                        if (json.link_reload) {
                            setTimeout(function () {
                                var link = $('[hreflang="' + json.link_reload + '"]').attr('href');
                                if (typeof link !== "undefined")
                                    window.location.href = link;
                                else
                                    window.location.reload();
                            }, 1500);
                        }
                    }
                }
            },
        });
    },
    show_popup: function (content, class_content) {
        $('body').append(content);
        $('.' + class_content + '').addClass('active');
    },
    process_choose: function (state) {
        if (!state.id) {
            return state.text;
        }
        var link_image = state.element.attributes.imgflag.value;
        var $state = $(
            '<span><img src="' + link_image + '" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    },
    isMobile: function () {
        var $window = $(window);
        var checkWidth = function () {
            var windowsize = $window.width();
            if (windowsize < 768) {
                if ($('#mobile_top_menu_wrapper .ets_click_show').length > 0) {
                    return false;
                }
                var content_move = $('.ets_click_show').clone();
                $('#mobile_top_menu_wrapper').append(content_move);
            }
        }
        checkWidth();
        $(window).resize(checkWidth);
    },
    geo_loaded: function () {
        $('.ets_geo_popup_choose.active, .ets_geo_popup.active').removeClass('active');
        if (ajax_url) {
            $.ajax({
                url: FRONT_GEO.request() + 'geo_loaded=1',
                type: 'post',
                dataType: 'json',
            });
        }
    }
};

$(document).ready(function () {
    FRONT_GEO.init();
    $(document).on('click', '.ets_geo_popup .yes_ok', function (evt) {
        evt.preventDefault();
        var btn = $(this);
        if (btn.attr('href') && !btn.hasClass('active')) {
            btn.addClass('active');
            $.ajax({
                url: FRONT_GEO.request(btn.attr('href')) + 'geo_confirm=1',
                type: 'post',
                dataType: 'json',
                success: function (json) {
                    btn.removeClass('active');
                    $('.ets_geo_popup_choose').removeClass('active');
                    if (json.link_reload) {
                        var link = $('[hreflang="' + json.link_reload + '"]').attr('href');
                        if (typeof link !== "undefined")
                            window.location.href = link;
                        else
                            window.location.reload();
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });
    $(document).on('click', '.ets_geo_popup .no_ok, .ets_geo_close_popup:not(.is_block)', function (evt) {
        evt.preventDefault();
        if (!$('.ets_geo_popup_choose').length) {
            FRONT_GEO.geo_loaded();
        } else {
            $('.ets_geo_popup_choose.active').removeClass('active');
        }
    });
    $(document).on('click', '.ets_geo_close_popup.is_block', function (evt) {
        $('.ets_geo_popup.active').removeClass('active');
        return false;
    });
    $(document).on('click', '.ets_geo_popup_content, .select2-container', function (e) {
        e.stopPropagation();
    });
    $(document).on('click', '.ets_geo_popup_choose', function (e) {
        $('.ets_geo_popup_choose').removeClass('active');
    });
    $(document).on('click', '.ets_click_show', function (evt) {
        evt.preventDefault();
        if ($('.ets_geo_popup_choose').eq(0).length > 0) {
            $('.ets_geo_popup_choose').eq(0).addClass('active');
        } else {
            var btn = $(this);
            if ($('body.geo_location_loading').length <= 0) {
                $('body').addClass('geo_location_loading');
                $.ajax({
                    url: FRONT_GEO.request() + 'geo_country_selected=1',
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        $('body').removeClass('geo_location_loading');
                        if (json) {
                            btn.removeClass('running');
                            FRONT_GEO.show_popup(json.content_pop_choose, 'ets_geo_popup_choose');
                            $(".ets_chosen-select").select2({
                                templateSelection: FRONT_GEO.process_choose
                            });
                        }
                    },
                    error: function () {
                        $('body').removeClass('geo_location_loading');
                    }
                });
            }
        }
    });
    $(document).on('click', '.ets_geo_btn_submit_apply', function (evt) {
        evt.preventDefault();
        var btn = $(this);
        if (!btn.hasClass('active')) {
            btn.addClass('active');
            $('.ets_geo_popup_choose').removeClass('active');
            $.ajax({
                url: FRONT_GEO.request() + 'geo_selected_country=1',
                data: {
                    'country_id': $('.ets_chosen-select').find('option:selected').val(),
                },
                type: 'post',
                dataType: 'json',
                success: function (json) {
                    btn.removeClass('active');
                    if (json) {
                        if (json.link_block) {
                            window.location.href = json.link_block;
                        }
                        if (json.link_reload) {
                            var link = $('[hreflang="' + json.link_reload + '"]').attr('href');
                            if (typeof link !== "undefined")
                                window.location.href = link;
                            else
                                window.location.reload();
                        }
                    }
                },
                error: function () {
                    btn.removeClass('active');
                }
            });
        }
    });
});