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
<div class="geo_block_user">
	<div class="geo_block_wrap">
		<div class="geo_block_user_content">
			<p class="geo_msg alert alert-danger">
				{if isset($msg) && $msg}{$msg nofilter}{/if}
			</p>
		</div>
	</div>
</div>

<style>
	*{
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
	.geo_block_user{
		position: fixed;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
	}
	.geo_block_wrap{
		position: relative;
		width: 100%;
		height: 100%;
	}
	.geo_block_user_content{
		position: absolute;
		left: 50%;
		top: 50%;
		-webkit-transform: translate(-50%,-50%);
		-moz-transform: translate(-50%,-50%);
		-ms-transform: translate(-50%,-50%);
		-o-transform: translate(-50%,-50%);
		transform: translate(-50%,-50%);
	}
	.geo_block_user_content p {
		margin: 0;
		padding: 15px 50px;
		color: #721c24;
		background-color: #f8d7da;
		border-color: #f5c6cb;
		font-size: 22px;
	}
</style>