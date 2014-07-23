<?php
//##copyright##

class iaCookbook extends iaModel
{
	var $mTable = 'cookbooks';
	
	function goToItem($aParams)
	{
		$cookbook = $aParams['item'];
		$url = $this->iaCore->gPackages['recipes']['url'].'cookbooks/'.$cookbook['id'].'-'.$cookbook['title_alias'].'/';
		$output = $url.'" id="cookbook_'.$cookbook['id'].'">';
		
		return array($url, $output);
	}
	
	function accountActions($aParams)
	{
		$edit_url = IA_URL.'cookbook/edit/?id='.$aParams['item']['id'];
		
		return array($edit_url, '');
	}

	function getCookbook($aId)
	{
		return $this->iaDb->row('*', "`id`= '$aId'", false, false, 'cookbooks');
	}
	
	function getCookbooks($aWhere, $aStart = 0, $aLimit = 0, $aOrder = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS * ";
		$sql .= "FROM `{$this->mTable}` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= $aWhere;
		$sql .= !empty($aWhere) ? $aWhere : 'ORDER BY `date_added` DESC ';
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';
		 
		return $this->iaDb->getAll($sql);
	}
	
	function getCookbooksByAccount($aAccount, $aStart = 0, $aLimit = null)
	{
		$where = "AND `account_id` = '{$aAccount}' ";
		 
		return $this->getCookbooks($where, $aStart, $aLimit);
	}
	
	function getCookbooksByCategory($aCategory, $aStart = 0, $aLimit = 0)
	{
		$where = "AND `recipecats` REGEXP ('{$aCategory},|{$aCategory}$') ";
		 
		return $this->getCookbooks($where, $aStart, $aLimit);
	}
}