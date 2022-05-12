<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPrice extends pjPriceAppController
{
	public $sessionPrice = 'pjPrice_session';
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if (!$this->isPriceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		$err = 'PPR02';
		if (isset($_POST['price_create']))
		{
			$pjPriceModel = pjPriceModel::factory();
			$pjPriceModel->where('foreign_id', $this->getForeignId())->eraseAll();
			foreach ($_POST['tabs'] as $tab_id => $tab_name)
			{
				$i = $tab_id;
				
				$data = array();
				$data['tab_id'] = $i;
				$data['season'] = $tab_name;
				$data['foreign_id'] = $this->getForeignId();
				if ($i > 1)
				{
					$data['date_from'] = pjUtil::formatDate($_POST[$i . '_date_from'][0], $this->option_arr['o_date_format']);
					$data['date_to'] = pjUtil::formatDate($_POST[$i . '_date_to'][0], $this->option_arr['o_date_format']);
				}
				//$pjPriceModel->begin();
				foreach ($_POST[$i . '_adults'] as $k => $adults)
				{
					$data['adults'] = $_POST[$i . '_adults'][$k];
					$data['children'] = $_POST[$i . '_children'][$k];
					$data['mon'] = $_POST[$i . '_day_1'][$k];
					$data['tue'] = $_POST[$i . '_day_2'][$k];
					$data['wed'] = $_POST[$i . '_day_3'][$k];
					$data['thu'] = $_POST[$i . '_day_4'][$k];
					$data['fri'] = $_POST[$i . '_day_5'][$k];
					$data['sat'] = $_POST[$i . '_day_6'][$k];
					$data['sun'] = $_POST[$i . '_day_0'][$k];
					$pjPriceModel->reset()->setAttributes($data)->insert();
				}
				//$pjPriceModel->commit();
			}
			$err = 'PPR01';
		}
		pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjPrice&action=pjActionIndex&err=$err");
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!$this->isPriceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		$pjPriceModel = pjPriceModel::factory();
		$_arr = $pjPriceModel
			->where('t1.foreign_id', $this->getForeignId())
			->orderBy('t1.tab_id ASC, t1.id ASC, t1.date_from DESC, t1.date_to DESC')
			->findAll()
			->getData();
		
		$arr = array();
		foreach ($_arr as $k => $v)
		{
			if (!isset($arr[$v['season']]))
			{
				$arr[$v['season']] = array();
			}
			$arr[$v['season']][] = $v;
		}
		
		$query = sprintf("SELECT p1.id, p1.foreign_id, p1.tab_id, p1.season, p1.date_from, p1.date_to,
			p2.id AS `p2_id`, p2.foreign_id AS `p2_foreign_id`, p2.tab_id AS `p2_tab_id`, p2.season AS `p2_season`, p2.date_from AS `p2_date_from`, p2.date_to AS `p2_date_to`
			FROM (
				SELECT p1.id AS `pid1`, p2.id AS `pid2`
				FROM `%1\$s` `p1`, `%1\$s` `p2`
				WHERE p2.date_from BETWEEN p1.date_from AND p1.date_to
                AND p2.id != p1.id
					UNION
				SELECT p1.id, p2.id
				FROM `%1\$s` `p1`, `%1\$s` `p2`
				WHERE p2.date_to BETWEEN p1.date_from AND p1.date_to
				AND p2.id != p1.id
			) `p`, `%1\$s` `p1`, `%1\$s` `p2`
			WHERE p1.id = `pid1` AND p2.id = `pid2`
			AND p2.id > p1.id
			AND p1.tab_id != p2.tab_id
			GROUP BY p1.foreign_id, p1.tab_id, p2.foreign_id, p2.tab_id", $pjPriceModel->getTable());
		$overlap_arr = $pjPriceModel->reset()->prepare($query)->exec()->getData();
		
		$this
			->set('arr', $arr)
			->set('overlap_arr', $overlap_arr)
			->appendCss('pjPrice.css', $this->getConst('PLUGIN_CSS_PATH'))
			->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
			->appendJs('pjPrice.js', $this->getConst('PLUGIN_JS_PATH'))
		;
	}

	public function pjActionDeleteAll()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isPriceReady())
		{
			//pjPriceModel::factory()->where('foreign_id', $this->getForeignId())->eraseAll();
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Prices has been deleted.'));
		}
		exit;
	}
	
	public function pjActionBeforeSave()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isPriceReady())
		{
			if (!isset($_SESSION[$this->sessionPrice]) || !is_array($_SESSION[$this->sessionPrice]))
			{
				$_SESSION[$this->sessionPrice] = array();
			}
			
			if (isset($_POST['tabs']))
			{
				if (isset($_SESSION[$this->sessionPrice]['tabs']))
				{
					// If you want to append array elements from the second array to the first array
					// while not overwriting the elements from the first array and not re-indexing,
					// use the + array union operator:
					$_SESSION[$this->sessionPrice]['tabs'] = $_SESSION[$this->sessionPrice]['tabs'] + $_POST['tabs'];
					unset($_POST['tabs']);
				}
				
				$_SESSION[$this->sessionPrice] = array_merge($_SESSION[$this->sessionPrice], $_POST);
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isPriceReady())
		{
			if (!isset($_SESSION[$this->sessionPrice]) || empty($_SESSION[$this->sessionPrice]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			
			$STORE = $_SESSION[$this->sessionPrice];
			
			$tmp = array();
			$tab_ids = array();
			foreach ($STORE['tabs'] as $tab_id => $tab_name)
			{
				$tab_ids[] = $tab_id;
				$i = $tab_id;
				if ($i > 1)
				{
					$tmp_arr = $STORE[$i . '_date_from'];
					reset($tmp_arr);
					$first_key = key($tmp_arr);
					$date_from = pjUtil::formatDate($STORE[$i . '_date_from'][$first_key], $this->option_arr['o_date_format']);
					$date_to = pjUtil::formatDate($STORE[$i . '_date_to'][$first_key], $this->option_arr['o_date_format']);
				}
				foreach ($STORE[$i . '_adults'] as $k => $adults)
				{
					$arr = array($tab_id, $adults, $STORE[$i . '_children'][$k]);
					if ($i > 1)
					{
						$arr[] = $date_from;
						$arr[] = $date_to;
					} else {
						$arr[] = $tab_name;
					}
					$string = join("|", $arr);
					if (in_array($string, $tmp))
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
					}
					$tmp[] = $string;
				}
			}
			
			$insert_ids = array();
			$pjPriceModel = pjPriceModel::factory();
			$pjPriceModel->where('foreign_id', $this->getForeignId())/*->whereIn('tab_id', $tab_ids)*/->eraseAll();
			foreach ($STORE['tabs'] as $tab_id => $tab_name)
			{
				$i = $tab_id;
				
				$data = array();
				$data['tab_id'] = $i;
				$data['season'] = $tab_name;
				$data['foreign_id'] = $this->getForeignId();
				if ($i > 1)
				{
					$tmp_arr = $STORE[$i . '_date_from'];
					reset($tmp_arr);
					$first_key = key($tmp_arr);
					$data['date_from'] = pjUtil::formatDate($STORE[$i . '_date_from'][$first_key], $this->option_arr['o_date_format']);
					$data['date_to'] = pjUtil::formatDate($STORE[$i . '_date_to'][$first_key], $this->option_arr['o_date_format']);
				}
				//$pjPriceModel->begin();
				foreach ($STORE[$i . '_adults'] as $k => $adults)
				{
					$data['adults'] = $STORE[$i . '_adults'][$k];
					$data['children'] = $STORE[$i . '_children'][$k];
					$data['mon'] = $STORE[$i . '_day_1'][$k];
					$data['tue'] = $STORE[$i . '_day_2'][$k];
					$data['wed'] = $STORE[$i . '_day_3'][$k];
					$data['thu'] = $STORE[$i . '_day_4'][$k];
					$data['fri'] = $STORE[$i . '_day_5'][$k];
					$data['sat'] = $STORE[$i . '_day_6'][$k];
					$data['sun'] = $STORE[$i . '_day_0'][$k];
					$insert_ids[] = $pjPriceModel->reset()->setAttributes($data)->insert()->getInsertId();
				}
				//$pjPriceModel->commit();
			}
						
			$_SESSION[$this->sessionPrice] = NULL;
			unset($_SESSION[$this->sessionPrice]);
			
			if (in_array(false, $insert_ids) || in_array(0, $insert_ids))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
		}
		exit;
	}
}
?>