<?php
//##copyright##

class iaRecipe extends iaModel
{
	/**
	 * @var $mTable defines current table
	 */
	var $mTable = 'recipes';
	
	function getRecipes($aWhere, $aStart = 0, $aLimit = '', $aOrder = '')
	{
		$order = $aOrder ? " ORDER BY ".$aOrder : ' ';
		$limit = $aLimit ? " LIMIT {$aStart}, {$aLimit} " : ' ';
		
		$sql = "SELECT t1.*, '1' `edit`, '1' `remove`, ";
		$sql .= "IF(t3.`fullname` <> '', t3.`fullname`, t3.`username`) `account` ";
		$sql .= "FROM `{$this->mTable}` t1 ";
		$sql .= "LEFT JOIN `{$this->mPrefix}accounts` t3 ";
		$sql .= "ON t1.`account_id` = t3.`id` ";
		$sql .= "WHERE 1 = 1 AND ";
		$sql .= $aWhere.$order.$limit;
		
		return $this->iaDb->getAll($sql);	
	}
	
	function getRecipesNum($aWhere)
	{
		return $this->iaDb->one('COUNT(*)', $aWhere);
	}
	
	function getRecipeById($aId)
	{
		$sql = "SELECT t1.*, ";
		$sql .= "IF(t3.`fullname` <> '', t3.`fullname`, t3.`username`) `account` ";
		$sql .= "FROM `{$this->mTable}` t1 ";
		$sql .= "LEFT JOIN `{$this->mPrefix}accounts` t3 ";
		$sql .= "ON t1.`account_id` = t3.`id` ";
		$sql .= "WHERE t1.`id` = '{$aId}'";
		
		return $this->iaDb->getRow($sql);
	}

	function add($aData)
	{
		$add_data = array('date_added' => 'NOW()', 'date_modified' => 'NOW()');

		return $this->iaDb->insert($aData, $add_data);
	}
	
	function update($aData, $aWhere = '')
	{
		$add_data = array('date_modified' => 'NOW()');
			
		return $this->iaDb->update($aData, $aWhere, $add_data);
	}
}