<?php
//##copyright##

define("INTELLI_REALM", $iaCore->get_cfg('name'));

if(SMARTY)
{
	$iaGenre = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'recipecat');
	$per_page = $iaCore->get('recipes_per_page', 10);
	$page = isset($_GET['page']) && 1 < $_GET['page'] ? (int)$_GET['page'] : 1;
	$start = ($page - 1) * $per_page;
	$where = false;
	
	$fields = $iaCore->getAcoFieldsList(false, 'recipecats', '', true);
	
	// gets current page and defines start position
	$num_index = 20;
	$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$start = (max($page,1) - 1) * $num_index;
	
	$search_alphas = array('0-9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$alpha = (isset($vals[0]) && in_array($vals[0], $search_alphas)) ? $vals[0] : false;
	$cause = $alpha ? ('0-9' == $alpha ?  "(`$account_by` REGEXP '^[0-9]') AND " : "(`$account_by` LIKE '{$alpha}%') AND ") : '';
	
	$recipecats = $iaGenre->getGenres($where, $start, $per_page);
	$recipecats_total = $iaDb->foundRows();

	if($recipecats)
	{
		$recipecats = $iaCore->updateItemsFavorites($recipecats, 'recipecats');
	}
	
	$iaCore->assign_by_ref('fields', $fields);
	$iaCore->assign('recipecats', $recipecats);
	$iaCore->assign('aTotal', $recipecats_total);
	$iaCore->assign('aItemsPerPage', $per_page);
	$iaCore->assign('aTemplate', IA_URL . 'recipes/?page={page}');
	
	$iaCore->set_breadcrumb(_t('recipecats'), '', BREADCRUMB_POSITION_LAST, BREADCRUMB_BEHAVE_REPLACE);
	
	$iaCore->set_cfg('title', _t('page_title_'.INTELLI_REALM));
	$iaCore->set_cfg('body', 'recipecats');
}