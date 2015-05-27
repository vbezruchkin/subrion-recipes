<?php
//##copyright##

$mod = $iaCore->get_cfg('name');
define('INTELLI_REALM', $mod);

$iaDb->setTable('recipes');
$iaRecipe = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'admin', 'recipe');

$where = '1=1';
if(SMARTY)
{
	if(isset($_GET['status']))
	{
		if(in_array($_GET['status'], array('active', 'approval')))
		{
			$_SESSION['recipe_status'] = $_GET['status'];
		}
	}
	elseif(isset($_SESSION['recipe_status']))
	{
		unset($_SESSION['recipe_status']);
	}
}
else
{
	if(isset($_SESSION['recipe_status']))
	{
		$where = "`status` = '".$_SESSION['recipe_status']."'";
	}
}

if(AJAX)
{
	// ajax account return
	if (isset($_GET['q']))
	{
		$where = "`fullname` LIKE '{$_GET['q']}%' OR  `username` LIKE '{$_GET['q']}%' ";
		$order = "ORDER BY `account` ASC ";
		
		$iaDb->setTable('accounts');
		$accounts = $iaDb->all("IF(`fullname` <> '', `fullname`, `username`) `account` ", $where.$order, 0, 15);
		$iaDb->resetTable();
		
		if ($accounts)
		{
			foreach($accounts as $account)
			{
				echo $account['account']."\r\n";
			}
		}
		exit;
	}
	
	// return alias value
	if (isset($_GET['action']) && 'getalias' == $_GET['action'])
	{
		$out['data'] = '';

		$title = $_GET['title'];

		// transliterate title alias
		if ($iaCore->get('recipes_auto_alias'))
		{
			$iaUtil = $iaCore->factory('core', 'util');
			if(!defined('IA_NOUTF'))
			{
				iaUtf8::loadUTF8Core();
				iaUtf8::loadUTF8Util('ascii', 'utf8_to_ascii');
			}
			if(!utf8_is_ascii($title))
			{
				$title = utf8_to_ascii($title);
			}	
		}
		
		$out['data'] = IA_PACKAGE_URL.'category/cookbook/'.$iaCore->convertStr($title).'.html';
		
		$iaCore->assign_all($out);
	}
	
	if(isset($_GET['action']) && $_GET['action'] == 'get')
	{
		$out = array('data' => '', 'total' => 0);
		$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
		$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
		$sort = $_GET['sort'];
		$dir = in_array($_GET['dir'], array('ASC', 'DESC')) ? $_GET['dir'] : 'ASC';

		if(!empty($sort) && !empty($dir))
		{
			$order = '`'.$sort.'` '.$dir;
		}
		
		$where = array();		
		if(isset($_GET['status']) && in_array($_GET['status'], array('active', 'approval')))
		{
			$where[] = "t1.`status` = :status";
			$values['status'] = $_GET['status'];
		}
		elseif(isset($_SESSION['recipe_status']))
		{
			$where[] = "t1.`status` = :status";
			$values['status'] = $_SESSION['recipe_status'];
		}

		if(isset($_GET['title']) && !empty($_GET['title']))
		{
			$where[] = "t1.`title` LIKE :title";
			$values['title'] = '%'.$_GET['title'].'%';
		}
		
		if(isset($_GET['account']) && !empty($_GET['account']))
		{
			$iaDb->setTable('accounts');
			$account_id = $iaDb->one('id', "`fullname` LIKE '{$_GET['account']}%' OR  `username` LIKE '{$_GET['account']}' ");
			$iaDb->resetTable();
			
			$where[] = "t1.`account_id` = :account_id";
			$values['account_id'] = $account_id;
		}
		
		if(empty($where))
		{
			$where[] = '1=1';
			$values = array();
		}

		$where = implode(" AND ", $where);
		$iaRecipe->iaDb->mysql_bind($where, $values);
		
		$out['total'] = $iaRecipe->getRecipesNum($where);
		$out['data'] = $iaRecipe->getRecipes($where, $start, $limit, $order);
		
		$iaCore->assign_all($out);
	}
	
	if(isset($_POST['action']))
	{
		$out = array('msg' => '', 'error' => true);
		$where = $iaDb->convertIds('id', $_POST['ids']);

		if('remove' == $_POST['action'])
		{
			$result = $iaDb->delete($where);
		}
		else
		{
			$result = $iaRecipe->update(array($_POST['field'] => $_POST['value']), $where);
		}
		
		if($result)
		{
			$out['error'] = false;
			$out['msg'] = _t('changes_saved');
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = 'Not save';
		}
	
		$iaCore->assign_all($out);
	}
}
elseif(SMARTY)
{	
	$error = false;
	$error_fields = array();
	$msg = array();
	$iaCore->set_cfg('body', 'none');
	
	if($mod == 'manage_recipes')
	{
		$iaCore->grid('_IA_URL_packages/recipes/js/admin/recipes');
		$iaCore->display('none');
	}
	elseif($mod == 'edit_recipe' || $mod == 'add_recipe')
	{
		$iaCore->set_breadcrumb(_t('manage_recipes'), IA_ADMIN_URL . 'manage/recipes/');
		$iaCore->set_cfg('body', 'edit_recipe');
		
		$iaRecipecat = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'admin', 'recipecat');
		
		$recipecats = $iaRecipecat->getRecipecats();
		$iaCore->assign('recipecats', $recipecats);
		
		if(isset($vals[0])) 
		{
			$_GET['id'] = (int)$_GET['id'];
		}
		
		if ($mod == 'add_recipe' || empty($_GET['id']))
		{
			$recipe = array();
		}
		else
		{
			$recipe = $iaRecipe->getRecipeById($_GET['id']);
		}
		
		$fields = $iaCore->getAllFields(true, "", "recipes");
		
		if (!empty($_POST))
		{
			if($fields)
			{
				$iaUtil = $iaCore->factory('core', 'util');
				list($data, $error, $msg, $error_fields) = iaUtil::updateItemPOSTFields($fields, $recipe, true);
				
			}
			if (!$error)
			{
				$msg = _t('changes_saved');
				
				$iaCore->startHook("phpAdminBeforeRecipeSubmit");
				
				$data['status'] = $_POST['status'];
				$data['title_alias'] = !empty($_POST['title_alias'])? $_POST['title_alias'] : $_POST['title'];
				
				// transliterate title alias
				if ($iaCore->get('recipes_auto_alias'))
				{
					$iaUtil = $iaCore->factory('core', 'util');
					if(!defined('IA_NOUTF'))
					{
						iaUtf8::loadUTF8Core();
						iaUtf8::loadUTF8Util('ascii', 'utf8_to_ascii');
					}
					if(!utf8_is_ascii($data['title_alias']))
					{
						$data['title_alias'] = utf8_to_ascii($data['title_alias']);
					}
				}
				
				$data['title_alias'] = $iaCore->convertStr($data['title_alias']);
				
				$data['recipecats'] = !empty($_POST['recipecats']) ? $_POST['recipecats'] : array();
				$data['recipecats'] = implode(',', $data['recipecats']);
				
				if (!empty($_POST['account']))
				{
					$iaDb->setTable('accounts');
					$account_id = $iaDb->one('id', "`fullname` LIKE '{$_POST['account']}%' OR  `username` LIKE '{$_POST['account']}' ");
					$iaDb->resetTable();
				
					$data['account_id'] = $account_id;
				}
				else
				{
					$data['account_id'] = $_SESSION['user']['id'];
				}
				
				if (!empty($_POST['recipecats']))
				{
					$recipecat_info = $iaRecipecat->iaDb->row('*', "`id`= '{$_POST['recipecats'][0]}'", false, false, 'recipecats');
				
					$data['recipecats'] = implode($_POST['recipecats'], ',');
					$data['category_alias'] = $recipecat_info['title_alias'];
				}

				if (!empty($_POST['cookbook']))
				{
					$cookbook_info = $iaCookbook->getCookbookById($_POST['cookbook']);
				
					$data['id_cookbook'] = $cookbook_info['id'];
					$data['cookbook_alias'] = $cookbook_info['title_alias'];
				}
				
				if (empty($recipe))
				{
					$iaCore->startHook("phpAdminBeforeRecipeAdd");

					$data['id'] = $iaRecipe->add($data);
					$msg = _t('recipe_added');
				}
				else
				{
					$data['id'] = $recipe['id'];
					
					$iaCore->startHook("phpAdminBeforeRecipeUpdate");
					
					$iaRecipe->update($data);
					$msg = _t('changes_saved');
				}
				$recipe = $iaRecipe->getRecipeById($data['id']);
				
				$iaCore->msg($msg, ($error ? 'error' : 'success'));
				if(isset($_POST['goto']))
				{
					iaUtil::go_to(IA_ADMIN_URL . 'manage/recipes/' . ($_POST['goto'] == 'add' ? 'add/' : ''));
				}
			}			
		}
		$recipe['recipecats'] = !empty($recipe['recipecats']) ? explode(',', $recipe['recipecats']) : array();
		
		$fields_groups = $iaCore->getFieldsGroups('', false, 'recipes');
		$iaCore->assign('fields_groups', $fields_groups);
		
		$iaCore->assign('recipe', $recipe);
		$iaCore->assign('isView', false);

		$iaCore->display('recipes');
	}
}
$iaDb->resetTable();