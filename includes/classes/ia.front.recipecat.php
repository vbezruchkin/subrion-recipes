<?php
//##copyright##

class iaRecipecat extends iaModel
{
	var $mTable = 'recipecats';
	
	function goToItem($aParams)
	{
		$recipecat = $aParams['item'];
		
		$url = $this->iaCore->gPackages['recipes']['url'].'categories/'.$recipecat['title_alias'].'/';		
		$output = $url.'" id="recipecats_'.$recipecat['id'].'">';
		
		return array($url, $output);
	}
	
	function accountActions($aParams)
	{
		$edit_url = IA_URL.'recipecat/edit/?id='.$aParams['item']['id'];
		
		return array($edit_url, '');
	}

	function getRecipecat($aId)
	{
		return $this->iaDb->row('*', "`id`= '$aId'", false, false, 'recipecats');
	}
	
	function getRecipecatByAlias($aAlias)
	{
		return $this->iaDb->row('*', "`title_alias` = '{$aAlias}'", false, false, 'recipecats');
	}
	
	function getRecipecats()
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS * ";
		$sql .= "FROM `{$this->mTable}` ";
		$sql .= "WHERE `status` = 'active' ";
		$sql .= "ORDER BY `title` ASC";
		 
		return $this->iaDb->getAll($sql);
	}
}