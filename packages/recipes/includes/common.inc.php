<?php
if (IN_FRONT)
{
	$sql  = "SELECT t1.`id`, t1.`title`, t1.`title_alias`, t1.`date_added`, t1.`category_alias`, t1.`views` ";
	$sql .= "FROM `{$iaDb->mPrefix}recipes` t1 ";
	$sql .= "WHERE t1.`status`='active' ";
	
	if (in_array('random_recipes', $iaCore->gBlocks))
	{
		$sql2  = "ORDER BY RAND() LIMIT 0, ".$iaCore->get('recipes_per_block', 5);
		$iaCore->assign('random_recipes', $iaDb->getAll($sql.$sql2));
	}
	if (in_array('latest_recipes', $iaCore->gBlocks))
	{
		$sql2  = "ORDER BY t1.`date_added` DESC LIMIT 0, ".$iaCore->get('recipes_per_block', 5);
		$iaCore->assign('latest_recipes', $iaDb->getAll($sql.$sql2));
	}
	if (in_array('popular_recipes', $iaCore->gBlocks))
	{
		$sql2  = "ORDER BY t1.`views` DESC LIMIT 0, ".$iaCore->get('recipes_per_block', 5);
		$iaCore->assign('popular_recipes', $iaDb->getAll($sql.$sql2));
	}
	
	$gI18N['no_recipes'] = str_replace('{%URL%}', $iaCore->gPackages['recipes']['url'] . 'add/', $gI18N['no_recipes']);
	
	$iaRecipe = $iaCore->factoryPackages('recipes', 'front', 'recipe');
	$recipes = $iaRecipe->getRecipes(false, 0, $iaCore->get('recipes_per_block', 5));
	if($recipes)
	{
		$recipes = $iaCore->updateItemsFavorites($recipes, 'recipes');
	}
	$iaCore->assign('new_recipes', $recipes);
	
	// Recipes categories
	$iaCore->iaDb->setTable('recipecats');
	$recipecats_list = $iaCore->iaDb->all("*", "`status` = 'active' ORDER BY `title` ASC");
	$iaCore->iaDb->resetTable();
	$iaCore->assign_by_ref('recipecats_list', $recipecats_list);
	
	// get categories, cookbooks, recipes statistics
	if (in_array('whoisonline', $iaCore->gBlocks))
	{
		$items = array('recipecats', 'cookbooks', 'recipes');
		foreach($items as $item)
		{
			$stats[$item] = $iaDb->one("COUNT(*)", "`status` = 'active'", 0, null, $item);
		}
		$iaCore->assign('stats', $stats);
	}
	
	// Top cookbooks & New cookbooks
	if (in_array('top_cookbooks', $iaCore->gBlocks))
	{
		$iaAlbum = $iaCore->factoryPackages('recipes', 'front', 'cookbook');
		$top_cookbooks = $iaAlbum->getTopAlbums();
		$iaCore->assign('top_cookbooks', $top_cookbooks);		
	}
	if (in_array('new_cookbooks', $iaCore->gBlocks))
	{
		$iaAlbum = $iaCore->factoryPackages('recipes', 'front', 'cookbook');
		$new_cookbooks = $iaAlbum->getLatestAlbums();
		$iaCore->assign('new_cookbooks', $new_cookbooks);		
	}
}