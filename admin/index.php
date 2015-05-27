<?php 
//##copyright##

if(!ini_get('safe_mode'))
{
	set_time_limit(100);
}
$iaCore->startHook('phpAdminRecipesHomeBeforeCode', array(), 1);

define('INTELLI_REALM', $iaCore->get_cfg('name'));

/* initialization all values */
$action = (isset($_GET['action']) ? $_GET['action'] : 'none');

// TODO: add permissions

// get statistics for items
$stats['items'] = array('recipecats', 'cookbooks', 'recipes');
foreach($stats['items'] as $item)
{
	$iaDb->setTable($item);
	
	$status_stats = $iaDb->keyvalue("`status`, count(id)", '1=1 GROUP BY `status`');
	$stats[$item]['active'] = isset($status_stats['active']) ? $status_stats['active'] : 0;
	$stats[$item]['approval'] = isset($status_stats['approval']) ? $status_stats['approval'] : 0;
	
	// get total stats by item
	$stats[$item]['total'] = $iaDb->one("count(*)");
	
	$iaDb->resetTable();
}
$iaCore->assign('stats', $stats);

// return recipes statistics chart data
if('getrecipeschart' == $action && AJAX)
{
	$iaCore->assign_by_ref('', array('statuses' => 'Active', 'total' => $stats['recipes']['active']), 'ajax');
	$iaCore->assign_by_ref('', array('statuses' => 'Approval', 'total' => $stats['recipes']['approval']), 'ajax');
}

$iaCore->startHook('phpAdminRecipesHomeAfterCode', 1);

$iaCore->display('index');