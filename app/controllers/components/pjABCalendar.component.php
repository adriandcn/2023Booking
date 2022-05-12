<?php
require_once dirname(__FILE__) . '/pjCalendar.component.php';
class pjABCalendar extends pjCalendar
{
	protected $calendarId = null;
	
	protected $reservationsInfo = array();
	
	protected $weekNumbers = false;
	
	protected $prices = array();
	
	protected $showPrices = false;
	
	protected $options = array();
	
	protected $isPrice = false;

	protected $periods = array();
	
	protected $JSON;
	
	public function __construct()
	{
		$this->JSON = new pjServices_JSON();
		parent::__construct();
	}
	
	public function getMonthView($month, $year)
    {
        return $this->getMonthHTML($month, $year, 1);
    }
    
	public function getCalendarLink($month, $year)
	{
		return array('href' => '#', 'onclick' => '', 'class' => 'abCalendarLinkMonth');
	}
	
	public function getMonthViewMulti($startMonth, $startYear, $numOfMonths=3)
	{
		if ($numOfMonths < 1 && $numOfMonths > 12)
		{
			return false;
		}
		
		$month[1] = $startMonth;
		foreach (range(2, 12) as $i)
		{
			$month[$i] = ($month[1] + $i - 1) > 12 ? $month[1] + $i - 1 - 12 : $month[1] + $i - 1;
		}
		
		$year[1] = $startYear;
		foreach (range(2, 12) as $i)
		{
			$year[$i] = ($month[1] + $i - 1) > 12 ? $year[1] + 1 : $year[1];
		}
		
		$str = "";
		foreach (range(1, $numOfMonths) as $i)
		{
			$str .= $this->getMonthView($month[$i], $year[$i]);
		}
		
		return $str;
	}
	
	public function getLegend($CalendarOptions, $locale)
	{
		$html = '
		<table class="abCalendarLegend" cellspacing="1" cellpadding="2">
			<tbody>
				<tr>
					<td class="abCalendarColor abCalendarColorAvailable">&nbsp;</td>
					<td class="abCalendarLabel">'.@$locale['legend_available'].'</td>
					<td class="abCalendarColor abCalendarColorPending">&nbsp;</td>
					<td class="abCalendarLabel">'.@$locale['legend_pending'].'</td>
					<td class="abCalendarColor abCalendarColorReserved">&nbsp;</td>
					<td class="abCalendarLabel">'.@$locale['legend_booked'].'</td>
					<td class="abCalendarColor abCalendarColorPast">&nbsp;</td>
					<td class="abCalendarLabel">'.@$locale['legend_past'].'</td>
				</tr>
			</tbody>
		</table>';
		return $html;
	}
	
	public function set($key, $value)
	{
		if (in_array($key, array('calendarId', 'reservationsInfo', 'weekNumbers', 'prices', 'showPrices', 'options', 'isPrice', 'periods')))
		{
			$this->$key = $value;
		}
		return $this;
	}
}
?>