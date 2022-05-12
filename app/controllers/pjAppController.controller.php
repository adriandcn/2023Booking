<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();

	public $defaultLocale = 'admin_locale_id';

	public $defaultCalendarId = 'admin_calendar_id';

	public $defaultFields = 'fields';

	public $defaultFieldsIndex = 'fields_index';

	protected function loadSetFields($force=FALSE, $locale_id=NULL, $fields=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = $this->getLocaleId();
		}

		if (is_null($fields))
		{
			$fields = $this->defaultFields;
		}

		$registry = pjRegistry::getInstance();
		if ($force
				|| !isset($_SESSION[$this->defaultFieldsIndex])
				|| (isset($this->option_arr) && $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index'])
				|| !isset($_SESSION[$fields])
				|| empty($_SESSION[$fields]))
		{
			pjAppController::setFields($locale_id);

			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$fields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}

		if (isset($_SESSION[$fields]) && !empty($_SESSION[$fields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$fields]);
		}

		return TRUE;
	}

	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(compact('offset'));
    }

	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}

			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}

    public function beforeFilter()
    {
    	if (!in_array($_GET['controller'], array('pjFront', 'pjInstaller')))
    	{
    		$pjCalendarModel = pjCalendarModel::factory();
    		if ($this->isOwner())
    		{
    			//$pjCalendarModel->where('t1.user_id', $this->getUserId());
    			$pjCalendarModel->where('t1.id_usuario_servicio', $_SESSION['usuario_servicio']);
    		}

	    	$calendars = $pjCalendarModel
				->select('t1.*, t2.content AS `name`')
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('t1.id ASC')
				->findAll()->getDataPair('id');
			$this->set('calendars', $calendars);

			if ($this->getForeignId() === false && count($calendars) > 0)
			{
				$keys = array_keys($calendars);
				$this->setForeignId($keys[0]);

			}
    	}

    	$this->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
    	$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
    	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
    	$this->appendJs('jquery-migrate.min.js', $dm->getPath('jquery_migrate'), FALSE, FALSE);
    	$this->appendJs('pjAdminCore.js');
    	$this->appendCss('reset.css');

    	$this->appendJs('js/jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
    	$this->appendCss('css/smoothness/jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');

    	$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
    	$this->appendCss('admin.css');

    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$_SESSION[$this->defaultLocale] = $locale_arr[0]['id'];
				}
			}
			if (!in_array($_GET['action'], array('pjActionPreview')))
			{
				$this->loadSetFields();
			}
		}

		if ($_GET['controller'] == 'pjInvoice')
		{
			$foreign_arr = array();
			foreach ($calendars as $calendar)
			{
				$foreign_arr[] = array(
					'id' => $calendar['id'],
					'title' => $calendar['name']
				);
			}
			$this->set('foreign_arr', $foreign_arr);
		}
    }

    public function getForeignId()
	{
		if (isset($_SESSION[$this->defaultCalendarId]))
		{
			return $_SESSION[$this->defaultCalendarId];
		}
		return false;
	}

    public static function getTokens($booking_arr, $option_arr)
    {
    	$na = __('lblNA', true, false);
    	$c_name = !empty($booking_arr['c_name']) ? @$booking_arr['c_name'] : $na;
    	$c_email = !empty($booking_arr['c_email']) ? @$booking_arr['c_email'] : $na;
    	$c_phone = !empty($booking_arr['c_phone']) ? @$booking_arr['c_phone'] : $na;
    	$c_adults = $booking_arr['c_adults'] != '' ? @$booking_arr['c_adults'] : $na;
    	$c_children = $booking_arr['c_children'] != '' ? @$booking_arr['c_children'] : $na;
    	$c_notes = !empty($booking_arr['c_notes']) ? @$booking_arr['c_notes'] : $na;
    	$c_address = !empty($booking_arr['c_address']) ? @$booking_arr['c_address'] : $na;
    	$c_city = !empty($booking_arr['c_city']) ? @$booking_arr['c_city'] : $na;
    	$country = !empty($booking_arr['country']) ? @$booking_arr['country'] : $na;
    	$c_state = !empty($booking_arr['c_state']) ? @$booking_arr['c_state'] : $na;
    	$c_zip = !empty($booking_arr['c_zip']) ? @$booking_arr['c_zip'] : $na;
    	$cc_type = !empty($booking_arr['cc_type']) ? @$booking_arr['cc_type'] : $na;
    	$cc_num = !empty($booking_arr['cc_num']) ? @$booking_arr['cc_num'] : $na;
    	$cc_exp_month = @$booking_arr['payment_method'] == 'creditcard' ? (!empty($booking_arr['cc_exp_month']) ? @$booking_arr['cc_exp_month'] : $na) : $na;
    	$cc_exp_year = @$booking_arr['payment_method'] == 'creditcard' ? (!empty($booking_arr['cc_exp_year']) ? @$booking_arr['cc_exp_year'] : $na) : $na;
    	$cc_code = !empty($booking_arr['cc_code']) ? @$booking_arr['cc_code'] : $na;
    	$payment_method = !empty($booking_arr['payment_method']) ? @$booking_arr['payment_method'] : $na;

    	$total_price = number_format(@$booking_arr['amount'] + @$booking_arr['tax'], 2);

    	$search = array(
    		'{Name}', '{Email}', '{Phone}', '{Adults}', '{Children}',
    		'{Notes}', '{Address}', '{City}', '{Country}', '{State}',
    		'{Zip}', '{CCType}', '{CCNum}', '{CCExpMonth}', '{CCExpYear}',
    		'{CCSec}', '{PaymentMethod}', '{StartDate}', '{EndDate}', '{Deposit}',
    		'{Security}', '{Tax}', '{Price}', '{TotalPrice}', '{CalendarID}', '{ReservationID}',
    		'{ReservationUUID}', '{CancelURL}');
		$replace = array(
			$c_name, $c_email, $c_phone, $c_adults, $c_children,
			$c_notes, $c_address, $c_city, $country, $c_state,
			$c_zip, $cc_type, $cc_num, $cc_exp_month, $cc_exp_year,
			$cc_code, $payment_method, date(@$option_arr['o_date_format'], strtotime(@$booking_arr['date_from'])), date(@$option_arr['o_date_format'], strtotime(@$booking_arr['date_to'])), @$booking_arr['deposit'] . " " . @$option_arr['o_currency'],
			@$booking_arr['security'] . " " . @$option_arr['o_currency'], @$booking_arr['tax'] . " " . @$option_arr['o_currency'], @$booking_arr['amount'] . " " . @$option_arr['o_currency'], @$total_price . " " . @$option_arr['o_currency'], @$booking_arr['calendar_id'], @$booking_arr['id'],
			@$booking_arr['uuid'], sprintf("%sindex.php?controller=pjFront&action=pjActionCancel&cid=%u&id=%u&hash=%s", PJ_INSTALL_URL, @$booking_arr['calendar_id'], @$booking_arr['id'], sha1(@$booking_arr['id'] . PJ_SALT))
		);
		return compact('search', 'replace');
    }

	public function setForeignId($calendar_id)
	{
		$_SESSION[$this->defaultCalendarId] = (int) $calendar_id;
		return $this;
	}

    public static function setFields($locale)
    {
    	if(isset($_SESSION['lang_show_id']) && (int) $_SESSION['lang_show_id'] == 1)
		{
			$fields = pjMultiLangModel::factory()
				->select('CONCAT(t1.content, CONCAT(":", t2.id, ":")) AS content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}else{
			$fields = pjMultiLangModel::factory()
				->select('t1.content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $k => $v)
		{
			if (strpos($k, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $k);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $v;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}

	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}

	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function isEditor()
	{
		return $this->getRoleId() == 2;
	}

	public function isOwner()
	{
		return $this->getRoleId() == 3;
	}

	public function isPriceReady()
	{
		return $this->isAdmin() || $this->isOwner();
	}

	public function isPeriodReady()
	{
		return $this->isAdmin() || $this->isOwner();
	}

	public function isInvoiceReady()
	{
		return $this->isAdmin() || $this->isEditor() || $this->isOwner();
	}

	public function isCountryReady()
	{
		return $this->isAdmin();
	}

	public function isOneAdminReady()
	{
		return $this->isAdmin();
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}

	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');

		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array('app/web/upload');
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}

		return $result;
	}

	public function pjActionAfterInstall()
	{
		$this->setLayout('pjActionEmpty');

		$id = pjCalendarModel::factory()->set('user_id', 1)->insert()->getInsertId();
		if ($id !== false && (int) $id > 0)
		{
			pjMultiLangModel::factory()->saveMultiLang(array(
				1 => array('name' => 'Calendar 1'),
				2 => array('name' => 'Kalender 1'),
				3 => array('name' => 'Calendario 1')
			), $id, 'pjCalendar');

			$pjOptionModel = pjOptionModel::factory();
			$pjOptionModel->init($id);
			$pjOptionModel->initConfirmation($id, null);

			$data = $data = $pjOptionModel->reset()->getAllPairs($id);
			pjUtil::pjActionGenerateImages($id, $data);
		}

		return array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded');
	}

	public function notify($notification_id, $user_id=NULL, $params=array())
	{
		$map = array(
			3 => array('o_email_new_reservation_subject', 'o_email_new_reservation', 'o_sms_new_reservation'),
			4 => array('o_email_new_reservation_subject', 'o_email_new_reservation', 'o_sms_new_reservation'),
			5 => array('o_email_reservation_cancelled_subject', 'o_email_reservation_cancelled', 'o_sms_reservation_cancelled'),
			6 => array('o_email_reservation_cancelled_subject', 'o_email_reservation_cancelled', 'o_sms_reservation_cancelled')
		);

		$pjUserNotificationModel = pjUserNotificationModel::factory()
			->select('t1.type, t2.email, t2.phone')
			->join('pjUser', "t2.id=t1.user_id", 'inner')
			->where('t1.notification_id', $notification_id);

		if (!is_null($user_id))
		{
			$pjUserNotificationModel->where('t1.user_id', $user_id);
		}
		$recipients = $pjUserNotificationModel->findAll()->getData();

		$pjEmail = new pjEmail();
		$smsPlugin = (pjObject::getPlugin('pjSms') !== NULL);

		foreach ($recipients as $recipient)
		{
			switch ($recipient['type'])
			{
				case 'email':
					if (empty($recipient['email']))
					{
						continue;
					}
					if ($this->option_arr['o_send_email'] == 'smtp')
					{
						$pjEmail
							->setTransport('smtp')
							->setSmtpHost($this->option_arr['o_smtp_host'])
							->setSmtpPort($this->option_arr['o_smtp_port'])
							->setSmtpUser($this->option_arr['o_smtp_user'])
							->setSmtpPass($this->option_arr['o_smtp_pass'])
						;
					}

					$body = $this->option_arr[@$map[$notification_id][1]];
					switch ($notification_id)
					{
						default:
							$body = str_replace(
								array(
									'{Name}','{Email}','{Phone}','{Adults}','{Children}',
									'{Notes}','{Address}','{City}','{Country}','{State}',
									'{Zip}','{CCType}','{CCNum}','{CCExpMonth}','{CCExpYear}',
									'{CCSec}','{PaymentMethod}','{StartDate}','{EndDate}','{Deposit}',
									'{Security}','{Tax}','{Amount}','{CalendarID}','{ReservationID}',
									'{ReservationUUID}', '{CancelURL}'
								),
								array(
									@$params['c_name'], @$params['c_email'], @$params['c_phone'], @$params['c_adults'], @$params['c_children'],
									@$params['c_notes'], @$params['c_address'], @$params['c_city'], @$params['country'], @$params['c_state'],
									@$params['c_zip'], @$params['cc_type'], @$params['cc_num'], @$params['cc_exp_month'], @$params['cc_exp_year'],
									@$params['cc_code'], @$params['payment_method'], pjUtil::formatDate(@$params['date_from'], 'Y-m-d', $this->option_arr['o_date_format']), pjUtil::formatDate(@$params['date_to'], 'Y-m-d', $this->option_arr['o_date_format']), pjUtil::formatCurrencySign(@$params['deposit'], $this->option_arr['o_currency']),
									pjUtil::formatCurrencySign(@$params['security'], $this->option_arr['o_currency']), pjUtil::formatCurrencySign(@$params['tax'], $this->option_arr['o_currency']), pjUtil::formatCurrencySign(@$params['amount'], $this->option_arr['o_currency']), @$params['calendar_id'], @$params['id'],
									@$params['uuid'], sprintf('<a href="%1$sindex.php?controller=pjFront&action=pjActionCancel&cid=%2$u&id=%3$u&hash=%4$s">%1$sindex.php?controller=pjFront&action=pjActionCancel&cid=%2$u&id=%3$u&hash=%4$s</a>', PJ_INSTALL_URL, @$params['calendar_id'], @$params['id'], sha1(@$params['id'] . PJ_SALT))
								),
								$body);
							break;
					}

					$pjEmail->setFrom($recipient['email'])
						->setTo($recipient['email'])
						->setSubject($this->option_arr[@$map[$notification_id][0]])
						->setContentType('text/html')
						->send(nl2br($body));
					break;
				case 'sms':
					if (empty($recipient['phone']) || !$smsPlugin)
					{
						continue;
					}
					$this->requestAction(array(
						'controller' => 'pjSms',
						'action' => 'pjActionSend',
						'params' => array(
							'number' => $recipient['phone'],
							'text' => $this->option_arr[@$map[$notification_id][2]],
							'type' => 'unicode',
							'key' => md5($this->option_arr['private_key'] . PJ_SALT)
						)
					), array('return'));
					break;
			}
		}
	}

	protected function pjActionGenerateInvoice($reservation_id)
	{
		if (!isset($reservation_id) || (int) $reservation_id <= 0)
		{
			return array('status' => 'ERR', 'code' => 400, 'text' => 'ID is not set ot invalid.');
		}
		$arr = pjReservationModel::factory()->find($reservation_id)->getData();
		if (empty($arr))
		{
			return array('status' => 'ERR', 'code' => 404, 'text' => 'Reservation not found.');
		}
		$map = array(
			'Confirmed' => 'paid',
			'Pending' => 'not_paid',
			'Cancelled' => 'cancelled'
		);

		$deposit = $this->option_arr['o_deposit_type'] == 'percent' ? ($arr['amount'] * $this->option_arr['o_deposit']) / 100 : $this->option_arr['o_deposit'];

		$response = $this->requestAction(
			array(
	    		'controller' => 'pjInvoice',
	    		'action' => 'pjActionCreate',
	    		'params' => array(
    				'key' => md5($this->option_arr['private_key'] . PJ_SALT),

					'uuid' => pjInvoiceModel::factory()->getInvoiceID(),
					'order_id' => $arr['uuid'],
					'foreign_id' => $arr['calendar_id'],
					'issue_date' => ':CURDATE()',
					'due_date' => ':CURDATE()',
					'created' => ':NOW()',
	    			'payment_method' => $arr['payment_method'],
					'status' => @$map[$arr['status']],
					'subtotal' => $arr['amount'],

					'tax' => $arr['tax'],

					'total' => $arr['amount'] + $arr['tax'],
					'paid_deposit' => $arr['deposit'],
					'amount_due' => $arr['amount'] + $arr['tax'] + $arr['security'] - $arr['deposit'],
					'currency' => $this->option_arr['o_currency'],
					'notes' => $arr['c_notes'],
					'b_billing_address' => $arr['c_address'],
					'b_name' => $arr['c_name'],
					'b_address' => $arr['c_address'],
					'b_street_address' => $arr['c_address'],
	    			'b_country' => $arr['c_country'],
					'b_city' => $arr['c_city'],
					'b_state' => $arr['c_state'],
					'b_zip' => $arr['c_zip'],
					'b_phone' => $arr['c_phone'],
					'b_email' => $arr['c_email'],
					'items' => array(
						array(
							'name' => 'Reservation deposit',
							'description' => sprintf("%s - %s; adults: %u; children: %u",
								pjUtil::formatDate($arr['date_from'], 'Y-m-d', $this->option_arr['o_date_format']),
								pjUtil::formatDate($arr['date_to'], 'Y-m-d', $this->option_arr['o_date_format']),
								$arr['c_adults'], $arr['c_children']),
							'qty' => 1,
							'unit_price' => $arr['deposit'],
							'amount' => $arr['deposit']
						),
						array(
							'name' => 'Security deposit',
							'description' => NULL,
							'qty' => 1,
							'unit_price' => $arr['security'],
							'amount' => $arr['security']
						)
					)
					)
    		),
    		array('return')
		);

		return $response;
	}

	protected function pjActionCheckDt($date_from, $date_to, $calendar_id=NULL, $id=NULL, $backend=false)
	{
		$calendar_id = !empty($calendar_id) ? (int) $calendar_id : $this->getForeignId();

		if ($backend && $calendar_id != $this->getForeignId())
		{
			$option_arr = pjOptionModel::factory()->getPairs($calendar_id);
		} else {
			$option_arr = $this->option_arr;
		}

		if ($option_arr['o_price_based_on'] == 'nights' && $date_from == $date_to)
		{
			return array('status' => 'ERR', 'code' => 100, 'text' => '');
		}

		$pjReservationModel = pjReservationModel::factory();

		$info = $pjReservationModel
			->prepare(sprintf("SELECT `date_from`, `date_to`
				FROM `%1\$s`
				WHERE `calendar_id` = :calendar_id
				%2\$s
				AND `status` != :status
				AND ((`date_from` BETWEEN :date_from AND :date_to)
				OR ( `date_to` BETWEEN :date_from AND :date_to)
				OR ( `date_from` <= :date_from AND `date_to` >= :date_to))",
				$pjReservationModel->getTable(), (!empty($id) ? " AND `id` != :id" : NULL),
				($option_arr['o_price_based_on'] == 'nights' ? '<' : '<='),
				($option_arr['o_price_based_on'] == 'nights' ? '>' : '>=')
			))
			->exec(array(
				'calendar_id' => $calendar_id,
				'status' => 'Cancelled',
				'date_from' => $date_from,
				'date_to' => $date_to,
				'id' => $id
			))
			->getData();

		$morning = array();
		$afternoon = array();
		$av_arr = array();
		$booked_arr = array();
		$nights_mode = false;
		if ($option_arr['o_price_based_on'] == 'nights')
		{
			$nights_mode = true;
		}
		if(isset($info) && count($info)  >0)
		{
			foreach ($info as $res)
			{
				$dt_from = strtotime($res['date_from']);
				$dt_to = strtotime($res['date_to']);
				for($i = $dt_from; $i <= $dt_to; $i = strtotime('+1 day', $i))
				{
					if(!empty($res['price_based_on']) && in_array($res['price_based_on'], array('nights', 'days')))
					{
						if($res['price_based_on'] == 'nights')
						{
							$nights_mode = true;
						}
					}
					if ($i == $dt_from && $nights_mode){
						$afternoon[$i] += 1;
					}elseif ($i == $dt_to && $nights_mode) {
						$morning[$i] += 1;
					}else {
						$booked_arr[$i] += 1;
					}
				}
			}
		}
		$s_from = strtotime($date_from);
		$s_to = strtotime($date_to);
  		for($z = $s_from; $z <= $s_to; $z = strtotime('+1 day', $z))
		{
			if(isset($booked_arr[$z]) || isset($morning[$z]) || isset($afternoon[$z]))
			{
				$booked_value = isset($booked_arr[$z]) ? $booked_arr[$z] : 0;
				$monring_value = isset($morning[$z]) ? $morning[$z] : 0;
				$afternoon_value = isset($afternoon[$z]) ? $afternoon[$z] : 0;

				$booked_value += min($monring_value,$afternoon_value);
				$morning[$z] -= min($monring_value, $afternoon_value);
				$afternoon[$z] -= min($monring_value, $afternoon_value);

				$av_arr[$z] = $booked_value;
				if($morning[$z] >= $afternoon[$z])
				{
					if($z > $s_from && $z <= $s_to)
					{
						$av_arr[$z] = $booked_value + ($morning[$z] );
					}
				}else{
					if($z >= $s_from && $z < $s_to)
					{
						$av_arr[$z] = $booked_value + ($afternoon[$z]);
					}
				}
			}else{
				$av_arr[$z] = 0;
			}
		}
		$cnt = max($av_arr);

		if(empty($id))
		{
			if ($cnt < (int) $option_arr['o_bookings_per_day'])
			{
				$result = array('status' => 'OK', 'code' => 200, 'text' => '');
			} else {
				$result = array('status' => 'ERR', 'code' => 100, 'text' => '');
			}
		}else{
			if ($cnt < (int) $option_arr['o_bookings_per_day'])
			{
				$result = array('status' => 'OK', 'code' => 200, 'text' => '');
			} else {
				$result = array('status' => 'ERR', 'code' => 100, 'text' => '');
			}
		}
		return $result;
	}

	static public function getFromEmail()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;
	}
}
?>