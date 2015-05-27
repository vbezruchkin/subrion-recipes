<h1>{lang key="page_title_recipe_add"}</h1>

<div id="recipe_alert" class="message error" {if !isset($msg) || !$msg}style="display:none"{/if}><ul></ul></div>

<form action="{$smarty.const.IA_PACKAGE_URL}add/{if isset($smarty.get.id)}?id={$smarty.get.id}{/if}" method="post" enctype="multipart/form-data" id="recipe_form">

	{preventCsrf}
	
	{include file="plans.tpl" item=$item}
	
	{assign var="item" value=$item}
	{foreach from=$sections item=s key=key}
		{if $s.fields}
			{if $key neq '___empty___'}
				{assign var="grouptitle" value="fieldgroup_"|cat:$s.name}
			{else}
				{assign var="grouptitle" value="other"}
			{/if}
			<fieldset class="collapsible">
				<legend>{lang key=$grouptitle}</legend>
				{foreach from=$s.fields item=f}
					{include file="field-type-content.tpl" variable=$f isSuggest=1 isEdit=1}
				{/foreach}
			</fieldset>
		{/if}
	{/foreach}

	<p class="field">
		<input type="submit" class="button" name="change_info" value="{lang key='save_changes'}" />
		{if isset($smarty.get.id)}
		<input type="submit" class="button" name="delete_recipe" value="{lang key='delete'}" />
		{/if}
	</p>
</form>

{ia_print_js files="jquery/plugins/jquery.form,jquery/plugins/jquery.block,jquery/plugins/jquery.textcounter,_IA_URL_packages/recipes/js/front/submit"}