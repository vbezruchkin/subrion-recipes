<?php
//##copyright##

function sitemap_recipes($tpl = '<url><loc>{url}</loc></url>')
{
	$iaCore = iaCore::instance();
	$iaDb = &$iaCore->iaDb;

	$iaRecipe = $iaCore->factoryPackages('recipes', 'front', 'recipe');
	$text = '';

	$recipes = $iaRecipe->getRecipes();
	foreach($recipes as $row)
	{
		list($url, $output) = $iaRecipe->goToItem(array('item' => $row));
		$text .= str_replace(array('{url}', '{site}'), array($url, ''), $tpl);
	}
	return $text;
}