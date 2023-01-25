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
<script type="text/javascript">
var text_update_position='{l s='Successful update' mod='ets_geolocation'}';
</script>
<div class="panel ets-geo-panel{if isset($class)} {$class|escape:'html':'UTF-8'}{/if}">
    <div class="panel-heading">
        <span class="heading_title">{$title nofilter}</span>
        {if isset($totalRecords) && $totalRecords>0}<span class="badge ets_badge">{$totalRecords|intval}</span>{/if}
        <span class="panel-heading-action">
            {if !isset($show_add_new) || isset($show_add_new) && $show_add_new}
                <a class="list-toolbar-btn" href="{$currentIndex|escape:'html':'UTF-8'}">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Add new' mod='ets_geolocation'}" class="label-tooltip" data-toggle="tooltip" title="">
        				<i class="process-icon-new"></i>
                    </span>
                </a>
            {/if}
            {if isset($preview_link) && $preview_link}
                <a target="_blank" class="list-toolbar-btn" href="{$preview_link|escape:'html':'UTF-8'}">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Preview ' mod='ets_geolocation'} ({$title|escape:'html':'UTF-8'})" class="label-tooltip" data-toggle="tooltip" title="">
        				<i style="margin-left: 5px;" class="icon-search-plus"></i>
                    </span>
                </a>
            {/if}
        </span>
    </div>
    {if $fields_list}
        <div class="table-responsive clearfix">
            <form method="post" action="{$currentIndex|escape:'html':'UTF-8'}&list=true">
                <table class="table configuration">
                    <thead>
                        <tr class="nodrag nodrop">
                            {if $name=='ybc_comment' && count($field_values)}
                                <script type="text/javascript">
                                    var detele_confirm ="{l s='Do you want to delete this item?' mod='ets_geolocation'}";
                                </script>
                                <th class="fixed-width-xs">
                                    <span class="title_box">
                                        <input value="" class="message_readed_all" type="checkbox" />
                                    </span>
                                </th>
                            {/if}
                            {foreach from=$fields_list item='field' key='index'}
                                <th class="{$index|escape:'html':'UTF-8'}">
                                    <span class="title_box">
                                        {$field.title|escape:'html':'UTF-8'}
                                        {if isset($field.sort) && $field.sort}
                                            <a href="{$currentIndex|escape:'html':'UTF-8'}&sort={$index|escape:'html':'UTF-8'}&sort_type=desc&list=true{$filter_params nofilter}" {if isset($sort)&& $sort==$index && isset($sort_type) && $sort_type=='desc'} class="active"{/if}><i class="icon-caret-down"></i></a>
                                            <a href="{$currentIndex|escape:'html':'UTF-8'}&sort={$index|escape:'html':'UTF-8'}&sort_type=asc&list=true{$filter_params nofilter}" {if isset($sort)&& $sort==$index && isset($sort_type) && $sort_type=='asc'} class="active"{/if}><i class="icon-caret-up"></i></a>
                                        {/if}
                                    </span>
                                </th>
                            {/foreach}
                            {if $show_action}
                                <th style="text-align: center;">{l s='Action' mod='ets_geolocation'}</th>
                            {/if}
                        </tr>
                        {if $show_toolbar}
                            <tr class="nodrag nodrop filter row_hover">
                                {if $name=='ybc_comment' && count($field_values)}
                                    <th>&nbsp;</th>
                                {/if}
                                {foreach from=$fields_list item='field' key='index'}
                                    <th class="{$index|escape:'html':'UTF-8'}">
                                        {if isset($field.filter) && $field.filter}
                                            {if $field.type=='text'}
                                                <input class="filter" name="{$index|escape:'html':'UTF-8'}" type="text" {if isset($field.width)}style="width: {$field.width|intval}px;"{/if} {if isset($field.active)}value="{$field.active|escape:'html':'UTF-8'}"{/if}/>
                                            {/if}
                                            {if $field.type=='select' || $field.type=='active'}
                                                <select  {if isset($field.width)}style="width: {$field.width|intval}px;"{/if}  name="{$index|escape:'html':'UTF-8'}">
                                                    {if $index!='has_post'}
                                                        <option value=""> -- </option>
                                                    {/if}
                                                    {if isset($field.filter_list.list) && $field.filter_list.list}
                                                        {assign var='id_option' value=$field.filter_list.id_option}
                                                        {assign var='value' value=$field.filter_list.value}
                                                        {foreach from=$field.filter_list.list item='option'}
                                                            <option {if $field.active!=='' && $field.active==$option.$id_option} selected="selected" {/if} value="{$option.$id_option|escape:'html':'UTF-8'}">{$option.$value|escape:'html':'UTF-8'}</option>
                                                        {/foreach}
                                                    {/if}
                                                </select>
                                            {/if}
                                            {if $field.type=='int'}
                                                <label for="{$index|escape:'html':'UTF-8'}_min"><input type="text" placeholder="{l s='Min' mod='ets_geolocation'}" name="{$index|escape:'html':'UTF-8'}_min" value="{$field.active.min|escape:'html':'UTF-8'}" /></label>
                                                <label for="{$index|escape:'html':'UTF-8'}_max"><input type="text" placeholder="{l s='Max' mod='ets_geolocation'}" name="{$index|escape:'html':'UTF-8'}_max" value="{$field.active.max|escape:'html':'UTF-8'}" /></label>
                                            {/if}
                                        {else}
                                           {l s=' -- ' mod='ets_geolocation'}
                                        {/if}
                                    </th>
                                {/foreach}
                                {if $show_action}
                                    <th class="actions">
                                        <span class="pull-right">
                                            <input type="hidden" name="post_filter" value="yes" />
                                            {if $show_reset}<a  class="btn btn-warning"  href="{$currentIndex|escape:'html':'UTF-8'}&list=true"><i class="icon-eraser"></i> {l s='Reset' mod='ets_geolocation'}</a> &nbsp;{/if}
                                            <button class="btn btn-default" name="ybc_submit_{$name|escape:'html':'UTF-8'}" id="ybc_submit_{$name|escape:'html':'UTF-8'}" type="submit">
            									<i class="icon-search"></i> {l s='Filter' mod='ets_geolocation'}
            								</button>
                                        </span>
                                    </th>
                                {/if}
                            </tr>
                        {/if}
                    </thead>
                    {if $field_values}
                    <tbody id="list-{$name|escape:'html':'UTF-8'}">
                        {foreach from=$field_values item='row'}
                            <tr>
                                {foreach from=$fields_list item='field' key='key'}
                                    <td class="{$key|escape:'html':'UTF-8'} {if isset($sort)&& $sort==$key && isset($sort_type) && $sort_type=='asc' && isset($field.update_position) && $field.update_position}pointer dragHandle center{/if}" >
                                        {if $field.type != 'active'}
                                            {if isset($field.update_position) && $field.update_position}
                                                <div class="dragGroup">
                                                <span class="positions">
                                            {/if}
                                            {if isset($row.$key) && !is_array($row.$key)}{if isset($field.strip_tag) && !$field.strip_tag}{$row.$key nofilter}{else}{$row.$key|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}{/if}{/if}
                                            {if isset($row.$key) && is_array($row.$key) && isset($row.$key.image_field) && $row.$key.image_field}
                                                <a class="ybc_fancy" href="{$row.$key.img_url|escape:'html':'UTF-8'}"><img style="{if isset($row.$key.height) && $row.$key.height}max-height: {$row.$key.height|intval}px;{/if}{if isset($row.$key.width) && $row.$key.width}max-width: {$row.$key.width|intval}px;{/if}" src="{$row.$key.img_url|escape:'html':'UTF-8'}" /></a>
                                            {/if}
                                            {if isset($field.update_position) && $field.update_position}
                                                </div>
                                                </div>
                                            {/if}
                                        {else}
                                            {if $key=='priority' || $key =='countries'}
                                                {if isset($row.$key) && $row.$key}
                                                    {$row.$key|escape:'html':'UTF-8'}
                                                {/if}
                                            {/if}
                                            {if isset($row.$key) && $row.$key}
                                                <a name="{$name|escape:'html':'UTF-8'}"  href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&change_enabled=0&field={$key|escape:'html':'UTF-8'}" class="list-action field-{$key|escape:'html':'UTF-8'} list-action-enable action-enabled list-item-{$row.$identifier|escape:'html':'UTF-8'}" data-id="{$row.$identifier|escape:'html':'UTF-8'}" title="{if $key=='reported'}{l s='Click to unreported' mod='ets_geolocation'}{else}{l s='Click to disabled' mod='ets_geolocation'}{/if}"><i class="icon-check"></i></a>
                                            {else}
                                                <a name="{$name|escape:'html':'UTF-8'}" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&change_enabled=1&field={$key|escape:'html':'UTF-8'}" class="list-action field-{$key|escape:'html':'UTF-8'} list-action-enable action-disabled  list-item-{$row.$identifier|escape:'html':'UTF-8'}" data-id="{$row.$identifier|escape:'html':'UTF-8'}" title="{if $key=='reported'}{l s='Click to reported' mod='ets_geolocation'}{else}{l s='Click to enabled' mod='ets_geolocation'}{/if}"><i class="icon-remove"></i></a>
                                            {/if}
                                        {/if}
                                    </td>
                                {/foreach}
                                {if $show_action}
                                    <td class="text-right">
                                            <div class="btn-group-action">
                                                <div class="btn-group pull-right">
                                                    {if $name!='ybc_polls'}
                                                        {if isset($row.child_view_url) && $row.child_view_url}
                                                            <a class="btn btn-default" href="{$row.child_view_url|escape:'html':'UTF-8'}">{if $name=="ybc_category"}<i class="icon-search-plus"></i> {l s='Sub categories' mod='ets_geolocation'}{else}<i class="icon-search-plus"></i> {l s='View' mod='ets_geolocation'}{/if}</a>
                                                        {else}
                                                            <a class="edit btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}"><i class="icon-pencil"></i> {l s='Edit' mod='ets_geolocation'}</a>
                                                        {/if}
                                                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                    						<i class="icon-caret-down"></i>&nbsp;
                                    					</button>
                                                    {if in_array('delete',$actions) || (isset($row.view_url) && $row.view_url) || (isset($row.view_post_url) && $row.view_post_url)||(isset($row.delete_post_url) && $row.delete_post_url)}
                                                        <ul class="dropdown-menu">
                                                            {if isset($row.child_view_url) && $row.child_view_url}
                                                                <li><a class="edit" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}"><i class="icon-pencil"></i> {l s='Edit' mod='ets_geolocation'}</a></li>
                                                                <li class="divider"></li>
                                                            {/if}
                                                            {if isset($row.view_url) && $row.view_url}
                                                                <li><a target="_blank" href="{$row.view_url|escape:'html':'UTF-8'}"><i class="icon icon-external-link" aria-hidden="true"></i> {if isset($row.view_text) && $row.view_text} {$row.view_text|escape:'html':'UTF-8'}{else} {l s='View' mod='ets_geolocation'}{/if}</a></li>
                                                                <li class="divider"></li>
                                                            {/if}
                                                            {if isset($row.view_post_url) && $row.view_post_url}
                                                                <li><a target="_blank" href="{$row.view_post_url|escape:'html':'UTF-8'}"><i class="icon-search-plus"></i>{l s='View posts' mod='ets_geolocation'}</a></li>
                                                                <li class="divider"></li>
                                                            {/if}
                                                            {if isset($row.delete_post_url) && $row.delete_post_url}
                                                                <li><a onclick="return confirm('{l s='Do you want to delete posts?' mod='ets_geolocation'}');" href="{$row.delete_post_url|escape:'html':'UTF-8'}"><i class="icon-trash"></i>{l s='Delete all posts' mod='ets_geolocation'}</a></li>
                                                                <li class="divider"></li>
                                                            {/if}
                                                            {if in_array('delete',$actions)}
                                                                <li><a onclick="return confirm('{l s='Do you want to delete this item?' mod='ets_geolocation'}');" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&del=yes"><i class="icon-trash"></i> {l s='Delete' mod='ets_geolocation'}</a></li>
                                                            {/if}
                                                        </ul>
                                                    {/if}
                                                    {else}
                                                        <a class="edit btn btn-default" onclick="return confirm('{l s='Do you want to delete this item?' mod='ets_geolocation'}');" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&del=yes"><i class="icon-trash"></i> {l s='Delete' mod='ets_geolocation'}</a>
                                                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                    						<i class="icon-caret-down"></i>&nbsp;
                                    					</button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="send_mail_form" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&sendmailform=yes"><i class="icon-email"></i> {l s='Send email' mod='ets_geolocation'}</a></li>
                                                            <li class="divider"></li>
                                                            {if isset($row.id_user) && $row.id_user}
                                                                <li><a href="{$link_customer|escape:'html':'UTF-8'}&id_customer={$row.id_user|intval}"><i class="icon-user"></i> {l s='View customer' mod='ets_geolocation'}</a></li>
                                                                <li class="divider"></li>
                                                            {/if}
                                                        </ul>
                                                    {/if}
                                                </div>
                                            </div>
                                     </td>
                                {/if}
                            </tr>
                        {/foreach}
                    </tbody>
                    {/if}
                </table>
                {if !$field_values}
                    {l s='No items found' mod='ets_geolocation'}
                {/if}
                {if $paggination}
                    <div class="ets_geo_paggination" style="margin-top: 10px;">
                        {$paggination nofilter}
                    </div>
                {/if}
            </form>
        </div>
    {/if}
</div>
</span>
{if $name=='ybc_polls'}
    <div class="popup-form-send-email-polls">
        <div class="popup-form-send-email-polls-content">
        </div>
    </div>
{/if}