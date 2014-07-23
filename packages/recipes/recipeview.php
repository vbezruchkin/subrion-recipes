<?php
//##copyright##

define('INTELLI_REALM', 'recipe_view');

if (isset($vals[1]))
{
	$_GET['id'] = (int)$vals[1];
}

$iaRecipe = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'recipe');

$recipe = isset($_GET['id']) ? $iaRecipe->getRecipe((int)$_GET['id']) : false;

if (empty($recipe))
{
	iaCore::errorPage('404');
}
$iaCore->startHook('phpViewRecipeBeforeStart', array('listing' => $recipe['id'], 'item' => 'recipe'));

$recipe['@view'] = true;

// get sections
$sections = $iaCore->getAcoGroupsFields(false, false, "`f`.`type`<>'pictures'", $recipe);
$pictures_sections = $iaCore->getAcoGroupsFields(false, false, "`f`.`type`='pictures'", $recipe);

if($pictures_sections)
{
  foreach($pictures_sections as $onesection)
	{
		if(isset($onesection['fields']) && !empty($onesection['fields']) && is_array($onesection['fields']))
		{
			foreach($onesection['fields'] as $onefield)
			{
				if(isset($recipe[$onefield['name']]) && !empty($recipe[$onefield['name']]))
				{
					$iaCore->assign_by_ref('pictures_sections', $pictures_sections);
					break 2;
				}
			}
		}
	}
}
$artist['item'] = 'recipe';
$iaCore->recipe = $recipe;

$iaCore->assign(array('recipe' => $recipe, 'sections' => $sections));
// get account info
if($recipe['account_id'] > 0)
{
	$author = $iaDb->row('*', "`id`='{$recipe['account_id']}'", 0, 0, 'accounts');
	$iaCore->assign('author', $author);
}

// get category information
$iaRecipecat = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'recipecat');
$recipecat = $iaRecipecat->getRecipecat($recipe['recipecats']);

// set breadcrumb
$recipe_url = $iaRecipecat->goToItem(array('item' => $recipecat));
$iaCore->set_breadcrumb($recipecat['title'], $recipe_url[0]);
$iaCore->assign('recipecat', $recipecat);

// get cookbook information
$iaCookbook = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'cookbook');
$cookbook = $iaCookbook->getCookbook($recipe['id_cookbook']);
if (!empty($cookbook))
{
	$url = $iaCookbook->goToItem(array('item' => $cookbook));
	$iaCore->set_breadcrumb($cookbook['title'], $url[0]);
}
$iaCore->assign('cookbook', $cookbook);

// count views
$iaDb->setTable('recipes');
$iaDb->update(array(), "`id` = '{$recipe['id']}'", array('views' => "`views` + 1"));
$iaDb->resetTable();

// set meta keywords and description
$iaCore->set_cfg('description', $recipe['meta_description']);
$iaCore->set_cfg('keywords', $recipe['meta_keywords']);

$iaCore->set_cfg('title', $recipe['title']);
$iaCore->set_cfg('body', 'recipeview');