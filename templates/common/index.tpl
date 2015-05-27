<h1>{$gTitle}</h1>

{if $new_recipes}
	{include file="all-items-page.tpl" all_items=$new_recipes all_item_fields=$fields all_item_type="recipes"}
	
	{navigation aTotal=$aTotal aTemplate=$aTemplate aItemsPerPage=$aItemsPerPage aNumPageItems=5 aTruncateParam=1}
{else}
	<p>{lang key="recipes_not_added"}</p>
{/if}