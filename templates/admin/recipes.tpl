{include file="box-header.tpl" title=$gTitle}

<form method="post" enctype="multipart/form-data">
	{preventCsrf}
	
	<table width="100%" cellspacing="0" cellpadding="0" class="striped">
	{if isset($fields_groups)}
		{foreach from=$fields_groups item="groupvalue"}
			{if isset($groupvalue.fields)}
				<tr><td colspan="2" class="caption"><strong>[ {if isset($groupvalue.name) && $groupvalue.name neq "___empty___"}{assign var=temp value=$groupvalue.name}{lang key="fieldgroup_$temp"}{else}{lang key="other"}{/if} ]</strong></td></tr>

				{if 'recipes_general' == $groupvalue.name}
			<tr>
				<td class="t1">{lang key="account"}:</td>
				<td><input class="common" type="text" name="account" id="account" value="{if isset($recipe.account)}{$recipe.account}{/if}" size="45"/></td>
			</tr>
			<tr>
				<td class="t1">{lang key="cookbook"}:</td>
				<td><select name="cookbook" id="cookbook">
					{if !empty($cookbooks)}
						{foreach from=$cookbooks item=cookbook}
							<option value="{$cookbook.id}"{if $cookbook.id == $recipe.id_cookbook}selected="selected" {/if}>{$cookbook.title}</option>
						{/foreach}
					{else}
						<option value=""> - please choose account first - </option>
					{/if}
					</select>
				</td>
			</tr>
			<tr>
				<td class="t1">{lang key="recipecats"}:</td>
				<td>
				{if !empty($recipecats)}
					{foreach from=$recipecats item=recipecat}
						<div style="float: left; width: 100px; white-space: nowrap;"><input type="checkbox" name="recipecats[]" id="recipecats[{$recipecat.id}]" value="{$recipecat.id}" {if in_array($recipecat.id, $recipe.recipecats)}checked="checked" {/if}/> <label for="recipecats[{$recipecat.id}]">{$recipecat.title}</label></div>
					{/foreach}
				{else}
					{lang key="recipecats_not_added"}
				{/if}
				</td>
			</tr>
				{/if}
				
				{printFieldContents items=$recipe fields=$groupvalue.fields suggest="true"}
			{/if}
		{/foreach}
	{/if}
	<tr>
		<td colspan="2" class="caption"><strong>[ {lang key="system_fields"} ]</strong></td>
	</tr>
	<tr>
		<td class="t1">{lang key="title_alias"}:</td>
		<td><input class="common" style="float: left;" type="text" name="title_alias" id="title_alias" value="{if isset($recipe.title_alias)}{$recipe.title_alias}{/if}" size="45"/>
			<div style="float: left; display: none; margin-left: 3px; padding: 4px;" id="title_box"><span>{lang key="alias_url_will_be"}:&nbsp;<span><span id="title_url" style="padding: 3px; margin: 0; background: #FFE269;"></span></div>
		</td>
	</tr>
	<tr>
		<td class="t1">{lang key="status"}:</td>
		<td>
			<select name="status">
				<option value="active" {if isset($recipe.status) && $recipe.status eq 'active'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'active'}selected="selected"{/if}>{lang key="active"}</option>
				<option value="approval" {if isset($recipe.status) && $recipe.status eq 'approval'}selected="selected"{elseif isset($smarty.post.status) && $smarty.post.status eq 'approval'}selected="selected"{/if}>{lang key="approval"}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><input type="submit" class="common" value="{lang key='save'}" /></td>
		<td>{if 'add_recipe' eq $smarty.const.INTELLI_REALM}
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
var item = 'recipes';
</script>

{ia_print_js files="jquery/plugins/jquery.autocomplete,_IA_URL_packages/recipes/js/admin/footer"}

{ia_print_css files="_IA_URL_templates/default/css/jquery.autocomplete"}