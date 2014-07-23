<h1>{$gTitle}</h1>

{if $cookbooks}
	{foreach from=$cookbooks item=cookbook}
		{include file="all-items-page.tpl" all_items=$cookbooks all_item_fields=$fields all_item_type="cookbooks"}
	{/foreach}
{else}
	<p>{lang key="my_cookbooks_not_added"}</p>
{/if}

{ia_hooker name="smartyMyCookbooksBeforeFooter"}

{ia_print_js files="jquery/plugins/lightbox/jquery.lightbox"}