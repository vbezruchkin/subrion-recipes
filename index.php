<?php
//##copyright##

define("INTELLI_REALM", $iaCore->get_cfg('name'));

if(SMARTY)
{
	// get account recipes	
	if ('my_recipes' == INTELLI_REALM)
	{
		if (!IN_USER)
		{
			iaUtil::errorPage('403');
		}
		
		$fields	= $iaCore->getAcoFieldsList(false, false, '', true);
		$iaCore->assign_by_ref('fields', $fields);
		
		$iaRecipe = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'recipe');
		$recipes = $iaRecipe->getRecipesByAccount($_SESSION['user']['id'], 0, 10);
		$iaCore->assign('recipes', $recipes);
		
		$iaCore->startHook('phpMyRecipesBeforeStart', array('item' => 'recipe'));
		
		$iaCore->display('myrecipes');
	}
	elseif ('my_cookbooks' == INTELLI_REALM)
	{
		if (!IN_USER)
		{
			iaUtil::errorPage('403');
		}
		
		$fields	= $iaCore->getAcoFieldsList(false, 'cookbooks', '', true);
		$iaCore->assign_by_ref('fields', $fields);
		
		// get account cookbooks
		$iaCookbook = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'cookbook');
		$cookbooks = $iaCookbook->getCookbooksByAccount($_SESSION['user']['id'], 0, 10);
		$iaCore->assign('cookbooks', $cookbooks);
		
		$iaCore->startHook('phpMyCookbooksBeforeStart', array('item' => 'cookbook'));

		$iaCore->set_cfg('title', $iaCore->get_cfg('name'));

		$iaCore->display('mycookbooks');
	}
	elseif ('recipes_home' == INTELLI_REALM)
	{
		$fields	= $iaCore->getAcoFieldsList(false, false, '', true);
		$iaCore->assign_by_ref('fields', $fields);
		
		$iaCore->display();
	}
}