<?php
//##copyright##

$mod = $iaCore->get_cfg('name');
define('INTELLI_REALM', $mod);

$iaDb->setTable('cookbooks');
$iaCookbook = $iaCore->factoryPackages(IA_CURRENT_PACKAGE, 'admin', 'cookbook');

$where = '1=1';
if(SMARTY)
{
	if(isset($_GET['status']))
	{
		if(in_array($_GET['status'], array('active', 'approval')))
		{
			$_SESSION['cookbook_status'] = $_GET['status'];
		}
	}
	elseif(isset($_SESSION['cookbook_status']))
	{
		unset($_SESSION['cookbook_status']);
	}
}
else
{
	if(isset($_SESSION['cookbook_status']))
	{
		$where = "`status` = '".$_SESSION['cookbook_status']."'";
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
		
		$out['data'] = IA_PACKAGE_URL.'cookbooks/'.$iaCore->convertStr($title).'/';
		
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
		elseif(isset($_SESSION['cookbook_status']))
		{
			$where[] = "t1.`status` = :status";
			$values['status'] = $_SESSION['cookbook_status'];
		}

		if(isset($_GET['title']) && !empty($_GET['title']))
		{
			$where[] = "t1.`title` LIKE :title";
			$values['title'] = '%'.$_GET['title'].'%';
		}
	
		if(empty($where))
		{
			$where[] = '1=1';
			$values = array();
		}

		$where = implode(" AND ", $where);
		$iaCookbook->iaDb->mysql_bind($where, $values);
		
		$out['data'] = $iaCookbook->getCookbooks($where, $start, $limit, $order);
		$out['total'] = $iaCookbook->iaDb->foundRows();
		
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
			$result = $iaCookbook->update(array($_POST['field'] => $_POST['value']), $where);
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
	
	if($mod == 'manage_cookbooks')
	{
		$iaCore->grid('_IA_URL_packages/recipes/js/admin/cookbooks');
		$iaCore->display('none');
	}
	elseif($mod == 'edit_cookbook' || $mod == 'add_cookbook')
	{
		$iaCore->set_breadcrumb(_t('manage_cookbooks'), IA_ADMIN_URL . 'manage/cookbooks/');
		$iaCore->set_cfg('body', 'edit_cookbook');
		
		if(isset($vals[0])) 
		{
			$_GET['id'] = (int)$_GET['id'];
		}
		
		if ($mod == 'add_cookbook' || empty($_GET['id']))
		{
			$cookbook = array();
		}
		else
		{
			$cookbook = $iaCookbook->getCookbookById($_GET['id']);
		}
		
		$fields = $iaCore->getAllFields(true, "", "cookbooks");
		
		if (!empty($_POST))
		{
			if($fields)
			{
				$iaUtil = $iaCore->factory('core', 'util');
				list($data, $error, $msg, $error_fields) = iaUtil::updateItemPOSTFields($fields, $cookbook, true);
			}
			if (!$error)
			{
				$iaCore->startHook("phpAdminBeforeCookbookSubmit");
				
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
				
				if (empty($cookbook))
				{
					$iaCore->startHook("phpAdminBeforeCookbookAdd");
					
					$data['id'] = $iaCookbook->add($data);
					$msg = _t('cookbook_added');
				}
				else
				{
					$data['id'] = $cookbook['id'];
					
					$iaCore->startHook("phpAdminBeforeCookbookUpdate");
					
					$iaCookbook->update($data);
					$msg = _t('changes_saved');
				}
				$cookbook = $iaCookbook->getCookbookById($data['id']);
				
				$iaCore->msg($msg, ($error ? 'error' : 'success'));
				if(isset($_POST['goto']))
				{
					iaUtil::go_to(IA_ADMIN_URL . 'manage/cookbooks/' . ($_POST['goto'] == 'add' ? 'add/' : ''));
				}
			}
			
		}
		$fields_groups = $iaCore->getFieldsGroups();
		$iaCore->assign('fields_groups', $fields_groups);
		
		$iaCore->assign('cookbook', $cookbook);
		$iaCore->assign('isView', false);

		$iaCore->display('cookbooks');
	}
}
$iaDb->resetTable();