<?php
//##copyright##
define('INTELLI_REALM', $cfg['name']);

$iaDb->setTable('recipes');
$iaRecipe = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'front', 'recipe');

$plans = array();
$error_fields = array();

if(isset($_GET['id'])) $id = (int)$_GET['id'];
else $id = false;

$recipe = $id ? $iaDb->row('*, \'recipes\' as `item`', "`id`={$id}") : array();

// plans
$iaPlan = $iaCore->factory('front','plan');
$plans = $iaPlan->getPlans('recipes');
$iaCore->assign('plans', $plans);

if (isset($_GET['id']))
{
	if (empty($recipe))
	{
		iaCore::errorPage(404);
	}
	elseif ($_SESSION['user']['id'] != $recipe['account_id'])
	{
		iaCore::errorPage(403);
	}
}

if ($id)
{
	$sections = $iaCore->getAcoGroupsFields(false, 'recipes', "`f`.`type` <> 'pictures'");
	$iaCore->assign_by_ref('sections', $sections);
	
	$pictures_sections = $iaCore->getAcoGroupsFields(false, 'recipes', "`f`.`type`='pictures'", false);
	$iaCore->assign_by_ref('pictures_sections', $pictures_sections);
	
}
else
{
	$sections = $iaCore->getAcoGroupsFields(false, 'recipes');
	$iaCore->assign_by_ref('sections', $sections);
}

if(SMARTY)
{
	if (!empty($_POST))
	{
		$fields = $iaCore->getAcoFieldsList(false, 'recipes', false, true);
		if($fields)
		{
			$data = '';
			$iaUtil = $iaCore->factory('core','util');
			list($data, $error, $msg, $error_fields) = iaUtil::updateItemPOSTFields($fields, $recipe);
		}
		
		if (!$error)
		{
			$iaCore->startHook("beforeEstateSubmit");
			$dmsg = '';
			if ($iaCore->get('recipes_auto_approval') || $action == 'deleted')
			{
				$data['status'] = 'active';
			}
			else
			{
				$data['status'] = 'approval';
				$dmsg = '_apporval';
			}
			
			if (empty($recipe))
			{
				$action = 'added';
				$data['id'] = $iaRecipe->add($data);
				if($data['id'] == 0)
				{
					$error = true;
					$msg[] = _t('mysql_error');
				}
			}
			else
			{
				if(isset($_POST['delete_recipe']))
				{
					$iaDb->delete('`id` = '.$recipe['id']);
					$action = 'deleted';
					$data['id'] = 0;
				}
				else
				{
					$action = 'updated';
					$data['id'] = $recipe['id'];
					$iaDb->update($data);
				}
			}
			
			if(!$error)
			{
				$url = IA_PACKAGE_URL . ( $data['id'] != 0 ? 'add/?id='.$data['id'] : 'accounts/' );
				$msg[] = _t('recipe_'.$action.$dmsg);
				
				$iaUtil = $iaCore->factory('core', 'util');
				iaUtil::redirect(_t('thanks'), $msg, $url, isset($_POST['ajax']));
			}
		}
		
		if (isset($_POST['ajax']))
		{
			header('Content-type: text/xml');
			echo '<?xml version="1.0" encoding="'.$iaCore->get('charset').'" ?>'
				.'<root><error>' . $error . '</error><msg><![CDATA[<li>' . implode('</li><li>',$msg).']]></msg></root>';
			exit;
		}
	}

	
	$iaCore->assign('error_fields', $error_fields);
	$iaCore->assign('item', $recipe);
	
	$iaCore->set_cfg('body', 'submit');
	$iaCore->set_cfg('title', _t('page_title_'.INTELLI_REALM));
}
$iaDb->resetTable();