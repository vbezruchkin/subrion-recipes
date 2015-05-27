<h1>{$gTitle}</h1>

<div id="ia-tab-container">
	<ul>
		<li><a href="#ia-tab-container-1"><span>{lang key="general_info"}</span></a></li>
		{if $pictures_sections}
			<li><a href="#ia-tab-container-2"><span>{lang key="pictures_info"}</span></a></li>
		{/if}
		{ia_hooker name="tabTitle"}
	</ul>
	<div id="ia-tab-container-1">
		<div style="clear:both;"></div>
		
		{assign var="item" value=$genre}
		{foreach from=$sections item=s key=key}
			{if $s.fields}
				{if $key neq '___empty___'}
					{assign var="grouptitle" value="fieldgroup_"|cat:$s.name}
				{else}
					{assign var="grouptitle" value="other"}
				{/if}
				<fieldset class="collapsible">
					<legend>{lang key="$grouptitle"}</legend>
					{foreach from=$s.fields item=f}
						{include file="field-type-content.tpl" variable=$f isView=1}
					{/foreach}
				</fieldset>
			{/if}
		{/foreach}
		<div style="clear:both;margin-bottom:10px;"></div>
	</div>
	{if isset($pictures_sections)}
		<div id="ia-tab-container-2">
			{assign var="item" value=$genre}
			{foreach from=$pictures_sections item=s key=key}
				{if $s.fields}
					{if $key neq '___empty___'}
						{assign var="grouptitle" value="fieldgroup_"|cat:$s.name}
					{else}
						{assign var="grouptitle" value="other"}
					{/if}
					<fieldset class="collapsible">
						<legend>{lang key="$grouptitle"}</legend>
						{foreach from=$s.fields item=f}
							{include file="field-type-content.tpl" variable=$f isView=1}
						{/foreach}
					</fieldset>
				{/if}
			{/foreach}
			<div style="clear:both;margin-bottom:10px;"></div>
		</div>
	{/if}
	
	{ia_hooker name="tabContent"}
</div>

<h2>{lang key="lyrics"}</h2>
{if $lyrics}
	{include file="all-items-page.tpl" all_items=$lyrics all_item_fields=$fields all_item_type="lyrics"}
{else}
	<p>{lang key="genre_lyrics_not_added"}</p>
{/if}

{ia_hooker name="smartyViewGenreBeforeFooter"}

{ia_print_js files="jquery/plugins/lightbox/jquery.lightbox, frontend/bkmk"}