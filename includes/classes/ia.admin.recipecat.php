<?php
//##copyright##

class iaRecipecat extends iaModel
{
	/**
	 * @var $mTable defines current table
	 */
	var $mTable = 'recipecats';
	
	function getRecipecats()
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS * ";
		$sql .= "FROM `{$this->mTable}` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= "ORDER BY `title` ASC ";
		 
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