<?php
//##copyright##

class iaCookbook extends iaModel
{
	/**
	 * @var $mTable defines current table
	 */
	var $mTable = 'cookbooks';
	
	function getCookbookById($aId)
	{
		$sql = "SELECT t1.* FROM `{$this->mTable}` t1 ";
		$sql .= "WHERE t1.`id` = '{$aId}'";
		
		return $this->iaDb->getRow($sql);
	}
	
	function getCookbookByTitle($aTitle)
	{
		$sql = "SELECT t1.* FROM `{$this->mTable}` t1 ";
		$sql .= "WHERE t1.`title` = '{$aTitle}'";
		
		return $this->iaDb->getRow($sql);
	}
	
	function getCookbooks($aWhere, $aStart = 0, $aLimit = '', $aOrder = '')
	{
		$order = $aOrder ? " ORDER BY ".$aOrder : ' ';
		$limit = $aLimit ? " LIMIT {$aStart}, {$aLimit} " : ' ';
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS t1.*, '1' `edit`, '1' `remove` ";
		$sql .= "FROM `{$this->mTable}` t1 ";
		$sql .= "WHERE 1 = 1 AND ";
		$sql .= $aWhere.$order.$limit;
			
		return $this->iaDb->getAll($sql);	
	}
	
	function add($aData)
	{
		$add_data = array('account_id' => $_SESSION['user']['id'], 'date_added' => 'NOW()', 'date_modified' => 'NOW()');
		
		return $this->iaDb->insert($aData, $add_data);
	}
	
	function update($aData, $aWhere = '')
	{
		$add_data = array('date_modified' => 'NOW()');
		
		return $this->iaDb->update($aData, $aWhere, $add_data);
	}
}