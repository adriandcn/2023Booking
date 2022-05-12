<?php
if (isset($tpl['ABCalendar']))
{
	$month = $year = array();

	if (isset($_GET['year']) && isset($_GET['month']))
	{
		$y = $_GET['year'];
		$m = $_GET['month'];
	} else {
		list($y, $m) = explode("-", date("Y-m"));
	}
	
	if (isset($_GET['view']) && (int) $_GET['view'] > 1)
	{
		$next_month = $m + (int) $_GET['view'] <= 12 ? $m + (int) $_GET['view'] : $m + (int) $_GET['view'] - 12;
		$next_year = $m + (int) $_GET['view'] <= 12 ? $y : $y + 1;
		$prev_month = $m - (int) $_GET['view'] >= 1 ? $m - (int) $_GET['view'] : $m - (int) $_GET['view'] + 12;
		$prev_year = $m - (int) $_GET['view'] >= 1 ? $y : $y - 1;
	}
}
include dirname(__FILE__) . '/elements/menu.php';
if (isset($tpl['ABCalendar']))
{
	$month[1] = intval($m);
	foreach (range(2, 12) as $i)
	{
		$month[$i] = ($month[1] + $i - 1) > 12 ? $month[1] + $i - 1 - 12 : $month[1] + $i - 1;
	}
	
	$year[1] = intval($y);
	foreach (range(2, 12) as $i)
	{
		$year[$i] = ($month[1] + $i - 1) > 12 ? $year[1] + 1 : $year[1];
	}	
	if (isset($_GET['view']))
	{
		switch ((int) $_GET['view'])
		{
			case 12:
				foreach (range(1, 12) as $i)
				{
					?><div class="abBox13"><?php echo $tpl['ABCalendar']->getMonthView($month[$i], $year[$i]); ?></div><?php
				}
				break;
			case 6:
				foreach (range(1, 6) as $i)
				{
					?><div class="abBox13"><?php echo $tpl['ABCalendar']->getMonthView($month[$i], $year[$i]); ?></div><?php
				}
				break;
			case 3:
				foreach (range(1, 3) as $i)
				{
					?><div class="abBox13"><?php echo $tpl['ABCalendar']->getMonthView($month[$i], $year[$i]); ?></div><?php
				}
				break;
			case 1:
			default:
				echo $tpl['ABCalendar']->getMonthView($month[1], $year[1]);
				break;
		}
	} else {
		echo $tpl['ABCalendar']->getMonthView($month[1], $year[1]);
	}
	
	if ((int) $tpl['option_arr']['o_show_legend'] === 1)
	{
		echo $tpl['ABCalendar']->getLegend($tpl['option_arr'], pjRegistry::getInstance()->get('fields'));
	}
}
?>