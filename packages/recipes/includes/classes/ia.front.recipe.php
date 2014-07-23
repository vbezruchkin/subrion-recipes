<?php
//##copyright##

class iaRecipe extends iaModel
{
	var $mTable = 'recipes';
	
	function goToItem($aParams)
	{
		$recipe = $aParams['item'];
		$url = $this->iaCore->gPackages['recipes']['url'].'recipe/'.$recipe['category_alias'].'/'.$recipe['id'].'-'.$recipe['title_alias'].'.html';
		$output = $url.'" id="recipes_'.$recipe['id'].'">';
		
		return array($url, $output);
	}
	
	function accountActions($aParams)
	{
		$edit_url = IA_URL.'recipe/edit/?id='.$aParams['item']['id'];
		
		return array($edit_url, '');
	}
	
	function getForAccount($aIds, $aStart = 0 , $aLimit = 0, $aAccount = false)
	{
		if(is_array($aIds) && !$aAccount)
		{
			$where = " AND `id` IN ('".implode("','",$aIds)."') ";
		}
		elseif ($aAccount)
		{
			$where = " AND `account_id` ='{$aAccount}' ";
		}
		else
		{
			return false;
		}

		$all_items = $this->getRecipes($where, $aStart, $aLimit);
		$total_items = $this->iaDb->foundRows();
		
		return array('items'=>$all_items,'total_number'=>$total_items);
	}

	function getRecipe($aId)
	{
		$sql = "SELECT t1.*, ";
		$sql .= "acc.`username` `account_username`, IF('' != acc.`fullname`, acc.`fullname`, acc.`username`) `account_fullname` ";
		$sql .= "FROM `{$this->mTable}` t1 ";
		$sql .= "LEFT JOIN `{$this->mPrefix}accounts` acc ON t1.`account_id`=acc.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		$sql .= "AND t1.`id` = '{$aId}'";

		return $this->iaDb->getRow($sql);
	}

	function getRecipesNum($aWhere)
	{
		$sql = "SELECT COUNT(*) ";
		$sql .= "FROM `{$this->mTable}` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= $aWhere;
		
		return $this->iaDb->getOne($sql);
	}
	
	function getRecipes($aWhere, $aStart = 0, $aLimit = 0, $aOrder = '')
	{
		$sql = "SELECT t1.*, ";
		$sql .= "acc.`username` `account_username`, IF('' != acc.`fullname`, acc.`fullname`, acc.`username`) `account_fullname` ";
		$sql .= "FROM `{$this->mTable}` t1 ";
		$sql .= "LEFT JOIN `{$this->mPrefix}accounts` acc ON t1.`account_id`=acc.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		$sql .= $aWhere;
		$sql .= !empty($aWhere) ? $aWhere : 'ORDER BY t1.`date_added` DESC ';
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';
		 
		return $this->iaDb->getAll($sql);
	}
	
	function getRecipesByAccount($aAccount, $aStart = 0, $aLimit = null)
	{
		$where = "AND `account_id` = '{$aAccount}' ";
		 
		return $this->getRecipes($where, $aStart, $aLimit);
	}

	function getRecipesNumByCategory($aCategory)
	{
		$where = "AND `recipecats` REGEXP ('{$aCategory},|{$aCategory}$') ";

		return $this->getRecipesNum($where);
	}
	
	function getRecipesByCategory($aCategory, $aStart = 0, $aLimit = 0)
	{
		$where = "AND `recipecats` REGEXP ('{$aCategory},|{$aCategory}$') ";
		 
		return $this->getRecipes($where, $aStart, $aLimit);
	}
}