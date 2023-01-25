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
<!doctype html>
<html lang="{$language.iso_code|escape:'html':'UTF-8'}">
    <head>
        <style>
            .wrapper_block{
                float: left;
                width: 100%;
                position: fixed;
                height: 100%;
            }
            .wrapper_block .wrapper_entry{
                float: left;
                width: 100%;
                height: 100%;
                position: relative;
            }
            .wrapper_block .wrapper_entry .wrapper_content{
                display: inline-block;
                position: absolute;
                text-align: center;
                min-width: 500px;
                max-width: 95%;
                left: 50%;
                top: 50%;
                -webkit-transform: translate(-50%, -50%);
                -moz-transform: translate(-50%, -50%);
                -ms-transform: translate(-50%, -50%);
                -o-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
                padding: 50px 15px;
            }
            .wrapper_block .wrapper_entry .wrapper_content .logo{
                margin-bottom: 15px;
            }
            h1{
                color: #333;
                font: 24px sans-serif;
                margin: 0;
            }
        </style>
    </head>
    <body id="{$page.page_name}" class="{$page.body_classes|classnames}">
        <main>
            <header class="header">
            </header>
            <section class="wrapper_block">
                <div class="wrapper_entry">
                    <div class="wrapper_content">
                        {block name='page_header_logo'}
                            <div class="logo"><img src="{$shop.logo|escape:'html':'UTF-8'}" alt="logo"></div>
                        {/block}
                        {if isset($block_message) && $block_message}
                            <h1>{$block_message|escape:'html':'UTF-8'}</h1>
                        {/if}
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>