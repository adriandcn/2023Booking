<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPriceModel extends pjPriceAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_price';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'tab_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'season', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'adults', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'children', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'mon', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'tue', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'wed', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'thu', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'fri', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'sat', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'sun', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjPriceModel($attr);
	}
	
	private function queryData($foreign_id, $date_from, $date_to, $options, $adults=null, $children=null)
	{
		$price_arr = $this->reset()
    		->where('t1.foreign_id', $foreign_id)
    		->where(sprintf("((t1.date_from BETWEEN '%1\$s' AND '%2\$s') OR
    			(`date_to` BETWEEN '%1\$s' AND '%2\$s') OR
    			(`date_from` < '%1\$s' AND `date_to` > '%2\$s') OR
    			(`date_from` > '%1\$s' AND `date_to` < '%2\$s'))", $date_from, $date_to))
    		->orderBy('t1.id ASC')
    		->findAll()
    		->getData();

		$default_price_arr = $this->reset()
			->where('t1.foreign_id', $foreign_id)
			->where("(t1.date_from IS NULL OR t1.date_from = '0000-00-00')")
			->where("(t1.date_to IS NULL OR t1.date_to = '0000-00-00')")
			->orderBy('t1.id ASC')
    		->findAll()
    		->getData();
    	
		foreach ($price_arr as $k => $item)
    	{
			$price_arr[$k]['ts_from'] = strtotime($item['date_from']);
			$price_arr[$k]['ts_to'] = strtotime($item['date_to']);
    	}
    	
    	return compact('price_arr', 'default_price_arr');
	}
	
	public function getPrice($foreign_id, $date_from, $date_to, $options, $adults=null, $children=null)
	{
		list($startY, $startM, $startD) = explode("-", $date_from);
    	$from = strtotime($date_from);
    	$to = strtotime($date_to);
    	$nights = ceil((strtotime($date_to) - $from) / 86400);
    	if ($options['o_price_based_on'] == 'days')
    	{
    		$nights += 1;
    	}
    	list($txtDayOfWeek, $startDay) = explode("-", date("D-w", $from)); //Mon-Sun, 0-6
    	$endDay = date("w", strtotime($date_to));
    	$isoDayOfWeek = $startDay > 0 ? $startDay : 7; //1-7 (Fix for versions < PHP 5.1.0, else use date("N")
    	
		extract($this->queryData($foreign_id, $date_from, $date_to, $options, $adults, $children));
		$mask = array(1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun');

		$price = $discount = 0;
		
    	$j = $isoDayOfWeek;
    	$season = array();
    	foreach (range(1, $nights) as $i)
    	{
    		if ($j > 7)
    		{
    			$j = 1;
    		}
    		$date = mktime(0, 0, 0, $startM, $startD + ($i - 1), $startY);
    		
    		# Season price
    		foreach ($price_arr as $k => $item)
    		{
    			if ($date >= $item['ts_from'] && $date <= $item['ts_to'])
    			{
    				if (!is_null($adults) && (int) $adults > -1 && $adults != $item['adults'])
    				{
    					continue;
    				}
    				if (!is_null($children) && (int) $children > -1 && $children != $item['children'])
    				{
    					continue;
    				}
    				$price += $item[$mask[$j]];
    				$season[$i] = true;
    				break;
    			}
    		}
    		# Default price (season)
    		if (!isset($season[$i]))
    		{
	    		foreach ($price_arr as $k => $item)
	    		{
	    			if ($date >= $item['ts_from'] && $date <= $item['ts_to'])
	    			{
	    				$price += $item[$mask[$j]];
	    				$season[$i] = true;
	    				break;
	    			}
	    		}
    		}
    		# Default price (adults, children)
    		if (!isset($season[$i]))
    		{
    			foreach ($default_price_arr as $k => $item)
	    		{
	    			if (!is_null($adults) && (int) $adults > -1 && $adults != $item['adults'])
    				{
    					continue;
    				}
    				if (!is_null($children) && (int) $children > -1 && $children != $item['children'])
    				{
    					continue;
    				}
    				$price += $item[$mask[$j]];
    				$season[$i] = true;
    				break;
	    		}
    		}
    		# Default price (general)
    		if (!isset($season[$i]))
    		{
	    		foreach ($default_price_arr as $k => $item)
	    		{
	    			if ((int) $item['adults'] === 0 && (int) $item['children'] === 0)
	    			{
	    				$price += $item[$mask[$j]];
	    				$season[$i] = true;
	    				break;
	    			}
	    		}
    		}
    		$j++;
    	}
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
	
	public function getPricePerDay($foreign_id, $date_from, $date_to, $options, $adults=null, $children=null)
    {
    	list($startY, $startM, $startD) = explode("-", $date_from);
    	$from = strtotime($date_from);
    	$nights = ceil((strtotime($date_to) - $from) / 86400);
    	if ($options['o_price_based_on'] == 'days')
    	{
    		$nights += 1;
    	}
    	list($txtDayOfWeek, $startDay) = explode("-", date("D-w", $from)); //Mon-Sun, 0-6
    	$endDay = date("w", strtotime($date_to));
    	$isoDayOfWeek = $startDay > 0 ? $startDay : 7; //1-7 (Fix for versions < PHP 5.1.0, else use date("N")
    	
    	extract($this->queryData($foreign_id, $date_from, $date_to, $options, $adults, $children));
    	
    	$mask = array(1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun');

    	$price = $discount = 0;
    	$j = $isoDayOfWeek;
    	$season = $pricePerNight = $pricePerDay = $priceMin = $priceNum = array();
    	foreach (range(1, $nights) as $i)
    	{
    		if ($j > 7)
    		{
    			$j = 1;
    		}
    		$date = mktime(0, 0, 0, $startM, $startD + ($i - 1), $startY);
    		
    		# Find out min price for current date----//
    		$priceMin[$date] = 99999999; //init
    		$priceNum[$date] = count($default_price_arr);
    		foreach ($price_arr as $k => $item)
    		{
    			if ($date >= $item['ts_from'] && $date <= $item['ts_to'])
    			{
    				$priceNum[$date] += 1;
    				if ($item[$mask[$j]] < $priceMin[$date])
    				{
    					$priceMin[$date] = $item[$mask[$j]];
    				}
    			}
    		}
    		if ($priceMin[$date] == 99999999)
    		{
	    		foreach ($default_price_arr as $k => $item)
	    		{
	    			if ($item[$mask[$j]] < $priceMin[$date])
	    			{
	    				$priceMin[$date] = $item[$mask[$j]];
	    			}
	    		}
    		}
    		# //----Find out min price for current date
    		
    		# Season price
    		foreach ($price_arr as $k => $item)
    		{
    			if ($date >= $item['ts_from'] && $date <= $item['ts_to'])
    			{
    				if (!is_null($adults) && (int) $adults > -1 && $adults != $item['adults'])
    				{
    					continue;
    				}
    				if (!is_null($children) && (int) $children > -1 && $children != $item['children'])
    				{
    					continue;
    				}
    				$price += $item[$mask[$j]];
    				$pricePerNights[$i] = $item[$mask[$j]];
    				$pricePerDay[$date] = $item[$mask[$j]];
    				$season[$i] = true;
    				break;
    			}
    		}
    		# Default price (season)
    		if (!isset($season[$i]))
    		{
	    		foreach ($price_arr as $k => $item)
	    		{
	    			if ($date >= $item['ts_from'] && $date <= $item['ts_to'])
	    			{
	    				$price += $item[$mask[$j]];
	    				$pricePerNights[$i] = $item[$mask[$j]];
	    				$pricePerDay[$date] = $item[$mask[$j]];
	    				$season[$i] = true;
	    				break;
	    			}
	    		}
    		}
    		# Default price (adults, children)
    		if (!isset($season[$i]))
    		{
    			foreach ($default_price_arr as $k => $item)
	    		{
	    			if (!is_null($adults) && (int) $adults > -1 && $adults != $item['adults'])
    				{
    					continue;
    				}
    				if (!is_null($children) && (int) $children > -1 && $children != $item['children'])
    				{
    					continue;
    				}
    				$price += $item[$mask[$j]];
    				$pricePerNights[$i] = $item[$mask[$j]];
    				$pricePerDay[$date] = $item[$mask[$j]];
    				$season[$i] = true;
    				break;
	    		}
    		}
    		# Default price (general)
    		if (!isset($season[$i]))
    		{
	    		foreach ($default_price_arr as $k => $item)
	    		{
	    			if ((int) $item['adults'] === 0 && (int) $item['children'] === 0)
	    			{
	    				$price += $item[$mask[$j]];
	    				$pricePerNights[$i] = $item[$mask[$j]];
	    				$pricePerDay[$date] = $item[$mask[$j]];
	    				$season[$i] = true;
	    				break;
	    			}
	    		}
    		}
    		$j++;
    	}
			
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
		
		if (isset($options['o_deposit']) && (float) $options['o_deposit'] > 0)
		{
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
		$result['pricePerDay'] = $pricePerDay;
		$result['priceData'] = array();
		foreach ($pricePerDay as $time => $v)
		{
			$result['priceData'][$time] = array('price' => @$pricePerDay[$time], 'priceMin' => @$priceMin[$time], 'priceNum' => @$priceNum[$time]);
		}
    	return $result;
    }
    
	public function pjActionSetup()
	{
		
	}
}
?>