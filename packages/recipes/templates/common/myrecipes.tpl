<h1>{$gTitle}</h1>

{if $recipes}
	{foreach from=$recipes item=recipe}
		{include file="all-items-page.tpl" all_items=$recipes all_item_fields=$fields all_item_type="recipes"}
	{/foreach}
{else}
	<p>{lang key="my_recipes_not_added"}</p>
{/if}

{ia_hooker name="smartyMyRecipesBeforeFooter"}

{ia_print_js files="jquery/plugins/lightbox/jquery.lightbox"}