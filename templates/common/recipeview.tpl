<h1>{$gTitle}</h1>

{if !empty($cookbook)}
	<div class="recipe_info">
		{lang key="cookbook"}: <a href="{goToItem item=$cookbook itemtype=cookbooks noimage=true}" class="b">{$cookbook.title}</a>
	</div>
{/if}

<p><span class="b">{lang key="ingredients"}:</span> {$recipe.ingredients}</p>
<p><span class="b">{lang key="procedures"}:</span> {$recipe.procedures}</p>

{if !empty($recipe.notes)}
	<p>{lang key="notes"}: {$recipe.notes}</p>
{/if}

<div class="info">
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style " style="float: left; vertical-align: middle;">
	<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4d5bdfc85920cceb" class="addthis_button_compact">Share</a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4d5bdfc85920cceb"></script>
	<!-- AddThis Button END -->

	&nbsp;|&nbsp;<img src="{$smarty.const.IA_TPL_URL}img/user.png" 
		alt="{$recipe.title}" title="{$recipe.account_fullname}"/>
	{if $recipe.account_username}
		<a href="{$smarty.const.IA_URL}accounts/info/{$recipe.account_username}.html">{$recipe.account_fullname}</a>
	{else}
		<i>{lang key="guest"}</i>
	{/if}
	
	&nbsp;|&nbsp;<img src="{$smarty.const.IA_TPL_URL}img/calendar.png" alt="" />
	{$recipe.date_added|date_format:$config.date_format}
	
	{if $recipe.views > 0}&nbsp;|&nbsp; <img src="{$smarty.const.IA_TPL_URL}img/chart.png" alt="" /> {$recipe.views} {lang key="views"}{/if}

	{if $smarty.const.IN_USER AND $smarty.session.user.id eq $recipe.account_id}
		&nbsp;|&nbsp;<img src="{$smarty.const.IA_TPL_URL}img/edit_16.png" alt="{lang key="edit"}" />
		<a href="{$gPackages.recipes.url}edit/?id={$recipe.id}" rel="nofollow">{lang key="edit"}</a>
	{/if}

	{if $smarty.const.IN_USER && $smarty.session.user.id neq $recipe.account_id}
		&nbsp;|&nbsp;{printFavorites item=$recipe itemtype=recipes}		
	{/if}
</div>

{ia_hooker name="smartyViewRecipeBeforeFooter"}