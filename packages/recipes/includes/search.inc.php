<?php
//##copyright##

function cookbooks_search()
{
}

function recipecats_search()
{
}

function recipes_search($aQuery, $aFields, $aStart, $aLimit, &$aNumAll, $aWhere = '', $cond = 'AND')
{
	$iaCore = &iaCore::instance();
	$ret = array();
	$match = array();
	if ($aQuery)
	{
		$match[] = sprintf(" MATCH (`title`, `ingredients`, `procedures`) AGAINST('%s') ", $iaCore->sql($aQuery));
	}
	if ($aWhere)
	{
		$match[] = '' . $aWhere;
	}

	// additional fields
	if ($aFields && is_array($aFields))
	{
		foreach ($aFields as $fname => $data)
		{
			if ('LIKE' == $data['cond'])
			{
				$data['val'] = "%{$data['val']}%";
			}
			// for multiple values, like combo or checkboxes
			if (is_array($data['val']))
			{
				if ('!=' == $data['cond'])
				{
					$data['cond'] = count($data['val']) > 1 ? 'NOT IN' : '!=';
				}
				else
				{
					$data['cond'] = count($data['val']) > 1 ? 'IN' : '=';
				}
				$data['val'] = count($data['val']) > 1 ? '(' . implode(',', $data['val']) . ')' : array_shift($data['val']);
			}
			else if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $data['val'], $range))
			{
				// search in range
				$data['cond'] = sprintf('BETWEEN %d AND %d', $range[1], $range[2]);
				$data['val'] = '';
			}
			else
			{
				$data['val'] = "'" . $iaCore->sql($data['val']) . "'";
			}

			$match[] = "`{$fname}` {$data['cond']} {$data['val']} ";
		}
	}

	$iaRecipe = $iaCore->factoryPackages('recipes', 'front', 'recipe');
	$recipes = $match ? $iaRecipe->getRecipes(' AND ('.implode(' '.$cond.' ', $match).')', $aStart, $aLimit) : array();
	$aNumAll = $iaRecipe->getRecipesNum(' AND ('.implode(' '.$cond.' ', $match).')');

	if ($recipes && SMARTY)
	{
		$iaCore->iaSmarty->assign('all_items', $recipes);
		$fields = $iaCore->getAcoFieldsList('recipes_home', 'recipes', '', true);
		$iaCore->iaSmarty->assign_by_ref('all_item_fields', $fields);	
		$iaCore->iaSmarty->assign('all_item_type', 'recipes');
		
		$ret[] = $iaCore->iaSmarty->fetch('all-items-page.tpl');
	}

	return $ret;
}
