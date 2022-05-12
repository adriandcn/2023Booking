<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPeriodModel extends pjPeriodAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_period';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'start_date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'end_date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'from_day', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'to_day', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'default_price', 'type' => 'decimal', 'default' => ':NULL')
	);

	public static function factory($attr=array())
	{
		return new pjPeriodModel($attr);
	}
	
	private function getIntersect($period_arr, $booking_dates, $adults, $children)
	{
		$ranges = pjPeriodModel::factory()->getRanges($period_arr);

		foreach ($ranges as $period_id => $arr)
		{
			foreach ($arr as $hash => $range)
			{
				$intersect = array_intersect($booking_dates, $range['dates']);
				if (!empty($intersect) && count($intersect) === count($range['dates']))
				{
					$diff = array_diff($booking_dates, $range['dates']);
					
					$info = array();
					$info['period_id'] = $period_id;
					
					if (!is_null($adults) && !is_null($children))
					{
						foreach ($range['info']['arr'] as $item)
						{
							if ((int) $item['adults'] === (int) $adults && (int) $item['children'] === (int) $children)
							{
								$info['price'] = $item['price'];
								break;
							}
						}
					}
					if (!isset($info['price']))
					{
						$info['price'] = $range['info']['default_price'];
					}
					
					return compact('intersect', 'diff', 'info');
				}
			}
		}
		
		return false;
	}
	
	public function getPeriodsPerDay($foreign_id, $month, $year, $count=1, $days=true)
	{
		$arr = array();
		
		foreach (range(1, $count) as $i)
		{
			$first = mktime(0, 0, 0, (int) $month + ($i - 1), 1, (int) $year);
			$numOfDays = date("t", $first);

			foreach (range(1, $numOfDays) as $j)
			{
				$d = mktime(0, 0, 0, (int) $month + ($i - 1), $j, (int) $year);
				$arr[$d] = NULL;
			}
		}
		
		$start = date("Y-m-d", mktime(0, 0, 0, (int) $month, 1, (int) $year));
		$end = date("Y-m-d", mktime(0, 0, 0, (int) $month + $count, 0, (int) $year));
		$period_arr = $this
			->select("t1.*, UNIX_TIMESTAMP(start_date) AS start_ts, UNIX_TIMESTAMP(end_date) AS end_ts")
			->where('t1.foreign_id', $foreign_id)
			->where(sprintf("start_date <= '%2\$s' AND end_date >= '%1\$s'", $start, $end))
			->findAll()
			->getData();

		$ranges = $this->getRanges($period_arr);
		foreach ($ranges as $period_id => $range)
		{
			foreach ($range as $range_data)
			{
				foreach ($range_data['dates'] as $date)
				{
					if (array_key_exists($date, $arr))
					{
						if (is_null($arr[$date]))
						{
							$arr[$date] = array();
						}
						$arr[$date][] = array_merge($range_data['info'], array(
							'from' => $range_data['dates'][0],
							'to' => $range_data['dates'][count($range_data['dates'])-1]
						));
					}
				}
			}
		}

		return $arr;
	}

	public function getPeriods($foreign_id, $date_from, $date_to, $adults=null, $children=null)
	{
		$arr = $this
    		->where('t1.foreign_id', $foreign_id)
    		->where('t1.start_date <=', $date_to)
    		->where('t1.end_date >=', $date_from)
    		->findAll()
    		->getData();
    	
    	$pjPeriodPriceModel = pjPeriodPriceModel::factory();
    	
    	$days = array();
    	foreach ($arr as $k => $period)
    	{
    		$pjPeriodPriceModel
    			->reset()
    			->where('t1.period_id', $period['id']);
    		if (!is_null($adults))
    		{
    			$pjPeriodPriceModel->where('t1.adults', $adults);
    		}
    		if (!is_null($children))
    		{
    			$pjPeriodPriceModel->where('t1.children', $children);
    		}
    		$arr[$k]['start_ts'] = strtotime($period['start_date']);
    		$arr[$k]['end_ts'] = strtotime($period['end_date']);
    		$arr[$k]['arr'] = $pjPeriodPriceModel->findAll()->getData();
    		$days[$k] = abs((int) $period['to_day'] - (int) $period['from_day']) + 1;
    		if ((int) $period['to_day'] === (int) $period['from_day'])
    		{
    			$days[$k] = 7;
    		}
    	}
    	array_multisort($days, SORT_DESC, SORT_NUMERIC, $arr);
    	
    	return $arr;
	}
	
	private function deepPrice(&$price, $period_arr, $booking_dates, $adults, $children)
	{
		$period_info = $this->getIntersect($period_arr, $booking_dates, $adults, $children);

		if ($period_info !== FALSE && isset($period_info['info']) && isset($period_info['info']['price']))
		{
			$price += $period_info['info']['price'];
		}
		
		if (!empty($period_info['diff']))
		{
			$arr = array_values($period_info['diff']);
			array_unshift($arr, end($period_info['intersect']));
			
			$this->deepPrice($price, $period_arr, $arr, $adults, $children);
		}
	}
	
	public function getPrice($foreign_id, $date_from, $date_to, $options, $adults=null, $children=null)
	{
		list($startY, $startM, $startD) = explode("-", $date_from);
    	$from = strtotime($date_from);
    	$to = strtotime($date_to);
    	$nights = ceil((strtotime($date_to) - $from) / 86400);
    	$booking_nights = $nights + 1;
    	if ($options['o_price_based_on'] == 'days')
    	{
    		$nights += 1;
    	}
    	
		$booking_dates = array();
		foreach (range(1, $booking_nights/*$nights*/) as $i)
		{
			$booking_dates[] = mktime(0, 0, 0, $startM, $startD + ($i - 1), $startY);
		}
		$price = 0;
		
		$period_arr = $this->getPeriods($foreign_id, $date_from, $date_to, $adults, $children);
		$this->deepPrice($price, $period_arr, $booking_dates, $adults, $children);

		$price = $price > 0 ? $price : 0;
		
		# -----
		$amount = $deposit = $tax = $security = $net = 0;
		$amount = $net = $price;
		
		if (isset($options['o_security']) && (float) $options['o_security'] > 0)
		{
			$security = (float) $options['o_security'];
		}
		
		if (isset($options['o_tax']) && (float) $options['o_tax'] > 0)
		{
			$tax = ($net * (float) $options['o_tax']) / 100;
		}
		
		if (isset($options['o_require_all_within']) && (int) $options['o_require_all_within'] > 0 &&
			strtotime(date("Y-m-d")) + (int) $options['o_require_all_within'] * 86400 >= $from)
		{
			$deposit = $amount + $tax + $security;
			
		} elseif (isset($options['o_deposit']) && (float) $options['o_deposit'] > 0) {
			
			switch ($options['o_deposit_type'])
			{
				case 'percent':
					$deposit = (($amount + $tax) * (float) $options['o_deposit']) / 100 + $security;
					break;
				case 'amount':
					$deposit = (float) $options['o_deposit'];
					break;
			}
		}
		$result = array_map('floatval', compact('amount', 'deposit', 'tax', 'security', 'net'));

		return $result;
	}
	
	public function getRanges($period_arr)
	{
		$ranges = array();
		foreach ($period_arr as $k => $period)
		{
			$started = false;
			$ranges[$period['id']] = array();
			
			for ($i = $period['start_ts']; $i <= $period['end_ts']; $i += 86400)
			{
				$weekDay = date("w", $i);
				$weekDay = $weekDay > 0 ? (int) $weekDay : 7;
				
				$period['from_day'] = (int) $period['from_day'];
				$period['to_day'] = (int) $period['to_day'];

				if ($weekDay === $period['to_day'] && $started)
				{
					$started = false;
					$ranges[$period['id']][$hash]['dates'][] = $i;
				}
				
				if ($weekDay === $period['from_day'] && !$started)
				{
					$started = true;
					$hash = md5(uniqid(rand(), true));
					$ranges[$period['id']][$hash] = array('dates' => array($i), 'info' => $period);
				}
				
				if (!in_array($weekDay, array($period['from_day'], $period['to_day'])) && $started)
				{
					$ranges[$period['id']][$hash]['dates'][] = $i;
				}
			}
			
			if ($started)
			{
				$ranges[$period['id']][$hash] = NULL;
				unset($ranges[$period['id']][$hash]);
			}
		}

		return $ranges;
	}
	
	public function pjActionSetup()
	{

	}
}
?>