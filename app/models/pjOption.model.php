<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjOptionModel extends pjAppModel
{
	protected $primaryKey = NULL;
	
	protected $table = 'options';
	
	protected $schema = array(
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'key', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'tab_id', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'value', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'label', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'varchar', 'default' => 'string'),
		array('name' => 'order', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'is_visible', 'type' => 'tinyint', 'default' => 1),
		array('name' => 'style', 'type' => 'varchar', 'default' => 'string')
	);
	
	public static function factory($attr=array())
	{
		return new pjOptionModel($attr);
	}
	
	public function getAllPairs($foreign_id)
	{
		return $this->where('t1.foreign_id', $foreign_id)->findAll()->getDataPair('key', 'value');
	}
	
	public function getPairs($foreign_id)
	{
		$_arr = $this
			->where('t1.foreign_id', $foreign_id)
			->orWhere('t1.key', 'private_key')
			->findAll()->getData();
		$arr = array();
		foreach ($_arr as $row)
		{
			switch ($row['type'])
			{
				case 'enum':
				case 'bool':
					list(, $arr[$row['key']]) = explode("::", $row['value']);
					break;
				default:
					$arr[$row['key']] = $row['value'];
					break;
			}
		}
		return $arr;
	}

	public function init($calendar_id)
	{
		$data = array(
			array($calendar_id, 'o_accept_bookings', 3, '1|0::1', NULL, 'bool', 1, 1, NULL),
			array($calendar_id, 'o_allow_authorize', 7, '1|0::0', NULL, 'bool', 18, 1, NULL),
			array($calendar_id, 'o_allow_bank', 7, '1|0::1', NULL, 'bool', 24, 1, NULL),
			array($calendar_id, 'o_allow_creditcard', 7, '1|0::1', NULL, 'bool', 23, 1, NULL),
			array($calendar_id, 'o_allow_paypal', 7, '1|0::0', NULL, 'bool', 16, 1, NULL),
			array($calendar_id, 'o_authorize_key', 7, '', NULL, 'string', 20, 1, NULL),
			array($calendar_id, 'o_authorize_mid', 7, '', NULL, 'string', 19, 1, NULL),
			array($calendar_id, 'o_authorize_hash', 7, NULL, NULL, 'string', 21, 1, NULL),
			array($calendar_id, 'o_authorize_tz', 7, '-43200|-39600|-36000|-32400|-28800|-25200|-21600|-18000|-14400|-10800|-7200|-3600|0|3600|7200|10800|14400|18000|21600|25200|28800|32400|36000|39600|43200|46800::0', 'GMT-12:00|GMT-11:00|GMT-10:00|GMT-09:00|GMT-08:00|GMT-07:00|GMT-06:00|GMT-05:00|GMT-04:00|GMT-03:00|GMT-02:00|GMT-01:00|GMT|GMT+01:00|GMT+02:00|GMT+03:00|GMT+04:00|GMT+05:00|GMT+06:00|GMT+07:00|GMT+08:00|GMT+09:00|GMT+10:00|GMT+11:00|GMT+12:00|GMT+13:00', 'enum', 22, 1, NULL),
			array($calendar_id, 'o_background_available', 2, '#80b369', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_booked', 2, '#da5350', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_empty', 2, '#f8f6f0', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_month', 2, '#248faf', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_nav', 2, '#187c9a', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_nav_hover', 2, '#116b86', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_past', 2, '#f2f0ea', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_pending', 2, '#f9ce67', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_select', 2, '#99CCCC', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_background_weekday', 2, '#ffffff', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_bank_account', 7, "Please, send your payment to HSBC\r\naccount number: ABCDEF1234567890", NULL, 'text', 25, 1, NULL),
			array($calendar_id, 'o_bf_address', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 6, 1, NULL),
			array($calendar_id, 'o_bf_adults', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 4, 1, NULL),
			array($calendar_id, 'o_bf_adults_max', 4, '5', '', 'int', NULL, 0, NULL),
			array($calendar_id, 'o_bf_captcha', 4, '1|3::3', 'No|Yes (Required)', 'enum', 12, 1, NULL),
			array($calendar_id, 'o_bf_children', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 5, 1, NULL),
			array($calendar_id, 'o_bf_children_max', 4, '5', '', 'int', NULL, 0, NULL),
			array($calendar_id, 'o_bf_city', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 9, 1, NULL),
			array($calendar_id, 'o_bf_country', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 7, 1, NULL),
			array($calendar_id, 'o_bf_email', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 2, 1, NULL),
			array($calendar_id, 'o_bf_name', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 1, 1, NULL),
			array($calendar_id, 'o_bf_notes', 4, '1|2|3::1', 'No|Yes|Yes (Required)', 'enum', 11, 1, NULL),
			array($calendar_id, 'o_bf_phone', 4, '1|2|3::3', 'No|Yes|Yes (Required)', 'enum', 3, 1, NULL),
			array($calendar_id, 'o_bf_state', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 8, 1, NULL),
			array($calendar_id, 'o_bf_terms', 4, '1|3::3', 'No|Yes (Required)', 'enum', 13, 1, NULL),
			array($calendar_id, 'o_bf_zip', 4, '1|2|3::2', 'No|Yes|Yes (Required)', 'enum', 10, 1, NULL),
			array($calendar_id, 'o_bookings_per_day', 3, '1', NULL, 'int', 5, 1, NULL),
			array($calendar_id, 'o_booking_behavior', 3, '1|2::1', 'Start & End date required|Single date', 'enum', 2, 1, NULL),
			array($calendar_id, 'o_border_inner', 2, '#e0dfde', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_border_inner_size', 2, '0|1|2|3|4|5|6|7|8|9::1', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_border_outer', 2, '#000000', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_border_outer_size', 2, '0|1|2|3|4|5|6|7|8|9::0', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_cancel_url', 7, 'http://www.phpjabbers.com/', NULL, 'string', 28, 1, NULL),
			array($calendar_id, 'o_color_available', 2, '#ffffff', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_color_booked', 2, '#ffffff', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_color_legend', 2, '#676F71', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_color_month', 2, '#ffffff', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_color_past', 2, '#c5c6c7', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_color_pending', 2, '#ffffff', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_color_weekday', 2, '#737576', NULL, 'color', 1, 1, NULL),
			array($calendar_id, 'o_currency', 7, 'AED|AFN|ALL|AMD|ANG|AOA|ARS|AUD|AWG|AZN|BAM|BBD|BDT|BGN|BHD|BIF|BMD|BND|BOB|BOV|BRL|BSD|BTN|BWP|BYR|BZD|CAD|CDF|CHE|CHF|CHW|CLF|CLP|CNY|COP|COU|CRC|CUC|CUP|CVE|CZK|DJF|DKK|DOP|DZD|EEK|EGP|ERN|ETB|EUR|FJD|FKP|GBP|GEL|GHS|GIP|GMD|GNF|GTQ|GYD|HKD|HNL|HRK|HTG|HUF|IDR|ILS|INR|IQD|IRR|ISK|JMD|JOD|JPY|KES|KGS|KHR|KMF|KPW|KRW|KWD|KYD|KZT|LAK|LBP|LKR|LRD|LSL|LTL|LVL|LYD|MAD|MDL|MGA|MKD|MMK|MNT|MOP|MRO|MUR|MVR|MWK|MXN|MXV|MYR|MZN|NAD|NGN|NIO|NOK|NPR|NZD|OMR|PAB|PEN|PGK|PHP|PKR|PLN|PYG|QAR|RON|RSD|RUB|RWF|SAR|SBD|SCR|SDG|SEK|SGD|SHP|SLL|SOS|SRD|STD|SYP|SZL|THB|TJS|TMT|TND|TOP|TRY|TTD|TWD|TZS|UAH|UGX|USD|USN|USS|UYU|UZS|VEF|VND|VUV|WST|XAF|XAG|XAU|XBA|XBB|XBC|XBD|XCD|XDR|XFU|XOF|XPD|XPF|XPT|XTS|XXX|YER|ZAR|ZMK|ZWL::EUR', NULL, 'enum', 6, 1, NULL),
			array($calendar_id, 'o_date_format', 1, 'd.m.Y|m.d.Y|Y.m.d|j.n.Y|n.j.Y|Y.n.j|d/m/Y|m/d/Y|Y/m/d|j/n/Y|n/j/Y|Y/n/j|d-m-Y|m-d-Y|Y-m-d|j-n-Y|n-j-Y|Y-n-j::d-m-Y', 'd.m.Y (25.09.2012)|m.d.Y (09.25.2012)|Y.m.d (2012.09.25)|j.n.Y (25.9.2012)|n.j.Y (9.25.2012)|Y.n.j (2012.9.25)|d/m/Y (25/09/2012)|m/d/Y (09/25/2012)|Y/m/d (2012/09/25)|j/n/Y (25/9/2012)|n/j/Y (9/25/2012)|Y/n/j (2012/9/25)|d-m-Y (25-09-2012)|m-d-Y (09-25-2012)|Y-m-d (2012-09-25)|j-n-Y (25-9-2012)|n-j-Y (9-25-2012)|Y-n-j (2012-9-25)', 'enum', 5, 1, NULL),
			array($calendar_id, 'o_deposit', 7, '10', NULL, 'float', 12, 1, NULL),
			array($calendar_id, 'o_deposit_type', 7, 'amount|percent::percent', 'Amount|Percent', 'enum', NULL, 0, NULL),
			array($calendar_id, 'o_disable_payments', 7, '1|0::0', NULL, 'bool', 4, 1, NULL),
			array($calendar_id, 'o_email_new_reservation', 8, "ID: {ReservationID}\r\n\r\nStart date: {StartDate}\r\nEnd date: {EndDate}\r\n\r\nPersonal details\r\nName: {Name}\r\nPhone: {Phone}\r\nEmail: {Email}", NULL, 'text', 2, 1, NULL),
			array($calendar_id, 'o_email_new_reservation_subject', 8, 'New reservation received', NULL, 'string', 1, 1, NULL),
			array($calendar_id, 'o_email_password_reminder', 8, "Dear {Name},\r\n\r\nYour password is: {Password}", NULL, 'text', 6, 1, NULL),
			array($calendar_id, 'o_email_password_reminder_subject', 8, 'Password reminder.', NULL, 'string', 5, 1, NULL),
			array($calendar_id, 'o_email_reservation_cancelled', 8, "Reservation has been cancelled.\r\n\r\nID: {ReservationID}\r\n\r\nStart date: {StartDate}\r\nEnd date: {EndDate}\r\n\r\nPersonal details\r\nName: {Name}\r\nPhone: {Phone}\r\nEmail: {Email}", NULL, 'text', 4, 1, NULL),
			array($calendar_id, 'o_email_reservation_cancelled_subject', 8, 'Reservation cancelled', NULL, 'string', 3, 1, NULL),
			array($calendar_id, 'o_font_family', 2, 'Arial|Arial Black|Book Antiqua|Century|Century Gothic|Comic Sans MS|Courier|Courier New|Impact|Lucida Console|Lucida Sans Unicode|Monotype Corsiva|Modern|Sans Serif|Serif|Small fonts|Symbol|Tahoma|Times New Roman|Verdana::Arial', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_family_legend', 2, 'Arial|Arial Black|Book Antiqua|Century|Century Gothic|Comic Sans MS|Courier|Courier New|Impact|Lucida Console|Lucida Sans Unicode|Monotype Corsiva|Modern|Sans Serif|Serif|Small fonts|Symbol|Tahoma|Times New Roman|Verdana::Arial', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_size_available', 2, '10|12|14|16|18|20|22|24|26|28|30::14', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_size_booked', 2, '10|12|14|16|18|20|22|24|26|28|30::14', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_size_legend', 2, '10|12|14|16|18|20|22|24|26|28|30::12', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_size_month', 2, '10|12|14|16|18|20|22|24|26|28|30::20', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_size_past', 2, '10|12|14|16|18|20|22|24|26|28|30::14', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_size_pending', 2, '10|12|14|16|18|20|22|24|26|28|30::14', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_size_weekday', 2, '10|12|14|16|18|20|22|24|26|28|30::12', NULL, 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_style_available', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: bold', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_style_booked', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: bold', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_style_legend', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: normal', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_style_month', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: normal', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_style_past', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: bold', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_style_pending', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: bold', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, NULL),
			array($calendar_id, 'o_font_style_weekday', 2, 'font-weight: normal|font-weight: bold|font-style: italic|font-style: underline|font-weight: bold; font-style: italic::font-weight: normal', 'Normal|Bold|Italic|Underline|Bold Italic', 'enum', 1, 1, NULL),
			array($calendar_id, 'o_month_year_format', 1, 'Month Year|Month, Year|Year Month|Year, Month::Month Year', NULL, 'enum', 6, 1, NULL),
			array($calendar_id, 'o_multi_lang', 99, '1|0::1', NULL, 'bool', NULL, 0, NULL),
			array($calendar_id, 'o_paypal_address', 7, '', NULL, 'string', 17, 1, NULL),
			array($calendar_id, 'o_price_based_on', 3, 'days|nights::days', 'Days|Nights', 'enum', 11, 1, NULL),
			array($calendar_id, 'o_price_plugin', 3, 'price|period::price', 'Day/Night|Periods', 'enum', 12, 1, NULL),
			array($calendar_id, 'o_require_all_within', 7, '10', NULL, 'int', 15, 1, NULL),
			array($calendar_id, 'o_security', 7, '20', NULL, 'float', 13, 1, NULL),
			array($calendar_id, 'o_send_email', 1, 'mail|smtp::mail', 'PHP mail()|SMTP', 'enum', 11, 1, NULL),
			array($calendar_id, 'o_show_legend', 1, '1|0::1', NULL, 'bool', 10, 1, NULL),
			array($calendar_id, 'o_show_prices', 1, '1|0::1', NULL, 'bool', 8, 1, NULL),
			array($calendar_id, 'o_show_week_numbers', 1, '1|0::1', NULL, 'bool', 9, 1, NULL),
			array($calendar_id, 'o_sms_new_reservation', 9, 'New reservation has been received.', NULL, 'text', 1, 1, NULL),
			array($calendar_id, 'o_sms_reservation_cancelled', 9, 'A reservation has been cancelled.', NULL, 'text', 2, 1, NULL),
			array($calendar_id, 'o_smtp_host', 1, NULL, NULL, 'string', 12, 1, NULL),
			array($calendar_id, 'o_smtp_pass', 1, NULL, NULL, 'string', 15, 1, NULL),
			array($calendar_id, 'o_smtp_port', 1, '25', NULL, 'int', 13, 1, NULL),
			array($calendar_id, 'o_smtp_user', 1, NULL, NULL, 'string', 14, 1, NULL),
			array($calendar_id, 'o_status_if_not_paid', 3, 'confirmed|pending|cancelled::pending', 'Confirmed|Pending|Cancelled', 'enum', 10, 1, NULL),
			array($calendar_id, 'o_status_if_paid', 3, 'confirmed|pending|cancelled::confirmed', 'Confirmed|Pending|Cancelled', 'enum', 9, 1, NULL),
			array($calendar_id, 'o_tax', 7, '10', NULL, 'float', 14, 1, NULL),
			array($calendar_id, 'o_allow_cash', 7, '1|0::1', NULL, 'bool', 26, 1, NULL),
			array($calendar_id, 'o_thankyou_page', 7, 'http://www.phpjabbers.com/', NULL, 'string', 27, 1, NULL),
			array($calendar_id, 'o_timezone', 1, '-43200|-39600|-36000|-32400|-28800|-25200|-21600|-18000|-14400|-10800|-7200|-3600|0|3600|7200|10800|14400|18000|21600|25200|28800|32400|36000|39600|43200|46800::0', 'GMT-12:00|GMT-11:00|GMT-10:00|GMT-09:00|GMT-08:00|GMT-07:00|GMT-06:00|GMT-05:00|GMT-04:00|GMT-03:00|GMT-02:00|GMT-01:00|GMT|GMT+01:00|GMT+02:00|GMT+03:00|GMT+04:00|GMT+05:00|GMT+06:00|GMT+07:00|GMT+08:00|GMT+09:00|GMT+10:00|GMT+11:00|GMT+12:00|GMT+13:00', 'enum', 7, 1, NULL),
			array($calendar_id, 'o_week_start', 1, '0|1|2|3|4|5|6::1', 'Sunday|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday', 'enum', 4, 1, NULL),
			array($calendar_id, 'o_fields_index', 99, 'd874fcc5fe73b90d770a544664a3775d', NULL, 'string', NULL, 0, NULL)
		);
		
		$this->setBatchFields(array('foreign_id', 'key', 'tab_id', 'value', 'label', 'type', 'order', 'is_visible', 'style'));
		$this->setBatchRows($data);
		$this->insertBatch();
	}
	
	public function initConfirmation($calendar_id, $locale_arr)
	{
		$pjMultiLangModel = pjMultiLangModel::factory();
		
		if($locale_arr != null)
		{
			$_arr = array();
			foreach($locale_arr as $v)
			{
				$_arr[$v['id']] = array('confirm_subject' => 'Reservation confirmation');
				
			}
			$pjMultiLangModel->reset()->saveMultiLang($_arr, $calendar_id, 'pjCalendar');
			
			$_arr = array();
			foreach($locale_arr as $v)
			{
				$_arr[$v['id']] = array('confirm_tokens' => "Thank you for your reservation.\r\n\r\nID: {ReservationID}\r\n\r\nStart date: {StartDate}\r\nEnd date: {EndDate}\r\n\r\nPersonal details\r\nName: {Name}\r\nPhone: {Phone}\r\nEmail: {Email}\r\n\r\nThis is the price for your reservation:\r\nPrice: {Price}\r\nTax: {Tax}\r\nTotal price: {TotalPrice}\r\nDeposit required to confirm your reservation: {Deposit}\r\n\r\nAdditional notes:\r\n{Notes}\r\n\r\nYou can cancel your reservation using this link\r\n{CancelURL}\r\n\r\nThank you\r\nManagement");
				
			}
			$pjMultiLangModel->reset()->saveMultiLang($_arr, $calendar_id, 'pjCalendar');
			
			$_arr = array();
			foreach($locale_arr as $v)
			{
				$_arr[$v['id']] = array('payment_subject' => 'Payment received');
				
			}
			$pjMultiLangModel->reset()->saveMultiLang($_arr, $calendar_id, 'pjCalendar');
			
			$_arr = array();
			foreach($locale_arr as $v)
			{
				$_arr[$v['id']] = array('payment_tokens' => "We\'ve received the payment for your reservation and it is now confirmed.\r\n\r\nID: {ReservationID}\r\n\r\nThank you\r\nManagement");
				
			}
			$pjMultiLangModel->reset()->saveMultiLang($_arr, $calendar_id, 'pjCalendar');
		}else{
			$pjMultiLangModel->reset()->saveMultiLang(array(
				1 => array('confirm_subject' => 'Reservation confirmation'),
				2 => array('confirm_subject' => 'Reservation confirmation'),
				3 => array('confirm_subject' => 'Reservation confirmation')
			), $calendar_id, 'pjCalendar');
			
			$pjMultiLangModel->reset()->saveMultiLang(array(
				1 => array('confirm_tokens' => "Thank you for your reservation.\r\n\r\nID: {ReservationID}\r\n\r\nStart date: {StartDate}\r\nEnd date: {EndDate}\r\n\r\nPersonal details\r\nName: {Name}\r\nPhone: {Phone}\r\nEmail: {Email}\r\n\r\nThis is the price for your reservation:\r\nPrice: {Price}\r\nTax: {Tax}\r\nTotal price: {TotalPrice}\r\nDeposit required to confirm your reservation: {Deposit}\r\n\r\nAdditional notes:\r\n{Notes}\r\n\r\nYou can cancel your reservation using this link\r\n{CancelURL}\r\n\r\nThank you\r\nManagement"),
				2 => array('confirm_tokens' => "Thank you for your reservation.\r\n\r\nID: {ReservationID}\r\n\r\nStart date: {StartDate}\r\nEnd date: {EndDate}\r\n\r\nPersonal details\r\nName: {Name}\r\nPhone: {Phone}\r\nEmail: {Email}\r\n\r\nThis is the price for your reservation:\r\nPrice: {Price}\r\nTax: {Tax}\r\nTotal price: {TotalPrice}\r\nDeposit required to confirm your reservation: {Deposit}\r\n\r\nAdditional notes:\r\n{Notes}\r\n\r\nYou can cancel your reservation using this link\r\n{CancelURL}\r\n\r\nThank you\r\nManagement"),
				3 => array('confirm_tokens' => "Thank you for your reservation.\r\n\r\nID: {ReservationID}\r\n\r\nStart date: {StartDate}\r\nEnd date: {EndDate}\r\n\r\nPersonal details\r\nName: {Name}\r\nPhone: {Phone}\r\nEmail: {Email}\r\n\r\nThis is the price for your reservation:\r\nPrice: {Price}\r\nTax: {Tax}\r\nTotal price: {TotalPrice}\r\nDeposit required to confirm your reservation: {Deposit}\r\n\r\nAdditional notes:\r\n{Notes}\r\n\r\nYou can cancel your reservation using this link\r\n{CancelURL}\r\n\r\nThank you\r\nManagement")
			), $calendar_id, 'pjCalendar');
			
			$pjMultiLangModel->reset()->saveMultiLang(array(
				1 => array('payment_subject' => 'Payment received'),
				2 => array('payment_subject' => 'Payment received'),
				3 => array('payment_subject' => 'Payment received')
			), $calendar_id, 'pjCalendar');
			
			$pjMultiLangModel->reset()->saveMultiLang(array(
				1 => array('payment_tokens' => "We\'ve received the payment for your reservation and it is now confirmed.\r\n\r\nID: {ReservationID}\r\n\r\nThank you\r\nManagement"),
				2 => array('payment_tokens' => "We\'ve received the payment for your reservation and it is now confirmed.\r\n\r\nID: {ReservationID}\r\n\r\nThank you\r\nManagement"),
				3 => array('payment_tokens' => "We\'ve received the payment for your reservation and it is now confirmed.\r\n\r\nID: {ReservationID}\r\n\r\nThank you\r\nManagement")
			), $calendar_id, 'pjCalendar');
		}
	}
}
?>