<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUtil extends pjToolkit
{
	static public function html2rgb($color)
	{
		$color = str_replace('#', '', $color);
		if (strlen($color) == 6)
		{
			list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		} else {
			return false;
		}
		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		
		return array($r, $g, $b);
	}
	
	static public function uuid()
	{
		return chr(rand(65,90)) . chr(rand(65,90)) . time();
	}
	
	static public function getClass($arr, $date, $tomorrow, $yesterday, $bookings_per_day, $haystack=array())
	{
		$default = array(
			'calendarStatus1' => 'calendarStatus1',
			'calendarStatus2' => 'calendarStatus2',
			'calendarStatus3' => 'calendarStatus3',
			'calendarStatus_1_2' => 'calendarStatus_1_2',
			'calendarStatus_1_3' => 'calendarStatus_1_3',
			'calendarStatus_2_1' => 'calendarStatus_2_1',
			'calendarStatus_2_3' => 'calendarStatus_2_3',
			'calendarStatus_3_1' => 'calendarStatus_3_1',
			'calendarStatus_3_2' => 'calendarStatus_3_2',
			'calendarStatusPartial' => 'calendarStatusPartial'
		);
		
		$stack = array_merge($default, $haystack);
		
		$class = NULL;
		if (isset($arr[$date]))
		{
			switch ((int) $arr[$date]['is_change_over'])
			{
				case 1:
					/*if ((!isset($arr[$yesterday]) || (isset($arr[$yesterday]) && $arr[$yesterday]['status'] == 1)) && $arr[$date]['status'] == 2)
       				{
      					$class = $stack['calendarStatus_1_2'];
       				} elseif (isset($arr[$yesterday]) && $arr[$yesterday]['status'] == 3 && $arr[$date]['status'] == 2) {
       					$class = $stack['calendarStatus_3_2'];
					//moved as #2
       				} elseif ((isset($arr[$yesterday]) && $arr[$yesterday]['status'] == 2 && $arr[$date]['status'] == 1) ||
       					($arr[$date]['status'] == 2 && (!isset($arr[$tomorrow]) || (isset($arr[$tomorrow]) && $arr[$tomorrow]['status'] == 1 && !isset($arr[$tomorrow]['end']))))) {
       					$class = $stack['calendarStatus_2_1'];
       				} elseif (($arr[$date]['status'] == 2 && isset($arr[$tomorrow]) && $arr[$tomorrow]['status'] == 3 && $arr[$tomorrow]['is_change_over'] == 0) ||
       					(isset($arr[$yesterday]) && $arr[$yesterday]['status'] == 2 && $arr[$date]['status'] == 3)) {
       					$class = $stack['calendarStatus_2_3'];
       				} elseif ((isset($arr[$yesterday]) && $arr[$yesterday]['status'] == 3 && $arr[$date]['status'] == 1) ||
       					//($arr[$date]['status'] == 3 && isset($arr[$tomorrow]) && $arr[$tomorrow]['status'] == 1) ||
       					(!isset($arr[$tomorrow]) && $arr[$date]['status'] == 3)) {
       					$class = $stack['calendarStatus_3_1'];
       				} elseif ((isset($arr[$tomorrow]) && $arr[$tomorrow]['status'] == 3 && $arr[$date]['status'] == 1) ||
       					(isset($arr[$yesterday]) && $arr[$yesterday]['status'] == 1 && $arr[$date]['status'] == 3) ||
       					(!isset($arr[$yesterday]) && $arr[$date]['status'] == 3)) {
       					$class = $stack['calendarStatus_1_3'];
       				} elseif (isset($arr[$yesterday]) && $arr[$yesterday]['status'] == 1 && isset($arr[$tomorrow]) &&
       					($arr[$tomorrow]['status'] == 1 || $arr[$tomorrow]['is_change_over'] == 1)) {
       					$class = $stack['calendarStatus1'];
       				// #2 start
       				} elseif ((isset($arr[$yesterday]) && $arr[$yesterday]['status'] == 2 && isset($arr[$tomorrow]) && in_array($arr[$tomorrow]['status'], array(1,2))) || 
       						($arr[$date]['status'] == 2 && isset($arr[$date]['end']) && $arr[$date]['end']['status'] == 'Confirmed' && isset($arr[$date]['start']) && $arr[$date]['start']['status'] == 'Confirmed') ) {
       					$class = $stack['calendarStatus2'];
       				// #2 end
       				} elseif ((!isset($arr[$yesterday]) && isset($arr[$tomorrow]) && $arr[$tomorrow]['status'] == 3) ||
       					(isset($arr[$yesterday]) && ($arr[$yesterday]['status'] == 3 || $arr[$yesterday]['is_change_over'] == 1))) {
       					$class = $stack['calendarStatus3'];
       				}*/
					if ($arr[$date]['status'] == 2 && !isset($arr[$date]['end']) && isset($arr[$date]['start'], $arr[$date]['confirmed']) && $arr[$date]['confirmed'] == $arr[$date]['count'])
					{
						$class = $stack['calendarStatus_1_2'];
						
					} elseif ($arr[$date]['status'] == 2 && isset($arr[$date]['end'], $arr[$date]['start'], $arr[$date]['pending'], $arr[$date]['confirmed'])) {
						
						$class = $stack['calendarStatus_3_2'];
						
					} elseif ($arr[$date]['status'] == 2 && isset($arr[$date]['end'], $arr[$date]['confirmed']) && !isset($arr[$date]['start']) && $arr[$date]['confirmed'] == $arr[$date]['count']) {
						
						$class = $stack['calendarStatus_2_1'];
						
					} elseif ($arr[$date]['status'] == 3 && isset($arr[$date]['end'], $arr[$date]['start'], $arr[$date]['pending'], $arr[$date]['confirmed']) 
						&& $arr[$date]['end']['status'] == 'Confirmed' && $arr[$date]['start']['status'] == 'Pending' && $arr[$date]['is_limit_reached'] == 1) {
						
						$class = $stack['calendarStatus_2_3'];
						
					} elseif ($arr[$date]['status'] == 3 && isset($arr[$date]['end']) && !isset($arr[$date]['start']) 
						&& (
							(isset($arr[$date]['pending']) && $arr[$date]['pending'] == $arr[$date]['count'])
							|| 
							(isset($arr[$date]['confirmed']) && $arr[$date]['confirmed'] == $arr[$date]['count'])
						)) {
						
						$class = $stack['calendarStatus_3_1'];
						
					} elseif ($arr[$date]['status'] == 3 && !isset($arr[$date]['end']) && isset($arr[$date]['start']) 
						&& (
							(isset($arr[$date]['pending']) && $arr[$date]['pending'] == $arr[$date]['count'])
							||
							(isset($arr[$date]['confirmed']) && $arr[$date]['confirmed'] == $arr[$date]['count'])
						)) {
						
						$class = $stack['calendarStatus_1_3'];
						
					} elseif (1==2) {
						
						$class = $stack['calendarStatus1'];
						
					} elseif ($arr[$date]['status'] == 3 && isset($arr[$date]['end'], $arr[$date]['start'])
						&& (
							(isset($arr[$date]['pending']) && $arr[$date]['pending'] == $arr[$date]['count'])
							||
							(isset($arr[$date]['confirmed']) && $arr[$date]['confirmed'] == $arr[$date]['count'])
							||
							(isset($arr[$date]['pending'], $arr[$date]['confirmed']) && $arr[$date]['pending'] + $arr[$date]['confirmed'] == $arr[$date]['count'])
						)) {
						
						$class = $stack['calendarStatus3'];
						
					} elseif ($arr[$date]['status'] == 2 && isset($arr[$date]['confirmed']) && $arr[$date]['confirmed'] == $arr[$date]['count'] 
						&& (isset($arr[$date]['end'], $arr[$date]['start']) xor isset($arr[$date]['in'])) ) {
						
						$class = $stack['calendarStatus2'];
						
					}
					break;
				case 0:
				default:
					$class = $stack['calendarStatus' . $arr[$date]['status']];
					break;
			}
			if ($arr[$date]['status'] == 3 && (int) $bookings_per_day > 1)
			{
				$class .= ' ' . $stack['calendarStatusPartial'];
			}
		}
		return $class;
	}
	
	static public function fixSingleDay($arr)
	{
		//1 Free
		//2 Reserved
		//3 Pending
		foreach ($arr as $key => $item)
		{
			if (!isset($item['confirmed']) && !isset( $item['pending']))
			{
				$arr[$key]['status'] = 1;
			} elseif (isset($item['confirmed']) && $item['confirmed'] == 1 && !isset( $item['pending'])) {
				$arr[$key]['status'] = 2;
			} elseif (!isset($item['confirmed']) && isset( $item['pending']) && $item['pending'] == 1) {
				$arr[$key]['status'] = 3;
			} elseif (isset($item['confirmed']) && $item['confirmed'] == 1 && isset( $item['pending']) && $item['pending'] == 1) {
				// Nights
				if (isset($item['start']['status']))
				{
					switch ($item['start']['status'])
					{
						case 'Pending':
							$arr[$key]['status'] = 3;
							break;
						case 'Confirmed':
							$arr[$key]['status'] = 2;
							break;
					}
				}
			} elseif (!isset($item['confirmed']) && isset($item['pending']) && $item['pending'] == 2) {
				// Nights
				$arr[$key]['status'] = 3;
			} elseif (isset($item['confirmed']) && $item['confirmed'] == 2 && !isset($item['pending']) ) {
				// Nights
				$arr[$key]['status'] = 2;
			}else{
				$arr[$key]['status'] = 1;
			}
		}
		
		return $arr;
	}

	static public function pjActionGenerateImages($cid, $data)
	{
		pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_reserved_start.jpg', str_replace('#', '', $data['o_background_available']), str_replace('#', '', $data['o_background_booked']));
		pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_reserved_end.jpg', str_replace('#', '', $data['o_background_booked']), str_replace('#', '', $data['o_background_available']));
		pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_pending_pending.jpg', str_replace('#', '', $data['o_background_pending']), str_replace('#', '', $data['o_background_pending']));
		pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_reserved_pending.jpg', str_replace('#', '', $data['o_background_booked']), str_replace('#', '', $data['o_background_pending']));
		pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_pending_reserved.jpg', str_replace('#', '', $data['o_background_pending']), str_replace('#', '', $data['o_background_booked']));
		pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_reserved_reserved.jpg', str_replace('#', '', $data['o_background_booked']), str_replace('#', '', $data['o_background_booked']));
		pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_pending_start.jpg', str_replace('#', '', $data['o_background_available']), str_replace('#', '', $data['o_background_pending']));
		pjUtil::pjActionBackgroundImage(PJ_UPLOAD_PATH . $cid . '_pending_end.jpg', str_replace('#', '', $data['o_background_pending']), str_replace('#', '', $data['o_background_available']));
	}
	
	static private function pjActionBackgroundImage($dst, $color_1, $color_2, $w=200, $h=200)
	{
		if (!extension_loaded('gd') || !function_exists('gd_info'))
		{
			return false;
		}
		# Spatial_anti-aliasing. Make an image larger then it's intended
		$width = $w * 10;
		$height = $h * 10;
		
		$image = imagecreatetruecolor($width, $height);
		if ($image === false)
		{
			return false;
		}
		if (function_exists('imageantialias'))
		{
			imageantialias($image, true);
		}
		$backgroundColor = pjUtil::html2rgb($color_1);
		$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
		if ($color !== false && $color !== -1)
		{
			if (function_exists('imagefilledrectangle'))
			{
				//imagefill($image, 0, 0, $color); // this one lead to an unexpected problem, works on localhost, but not at the server
				imagefilledrectangle($image, 0, 0, $width, $height, $color);
			}
		}
		if (isset($color_2) && !empty($color_2))
		{
			if ($color_1 == $color_2)
			{
				$backgroundColor = pjUtil::html2rgb('ffffff');
				$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
		
				$values = array(
						0, $height-2,
						$width-2, 0,
						$width, 0,
						$width, 1,
						1, $height,
						0, $height,
						0, $height-1
				);
				imagefilledpolygon($image, $values, 7, $color);
			} else {
				$backgroundColor = pjUtil::html2rgb($color_2);
				$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
				$values = array(
						$width,  0,  // Point 1 (x, y)
						$width,  $height, // Point 2 (x, y)
						0, $height,
						$width,  0
				);
				imagefilledpolygon($image, $values, 4, $color);
			}
		}
		# Shrink it down to remove the aliasing and make it it's intended size
		$new_image = imagecreatetruecolor($w, $h);
		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $w, $h, $width, $height);
		# save image
		imagejpeg($new_image, $dst, 100);
		imagedestroy($image);
		imagedestroy($new_image);
	}

	static public function getWeekRange($date, $week_start)
	{
		$week_arr = array(0=>'sunday',
						  1=>'monday',
						  2=>'tuesday',
						  3=>'wednesday',
						  4=>'thursday',
						  5=>'friday',
						  6=>'saturday');
						   
		$ts = strtotime($date);
	    $start = (date('w', $ts) == 0) ? $ts : strtotime('last ' . $week_arr[$week_start], $ts);
	    $week_start = ($week_start == 0 ? 6 : $week_start -1);
	    return array(date('Y-m-d', $start), date('Y-m-d', strtotime('next ' . $week_arr[$week_start], $start)));
	}
	
	static public function getComingWhere($period, $week_start)
	{
		$where_str = '';
		switch ($period) {
			case 1:
				$where_str = "(CURDATE() BETWEEN t1.date_from AND t1.date_to)";
				break;
			;
			case 2:
				$where_str = "(DATE(DATE_ADD(NOW(), INTERVAL 1 DAY)) BETWEEN t1.date_from AND t1.date_to)";
				break;
			;
			case 3:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
				$where_str = "((t1.date_from BETWEEN CURDATE() AND '$end_week') OR 
							   (t1.date_to BETWEEN CURDATE() AND '$end_week') OR 
							   (t1.date_from <= CURDATE() AND t1.date_to >= '$end_week'))";
				break;
			;
			case 4:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("+7 days")), $week_start);
				$where_str = "((t1.date_from BETWEEN '$start_week' AND '$end_week') OR 
							   (t1.date_to BETWEEN '$start_week' AND '$end_week') OR 
							   (t1.date_from <= '$start_week' AND t1.date_to >= '$end_week'))";
				break;
			;
			case 5:
				$end_month = date('Y-m-t',strtotime('this month'));
				$where_str = "((t1.date_from BETWEEN CURDATE() AND '$end_month') OR 
							   (t1.date_to BETWEEN CURDATE() AND '$end_month') OR 
							   (t1.date_from <= CURDATE() AND t1.date_to >= '$end_month'))";
				break;
			;
			case 6:
				$start_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, 1, date("Y")));
				$end_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 2, 0, date("Y")));
				$where_str = "((t1.date_from BETWEEN '$start_month' AND '$end_month') OR 
							   (t1.date_to BETWEEN '$start_month' AND '$end_month') OR 
							   (t1.date_from <= '$start_month' AND t1.date_to >= '$end_month'))";
				break;
			;
		}
		return $where_str;
	}
	
	static public function getMadeWhere($period, $week_start)
	{
		$where_str = '';
		switch ($period) {
			case 1:
				$where_str = "(DATE(t1.created) = CURDATE() OR DATE(t1.modified) = CURDATE())";
				break;
			;
			case 2:
				$where_str = "(DATE(t1.created) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) OR DATE(t1.modified) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)))";
				break;
			;
			case 3:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
				$where_str = "((DATE(t1.created) BETWEEN '$start_week' AND '$end_week') OR (DATE(t1.modified) BETWEEN '$start_week' AND '$end_week'))";
				break;
			;
			case 4:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("-7 days")), $week_start);
				$where_str = "((DATE(t1.created) BETWEEN '$start_week' AND '$end_week') OR (DATE(t1.modified) BETWEEN '$start_week' AND '$end_week'))";
				break;
			;
			case 5:
				$start_month = date('Y-m-01',strtotime('this month'));
				$end_month = date('Y-m-t',strtotime('this month'));
				$where_str = "((DATE(t1.created) BETWEEN '$start_month' AND '$end_month') OR (DATE(t1.modified) BETWEEN '$start_month' AND '$end_month'))";
				break;
			;
			case 6:
				$start_month = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y")));
				$end_month = date("Y-m-d", mktime(0, 0, 0, date("m"), 0, date("Y")));
				$where_str = "((DATE(t1.created) BETWEEN '$start_month' AND '$end_month') OR (DATE(t1.modified) BETWEEN '$start_month' AND '$end_month'))";
				break;
			;
		}
		return $where_str;
	}
	
	static public function getTimezoneName($timezone)
	{
		$offset = $timezone / 3600;
		$timezone_name = timezone_name_from_abbr(null, $offset * 3600, true);
		if($timezone_name === false)
		{
			$timezone_name = timezone_name_from_abbr(null, $offset * 3600, false);
		}
		if($offset == -12)
		{
			$timezone_name = 'Pacific/Wake';
		}
		return $timezone_name;
	}
}
?>