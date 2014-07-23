<?php
//##copyright##

define('INTELLI_REALM', 'recipecat_view');

if (isset($vals[0]))
{
	$recipecat_alias = $vals[0];
	// TODO: perform param validation
}

$iaRecipecat = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'recipecat');
$recipecat = isset($recipecat_alias) ? $iaRecipecat->getRecipecatByAlias($recipecat_alias) : false;

if (empty($recipecat))
{
	iaCore::errorPage('404');
}
$iaCore->startHook('phpViewRecipecatBeforeStart', array('listing' => $recipecat['id'], 'item' => 'recipecats'));

$recipecat['@view'] = true;

// get sections
$sections = $iaCore->getAcoGroupsFields(false, false, "`f`.`type`<>'pictures'", $recipecat);
$pictures_sections = $iaCore->getAcoGroupsFields(false, false, "`f`.`type`='pictures'", $recipecat);

if($pictures_sections)
{
	foreach($pictures_sections as $onesection)
	{
		if(isset($onesection['fields']) && !empty($onesection['fields']) && is_array($onesection['fields']))
		{
			foreach($onesection['fields'] as $onefield)
			{
				if(isset($recipecat[$onefield['name']]) && !empty($recipecat[$onefield['name']]))
				{
					$iaCore->assign_by_ref('pictures_sections', $pictures_sections);
					break 2;
				}
			}
		}
	}
}
$recipecat['item'] = 'recipecats';
$iaCore->recipecat = $recipecat;

$iaCore->assign(array('recipecat' => $recipecat, 'sections' => $sections));

// get recipe fields
$fields = $iaCore->getAcoFieldsList(false, 'recipes', '', true);
$iaCore->assign_by_ref('fields', $fields);

// gets current page and defines start position
$num_index = 20;
$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$start = (max($page,1) - 1) * $num_index;

// get recipes by category
$iaRecipe = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'Recipe');
$recipes = $iaRecipe->getRecipesByCategory($recipecat['id'], $start, $num_index);
$total_recipes = $iaRecipe->getRecipesNumByCategory($recipecat['id']);
// update favorites icon
$recipes = !empty($recipes) ? $iaCore->updateItemsFavorites($recipes, 'recipes') : $recipes;
$iaCore->assign('recipes', $recipes);

// pagination
$iaCore->assign('aTemplate', IA_PACKAGE_URL .'categories/'. $recipecat['title_alias'].'/?page={page}');
$iaCore->assign('total_recipes', $total_recipes);
$iaCore->assign('recipes', $recipes);
$iaCore->assign('aItemsPerPage', $num_index);

// breadcrumb formation
$iaCore->set_breadcrumb($recipecat['title'], '', BREADCRUMB_POSITION_LAST, BREADCRUMB_BEHAVE_REPLACE);

// set meta keywords and description
$iaCore->set_cfg('description', $recipecat['meta_description']);
$iaCore->set_cfg('keywords', $recipecat['meta_keywords']);

$iaCore->set_cfg('title', $recipecat['title']);
$iaCore->set_cfg('body', 'recipecatview');