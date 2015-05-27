{include file="box-header.tpl" title=$gTitle}

<form method="post" enctype="multipart/form-data">
	{preventCsrf}
	
	<table width="100%" cellspacing="0" cellpadding="0" class="striped">
	{if isset($fields_groups)}
		{foreach from=$fields_groups item="groupvalue"}
			{if isset($groupvalue.fields)}
				<tr><td colspan="2" class="caption"><strong>[ {if isset($groupvalue.name) && $groupvalue.name neq "___empty___"}{assign var=temp value=$groupvalue.name}{lang key="fieldgroup_$temp"}{else}{lang key="other"}{/if} ]</strong></td></tr>
				
				{if 'cookbook_general' == $groupvalue.name}
	<tr>
		<td class="t1">{lang key="artist"}:</td>
		<td><input class="common" type="text" name="artist" id="artist" value="{if isset($cookbook.artist)}{$cookbook.artist}{/if}" size="45"/></td>
	</tr>
				{/if}
				
				{printFieldContents items=$cookbook fields=$groupvalue.fields suggest="true"}
			{/if}
		{/foreach}
	{/if}
	<tr>
		<td colspan="2" class="caption"><strong>[ {lang key="system_fields"} ]</strong></td>
	</tr>
	<tr>
		<td class="t1">{lang key="title_alias"}:</td>
		<td><input class="common" style="float: left;" type="text" name="title_alias" id="title_alias" value="{if isset($cookbook.title_alias)}{$cookbook.title_alias}{/if}" size="45"/>
			<div style="float: left; display: none; margin-left: 3px; padding: 4px;" id="title_box"><span>{lang key="alias_url_will_be"}:&nbsp;<span><span id="title_url" style="padding: 3px; margin: 0; background: #FFE269;"></span></div>		
		</td>
	</tr>
	<tr>
		<td class="t1">{lang key="account"}:</td>
		<td><input class="common" type="text" name="account" id="account" value="{if isset($recipe.account)}{$recipe.account}{/if}" size="45"/></td>
	</tr>
	<tr>
		<td class="t1">{lang key="status"}:</td>
		<td>
			<select name="status">
				<option value="active" {if isset($cookbook.status) && $cookbook.status eq 'active'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'active'}selected="selected"{/if}>{lang key="active"}</option>
				<option value="approval" {if isset($cookbook.status) && $cookbook.status eq 'approval'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'approval'}selected="selected"{/if}>{lang key="approval"}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><input type="submit" class="common" value="{lang key='save'}" /></td>
		<td>{if 'add_cookbook' eq $smarty.const.INTELLI_REALM}
			<span><b>&nbsp;{lang key="and_then"}&nbsp;</b></span>
			<select name="goto">
				<option value="list" {if isset($smarty.post.goto) && $smarty.post.goto eq 'list'}selected="selected"{/if}>{lang key="go_to_list"}</option>
				<option value="add" {if isset($smarty.post.goto) && $smarty.post.goto eq 'add'}selected="selected"{/if}>{lang key="add_another_one"}</option>
			</select>
			{/if}
		</td>
	</tr>
	</table>
</form>

{include file="box-footer.tpl"}

<script type="text/javascript">
var item = 'cookbooks';
</script>

{ia_print_js files="jquery/plugins/jquery.autocomplete,_IA_URL_packages/recipes/js/admin/footer"}

{ia_print_css files="_IA_URL_templates/default/css/jquery.autocomplete"}