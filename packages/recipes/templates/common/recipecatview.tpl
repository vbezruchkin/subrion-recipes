<h1>{$gTitle}</h1>

{if count($recipes)>0}
	{include file="all-items-page.tpl" all_items=$recipes all_item_fields=$fields all_item_type="recipes"}

	{navigation aTotal=$total_recipes aTemplate=$aTemplate aItemsPerPage=$aItemsPerPage aNumPageItems=5 aTruncateParam=1}
{else}
	<p>{lang key="recipes_not_added"}</p>
{/if}