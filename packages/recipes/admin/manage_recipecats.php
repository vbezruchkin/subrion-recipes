<?php
//##copyright##

$mod = $iaCore->get_cfg('name');
define('INTELLI_REALM', $mod);

$iaDb->setTable('recipecats');
$iaRecipecat = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'admin', 'recipecat');

$where = '1=1';
if(SMARTY)
{
	if(isset($_GET['status']))
	{
		if(in_array($_GET['status'], array('active', 'approval')))
		{
			$_SESSION['recipecat_status'] = $_GET['status'];
		}
	}
	elseif(isset($_SESSION['recipecat_status']))
	{
		unset($_SESSION['recipecat_status']);
	}
}
else
{
	if(isset($_SESSION['recipecat_status']))
	{
		$where = "`status` = '".$_SESSION['recipecat_status']."'";
	}
}

if(AJAX)
{
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
		
		$out['data'] = IA_PACKAGE_URL.'recipecats/'.$iaCore->convertStr($title).'/';
		
		$iaCore->assign_all($out);
	}
	
	if(isset($_GET['action']) && $_GET['action'] == 'get')
	{
		$out = array('data' => '', 'total' => 0);
		$start = (int)$_GET['start'];
		$limit = (int)$_GET['limit'];
		$sort = $_GET['sort'];
		$dir = in_array($_GET['dir'], array('ASC', 'DESC')) ? $_GET['dir'] : 'ASC';

		$where = array();
		
		if(!empty($sort) && !empty($dir))
		{
			$order = ' ORDER BY `'.$sort.'` '.$dir;
		}

		if(isset($_GET['status']) && in_array($_GET['status'], array('active', 'approval')))
		{
			$where[] = "`status` = :status";
			$values['status'] = $_GET['status'];
		}
		elseif(isset($_SESSION['recipecat_status']))
		{
			$where[] = "`status` = :status";
			$values['status'] = $_SESSION['recipecat_status'];
		}

		if(isset($_GET['title']) && !empty($_GET['title']))
		{
			$where[] = "`title` LIKE :title";
			$values['title'] = '%'.$_GET['title'].'%';
		}
	
		if(empty($where))
		{
			$where[] = '1=1';
			$values = array();
		}

		$where = implode(" AND ", $where);
		$iaDb->mysql_bind($where, $values);
		
		$out['total'] = $iaDb->one("COUNT(*)", $where);
		$out['data'] = $iaDb->all("`title`, `title_alias`, `description`, `date_modified`, `date_added`, `status`, `id`, '1' `edit`, '1' `remove`", $where.$order, $start, $limit);
		
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
			$result = $iaRecipecat->update(array($_POST['field'] => $_POST['value']), $where);
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
	
	if($mod == 'manage_recipecats')
	{
		$iaCore->grid('_IA_URL_packages/recipes/js/admin/recipecats');
		$iaCore->display('none');
	}
	elseif($mod == 'edit_recipecat' || $mod == 'add_recipecat')
	{
		$iaCore->set_breadcrumb(_t('manage_recipecats'), IA_ADMIN_URL . 'manage/recipecats/');
		$iaCore->set_cfg('body', 'edit_recipecat');
		
		if(isset($vals[0])) 
		{
			$_GET['id'] = (int)$_GET['id'];
		}
		$recipecat = ($mod == 'add_recipecat' || empty($_GET['id'])) ? array() : $iaDb->row('*', sprintf("`id`=%d", $_GET['id']));

		$fields = $iaCore->getAllFields(true, "", "recipecats");
		
		if (!empty($_POST))
		{
			if($fields)
			{
				$iaUtil = $iaCore->factory('core', 'util');
				list($data, $error, $msg, $error_fields) = iaUtil::updateItemPOSTFields($fields, $recipecat, true);
				
			}
			if (!$error)
			{
				$iaCore->startHook("phpAdminBeforeRecipecatSubmit");

				$data['locked'] = (int)$_POST['locked'];
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

				if (empty($recipecat))
				{
					$iaCore->startHook("phpAdminBeforeRecipecatAdd");

					$data['id'] = $iaRecipecat->add($data);
					$msg = _t('recipecat_added');
				}
				else
				{
					$data['id'] = $recipecat['id'];
					
					$iaCore->startHook("phpAdminBeforeRecipecatUpdate");
					
					$iaRecipecat->update($data);					
					$msg = _t('changes_saved');
				}
				$recipecat = $iaDb->row('*', sprintf("`id`=%d", $data['id']));
				
				$iaCore->msg($msg, ($error ? 'error' : 'success'));
				if(isset($_POST['goto']))
				{
					iaUtil::go_to(IA_ADMIN_URL . 'manage/recipecats/' . ($_POST['goto'] == 'add' ? 'add/' : ''));
				}
			}
		}
		$fields_groups = $iaCore->getFieldsGroups();
		$iaCore->assign('fields_groups', $fields_groups);
		
		$iaCore->assign('recipecat', $recipecat);
		$iaCore->assign('isView', false);

		$iaCore->display('recipecats');
	}
}
$iaDb->resetTable();