<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPeriod extends pjPeriodAppController
{
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isPeriodReady())
		{
			$resp = array('code' => 100);
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$resp['code'] = 101;
				if (pjPeriodModel::factory()->set('id', $_POST['id'])->erase()->getAffectedRows() == 1)
				{
					pjPeriodPriceModel::factory()->where('period_id', $_POST['id'])->eraseAll();
					$resp['code'] = 200;
				}
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!$this->isPeriodReady())
		{
			$this->set('status', 2);
			return;
		}
		
		/*if (isset($_POST['period_create']))
		{
			$err = 'PPE02';
			if (isset($_POST['default_price']) && !empty($_POST['default_price']) && isset($_POST['start_date']) && !empty($_POST['start_date']))
			{
				$err = 'PPE01';
				$pjPeriodModel = pjPeriodModel::factory();
				$pjPeriodPriceModel = pjPeriodPriceModel::factory();
				foreach ($_POST['default_price'] as $k => $v)
				{
					if (empty($_POST['start_date'][$k]) || empty($_POST['end_date'][$k]))
					{
						continue;
					}
					
					$start_date = pjUtil::formatDate($_POST['start_date'][$k], $this->option_arr['o_date_format']);
					$end_date = pjUtil::formatDate($_POST['end_date'][$k], $this->option_arr['o_date_format']);
					
					if (!pjValidation::pjActionDate($start_date) || !pjValidation::pjActionDate($end_date))
					{
						continue;
					}
					
					if (strpos($k, 'new_') === 0)
					{
						$period_id = $pjPeriodModel->reset()->setAttributes(array(
							'foreign_id' => $this->getForeignId(),
							'start_date' => $start_date,
							'end_date' => $end_date,
							'from_day' => $_POST['from_day'][$k],
							'to_day' => $_POST['to_day'][$k],
							'default_price' => $_POST['default_price'][$k]
						))->insert()->getInsertId();
					} else {
						$pjPeriodModel->reset()->set('id', $k)->modify(array(
							'start_date' => $start_date,
							'end_date' => $end_date,
							'from_day' => $_POST['from_day'][$k],
							'to_day' => $_POST['to_day'][$k],
							'default_price' => $_POST['default_price'][$k]
						));
						$pjPeriodPriceModel->reset()->where('period_id', $k)->eraseAll();
						$period_id = $k;
					}
					
					if ($period_id !== false && (int) $period_id > 0)
					{
						if (isset($_POST['adults']) && isset($_POST['adults'][$k]))
						{
							foreach ($_POST['adults'][$k] as $index => $smth)
							{
								if (empty($_POST['price'][$k][$index]))
								{
									continue;
								}
								$pjPeriodPriceModel
									->reset()
									->set('period_id', $period_id)
									->set('adults', $_POST['adults'][$k][$index])
									->set('children', $_POST['children'][$k][$index])
									->set('price', $_POST['price'][$k][$index])
									->insert();
							}
						}
					}
				}
			}
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjPeriod&action=pjActionIndex&err=$err");
		} else {*/
			$period_arr = pjPeriodModel::factory()
				->where('foreign_id', $this->getForeignId())
				->orderBy('t1.start_date ASC, t1.end_date ASC')
				->findAll()
				->getData();
				
			$pjPeriodPriceModel = pjPeriodPriceModel::factory();
			foreach ($period_arr as $k => $period)
			{
				$period_arr[$k]['price_arr'] = $pjPeriodPriceModel->reset()->where('t1.period_id', $period['id'])->orderBy('t1.adults ASC, t1.children ASC')->findAll()->getData();
			}
			$this
				->set('period_arr', $period_arr)
				->appendJs('pjPeriod.js', $this->getConst('PLUGIN_JS_PATH'))
			;
		//}
	}

	public function pjActionDeleteAll()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isPeriodReady())
		{
			$pjPeriodModel = pjPeriodModel::factory();
			$period_ids = $pjPeriodModel->where('foreign_id', $this->getForeignId())->findAll()->getDataPair(NULL, 'id');
			if (!empty($period_ids))
			{
				$pjPeriodModel->eraseAll();
				pjPeriodPriceModel::factory()->whereIn('period_id', $period_ids)->eraseAll();
				
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Periods has been deleted.'));
		}
		exit;
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isPeriodReady())
		{
			if (isset($_POST['default_price']) && !empty($_POST['default_price']) && isset($_POST['start_date']) && !empty($_POST['start_date']))
			{
				$pjPeriodModel = pjPeriodModel::factory();
				$pjPeriodPriceModel = pjPeriodPriceModel::factory();
				foreach ($_POST['default_price'] as $k => $v)
				{
					if (empty($_POST['start_date'][$k]) || empty($_POST['end_date'][$k]))
					{
						continue;
					}
					
					$start_date = pjUtil::formatDate($_POST['start_date'][$k], $this->option_arr['o_date_format']);
					$end_date = pjUtil::formatDate($_POST['end_date'][$k], $this->option_arr['o_date_format']);
					
					if (!pjValidation::pjActionDate($start_date) || !pjValidation::pjActionDate($end_date))
					{
						continue;
					}
					
					$period_id = $pjPeriodModel->reset()->setAttributes(array(
						'foreign_id' => $this->getForeignId(),
						'start_date' => $start_date,
						'end_date' => $end_date,
						'from_day' => $_POST['from_day'][$k],
						'to_day' => $_POST['to_day'][$k],
						'default_price' => $_POST['default_price'][$k]
					))->insert()->getInsertId();
					
					if ($period_id !== false && (int) $period_id > 0)
					{
						if (isset($_POST['adults']) && isset($_POST['adults'][$k]))
						{
							foreach ($_POST['adults'][$k] as $index => $smth)
							{
								if (empty($_POST['price'][$k][$index]))
								{
									continue;
								}
								$pjPeriodPriceModel
									->reset()
									->set('period_id', $period_id)
									->set('adults', $_POST['adults'][$k][$index])
									->set('children', $_POST['children'][$k][$index])
									->set('price', $_POST['price'][$k][$index])
									->insert();
							}
						}
					}
				}
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Periods has been saved.'));
		}
		exit;
	}
}
?>