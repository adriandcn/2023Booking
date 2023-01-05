<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{
	public $defaultCaptcha = 'StivaSoftCaptcha';

	public $defaultLocale = 'front_locale_id';

	public $defaultCalendar = 'ABCalendar';

	public function __construct()
	{
		$this->setLayout('pjActionFront');

		self::allowCORS();
	}

	public function afterFilter()
	{
		$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();

		$this->set('locale_arr', $locale_arr);
	}

	public function beforeFilter()
	{
		if (isset($_GET['cid']) && (int) $_GET['cid'] > 0)
		{
			$OptionModel = pjOptionModel::factory();
			$this->option_arr = $OptionModel->getPairs($_GET['cid']);
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
		}

		if ((isset($_GET['cid']) && (int) $_GET['cid'] > 0) || (in_array($_GET['action'], array('pjActionGetAvailability'))))
		{
			if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
			{
				$this->pjActionSetLocale($_GET['locale']);
			}

			if ($this->pjActionGetLocale() === FALSE)
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->pjActionSetLocale($locale_arr[0]['id']);
				}
			}
			if (!in_array($_GET['action'], array('pjActionLoadCss')))
			{
				$this->loadSetFields();
			}
		}
	}

	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}

	public function pjActionLoad()
	{
		header("Content-Type: text/javascript; charset=utf-8");
		if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
		{
			$this->pjActionSetLocale($_GET['locale']);
			$this->loadSetFields(true);
		}
		$limit_arr = pjLimitModel::factory()
		->select('t1.min_nights, t1.max_nights, UNIX_TIMESTAMP(t1.date_from) AS ts_from, UNIX_TIMESTAMP(t1.date_to) AS ts_to')
		->where('t1.calendar_id', $_GET['cid'])
		->findAll()
		->getData();

		foreach ($limit_arr as $k => $limit)
		{
			$limit_arr[$k] = array_map("intval", $limit);
		}

		$this->set('limit_arr', $limit_arr);
	}

	public function pjActionLoadCalendar()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$limit_arr = pjLimitModel::factory()
			->select('t1.min_nights, t1.max_nights, UNIX_TIMESTAMP(t1.date_from) AS ts_from, UNIX_TIMESTAMP(t1.date_to) AS ts_to')
			->where('t1.calendar_id', $_GET['cid'])
			->findAll()
			->getData();

			foreach ($limit_arr as $k => $limit)
			{
				$limit_arr[$k] = array_map("intval", $limit);
			}

			$this->set('limit_arr', $limit_arr);
		}
	}

	public function pjActionLoadAvail()
	{
		$this->setAjax(true);
	}

	public function pjActionLoadAvailability()
	{
		header("Content-Type: text/javascript; charset=utf-8");
		if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
		{
			$this->pjActionSetLocale($_GET['locale']);
			$this->loadSetFields(true);
		}
		if ((isset($_GET['year']) && preg_match('/^(19|20)\d{2}$/', $_GET['year'])) === FALSE)
		{
			$arr = pjCalendarModel::factory()
			->select("t1.*, t2.value AS `o_timezone`")
			->join('pjOption', "t2.foreign_id=t1.id AND t2.key='o_timezone'", 'inner')
			->orderBy('t1.id ASC')
			->findAll()
			->limit(1)
			->getDataIndex(0);

			if ($arr !== FALSE && isset($arr['o_timezone']))
			{
				pjFront::pjActionSetTime($arr['o_timezone']);
			}
		}
	}

	public function pjActionCancel()
	{
		$this->setLayout('pjActionIframe');

		if (isset($_GET['id']) && (int) $_GET['id'] > 0 && isset($_GET['cid']) && (int) $_GET['cid'] > 0 &&
			isset($_GET['hash']) && !empty($_GET['hash']) && $_GET['hash'] == sha1($_GET['id'] . PJ_SALT))
		{
			$arr = pjReservationModel::factory()
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
					AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.content AS country, t3.user_id", PJ_SALT))
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
				->join('pjCalendar', 't3.id=t1.calendar_id', 'left outer')
				->where('t1.id', $_GET['id'])
				->where('t1.calendar_id', $_GET['cid'])
				->limit(1)
				->findAll()
				->getData();

			if (!empty($arr))
			{
				$arr = $arr[0];
			}
			if (isset($_POST['cancel_booking']) && isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$err = NULL;
				if (pjReservationModel::factory()->set('id', $_POST['id'])->modify(array('status' => 'Cancelled'))->getAffectedRows() == 1)
				{
					$err = '&err=AR13';
					$this->notify(5, NULL, $arr);
					$this->notify(6, $arr['user_id'], $arr);

					if (!empty($this->option_arr['o_cancel_url']) && preg_match('/http(s)?:\/\//', $this->option_arr['o_cancel_url']))
					{
						pjUtil::redirect($this->option_arr['o_cancel_url']);
					}
				}
				pjUtil::redirect(sprintf("%sindex.php?controller=pjFront&action=pjActionCancel&cid=%u&id=%u&hash=%s%s", PJ_INSTALL_URL, $_GET['cid'], $_GET['id'], $_GET['hash'], $err));
			}

			if (empty($arr))
			{
				$this->set('status', 'AR16');
			} else {
				$this->set('arr', $arr);
			}
		} else {
			$this->set('status', 'AR15');
		}

		$this
			->appendCss('admin.css')
			->appendCss('pj-button.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
	}

	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		header("Cache-Control: max-age=3600, private");
		$Captcha = new pjCaptcha(PJ_WEB_PATH . 'obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage(PJ_IMG_PATH . 'button.png');
		$Captcha->init(isset($_GET['rand']) ? $_GET['rand'] : null);
		exit;
	}

	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			echo @$_SESSION[$this->defaultCaptcha] === strtoupper($_GET['captcha']) ? 'true' : 'false';
		}
		exit;
	}

	public function pjActionCheckDates()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$date_from = date("Y-m-d", @$_GET['start_dt']);
			$date_to = date("Y-m-d", @$_GET['end_dt']);
			if ($date_from > $date_to)
			{
				$tmp = $date_from;
				$date_from = $date_to;
				$date_to = $tmp;
			}
			$resp = $this->pjActionCheckDt($date_from, $date_to, @$_GET['cid'], NULL, TRUE);
			pjAppController::jsonResponse($resp);
		}
		exit;
	}

	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);

		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}

		if (!isset($_POST['x_invoice_num']))
		{
			$this->log('Missing arguments');
			exit;
		}

		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjReservationModel = pjReservationModel::factory();

		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['x_invoice_num'])
			->limit(1)
			->findAll()
			->getData();
		if (!empty($invoice_arr))
		{
			$invoice_arr = $invoice_arr[0];
			$booking_arr = $pjReservationModel
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
					AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.content AS country, t3.content AS payment_subject, t4.content AS payment_tokens", PJ_SALT))
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.locale=t1.locale_id AND t3.field='payment_subject'", 'left outer')
				->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.locale=t1.locale_id AND t4.field='payment_tokens'", 'left outer')
				->where('t1.uuid', $invoice_arr['order_id'])
				->limit(1)
				->findAll()
				->getData();
			if (!empty($booking_arr))
			{
				$booking_arr = $booking_arr[0];
				$option_arr = pjOptionModel::factory()->getPairs($booking_arr['calendar_id']);

				$params = array(
					'transkey' => $option_arr['o_authorize_key'],
					'x_login' => $option_arr['o_authorize_mid'],
					'md5_setting' => $option_arr['o_authorize_hash'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
				);

				$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
				if ($response !== FALSE && $response['status'] === 'OK')
				{
					$pjReservationModel
						->reset()
						->set('id', $booking_arr['id'])
						->modify(array('status' => ucfirst($option_arr['o_status_if_paid'])));

					$pjInvoiceModel
						->reset()
						->set('id', $invoice_arr['id'])
						->modify(array('status' => 'paid', 'modified' => ':NOW()'));

					pjFront::pjActionConfirmSend($option_arr, $booking_arr, 'payment');
				} elseif (!$response) {
					$this->log('Authorization failed');
				} else {
					$this->log('Booking not confirmed. ' . $response['response_reason_text']);
				}
			} else {
				$this->log('Booking not found');
			}
		} else {
			$this->log('Invoice not found');
		}
		exit;
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);

		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjReservationModel = pjReservationModel::factory();

		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['custom'])
			->limit(1)
			->findAll()
			->getData();

		if (!empty($invoice_arr))
		{
			$invoice_arr = $invoice_arr[0];
			$booking_arr = $pjReservationModel
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
					AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.content AS country, t3.content AS payment_subject, t4.content AS payment_tokens", PJ_SALT))
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.locale=t1.locale_id AND t3.field='payment_subject'", 'left outer')
				->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.locale=t1.locale_id AND t4.field='payment_tokens'", 'left outer')
				->where('t1.uuid', $invoice_arr['order_id'])
				->limit(1)
				->findAll()
				->getData();
			if (!empty($booking_arr))
			{
				$booking_arr = $booking_arr[0];
				$option_arr = pjOptionModel::factory()->getPairs($booking_arr['calendar_id']);
				$params = array(
					'txn_id' => @$booking_arr['txn_id'],
					'paypal_address' => @$option_arr['o_paypal_address'],
					'deposit' => @$booking_arr['deposit'],
					'currency' => $this->option_arr['o_currency'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
				);

				$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
				if ($response !== FALSE && $response['status'] === 'OK')
				{
					$this->log('Booking confirmed');
					$pjReservationModel->reset()->set('id', $booking_arr['id'])->modify(array(
						'status' => ucfirst($option_arr['o_status_if_paid']),
						'txn_id' => $response['transaction_id'],
						'processed_on' => ':NOW()'
					));

					$pjInvoiceModel
						->reset()
						->set('id', $invoice_arr['id'])
						->modify(array('status' => 'paid', 'modified' => ':NOW()'));

					pjFront::pjActionConfirmSend($option_arr, $booking_arr, 'payment');
				} elseif (!$response) {
					$this->log('Authorization failed');
				} else {
					$this->log('Booking not confirmed');
				}
			} else {
				$this->log('Booking not found');
			}
		} else {
			$this->log('Invoice not found');
		}
		exit;
	}

	private static function pjActionConfirmSend($option_arr, $booking_arr, $type)
	{
		if (!in_array($type, array('confirm', 'payment')))
		{
			return false;
		}
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$tokens = pjAppController::getTokens($booking_arr, $option_arr);

		$from_email = pjAppController::getFromEmail();

		switch ($type)
		{
			case 'confirm':
				$subject = str_replace($tokens['search'], $tokens['replace'], $booking_arr['confirm_subject']);
				$message = str_replace($tokens['search'], $tokens['replace'], $booking_arr['confirm_tokens']);
				//client
				if (!empty($subject) && !empty($message))
				{
					$Email
						->setTo($booking_arr['c_email'])
						->setFrom($from_email)
						->setSubject($subject)
						->send($message);
				}
				break;
			case 'payment':
				$subject = str_replace($tokens['search'], $tokens['replace'], $booking_arr['payment_subject']);
				$message = str_replace($tokens['search'], $tokens['replace'], $booking_arr['payment_tokens']);
				//client
				if (!empty($subject) && !empty($message))
				{
					$Email
						->setTo($booking_arr['c_email'])
						->setFrom($from_email)
						->setSubject($subject)
						->send($message);
				}
				break;
		}
	}

	public function pjActionGetBookingForm()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (!isset($_SESSION[$this->defaultCalendar]))
			{
				$_SESSION[$this->defaultCalendar] = array();
			}

			if (isset($_GET['start_dt']) && isset($_GET['end_dt']))
			{
				$start_dt = $_GET['start_dt'];
				$end_dt = $_GET['end_dt'];

				if ($_GET['start_dt'] > $_GET['end_dt'])
				{
					$start_dt = $_GET['end_dt'];
					$end_dt = $_GET['start_dt'];
				}

				$_SESSION[$this->defaultCalendar] = array_merge($_SESSION[$this->defaultCalendar], compact('start_dt', 'end_dt'));
			}

			if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
			{
				$this->set('price_arr', pjPriceModel::factory()->getPrice(
					$_GET['cid'],
					date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']),
					date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']),
					$this->option_arr,
					@$_SESSION[$this->defaultCalendar]['c_adults'],
					@$_SESSION[$this->defaultCalendar]['c_children']
				));

			} elseif (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period') {
				$this->set('price_arr', pjPeriodModel::factory()->getPrice(
					$_GET['cid'],
					date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']),
					date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']),
					$this->option_arr,
					@$_SESSION[$this->defaultCalendar]['c_adults'],
					@$_SESSION[$this->defaultCalendar]['c_children']
				));
			}

			if ((int) $this->option_arr['o_bf_terms'] !== 1)
			{
				$this->set('cal_arr', pjCalendarModel::factory()
					->select('t1.*, t2.content AS terms_url, t3.content AS terms_body')
					->join('pjMultiLang', sprintf("t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='terms_url' AND t2.locale='%u'", $this->pjActionGetLocale()), 'left outer')
					->join('pjMultiLang', sprintf("t3.model='pjCalendar' AND t3.foreign_id=t1.id AND t3.field='terms_body' AND t3.locale='%u'", $this->pjActionGetLocale()), 'left outer')
					->find($_GET['cid'])
					->getData()
				);
			}

			if ((int) $this->option_arr['o_bf_country'] !== 1)
			{
				$this->set('country_arr', pjCountryModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
					->where('t1.status', 'T')
					->orderBy('`name` ASC')
					->findAll()->getData());
			}
		}
	}

	public function pjActionGetCalendar()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (isset($this->option_arr['o_timezone']))
			{
				if (!isset($_GET['month']) && !isset($_GET['year']))
				{
					list($m, $y) = explode("-", date("n-Y"));
				} else {
					$m = (int) $_GET['month'];
					$y = (int) $_GET['year'];
				}

				$ABCalendar = new pjABCalendar();
				$ABCalendar
					->setWeekTitle(__('lblWeekTitle', true, false))
					->setShowNextLink((int) $_GET['view'] > 1 ? false : true)
					->setShowPrevLink((int) $_GET['view'] > 1 ? false : true)
					->setPrevLink("")
					->setNextLink("")
					->set('calendarId', $_GET['cid'])
					->set('reservationsInfo', pjReservationModel::factory()
						->getInfo(
							$_GET['cid'],
							date("Y-m-d", mktime(0, 0, 0, $m, 1, $y)),
							date("Y-m-d", mktime(23, 59, 59, $m + $_GET['view'], 0, $y)),
							$this->option_arr,
							NULL,
							1
						)
					)
					->set('options', $this->option_arr)
					->set('weekNumbers', (int) $this->option_arr['o_show_week_numbers'] === 1 ? true : false)
					->setStartDay($this->option_arr['o_week_start'])
					->setDayNames(__('day_names', true))
					->setWeekDays(__('days', true))
					->setNA(mb_strtoupper(__('lblNA', true), 'UTF-8'))
					->setMonthNames(__('months', true))
				;
				if (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period')
				{
					$periods = pjPeriodModel::factory()->getPeriodsPerDay($_GET['cid'], $m, $y, $_GET['view'], $this->option_arr['o_price_based_on'] == 'days');
					$ABCalendar->set('periods', $periods);
				}
				if ((int) $this->option_arr['o_show_prices'] === 1)
				{
					if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
					{
						$price_arr = pjPriceModel::factory()->getPricePerDay(
							$_GET['cid'],
							date("Y-m-d", mktime(0, 0, 0, $m, 1, $y)),
							date("Y-m-d", mktime(0, 0, 0, $m + $_GET['view'], 1, $y)),
							$this->option_arr
						);
						$ABCalendar
							->set('prices', $price_arr['priceData'])
							->set('showPrices', true);
					}
				}

				$this->set('ABCalendar', $ABCalendar);
			}
		}
	}

	public function pjActionGetPeriods()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (!isset($_GET['month']) && !isset($_GET['year']))
			{
				list($m, $y) = explode("-", date("n-Y"));
			} else {
				$m = (int) $_GET['month'];
				$y = (int) $_GET['year'];
			}

			$date_from = date("Y-m-d", mktime(0, 0, 0, $m, 1, $y));
			$date_to = date("Y-m-d", mktime(0, 0, 0, $m + (int) $_GET['view'], 0, $y));

			# http://en.wikipedia.org/wiki/De_Morgan's_laws
			# http://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap
			# (StartA <= EndB) and (EndA >= StartB)
			$periods = pjPeriodModel::factory()
				->where('t1.foreign_id', $_GET['cid'])
				->where('t1.start_date <=', $date_to)
				->where('t1.end_date >=', $date_from)
				->findAll()
				->getData();
			foreach ($periods as $k => $period)
			{
				$periods[$k]['start_ts'] = strtotime($period['start_date']);
    			$periods[$k]['end_ts'] = strtotime($period['end_date']);
			}

			pjAppController::jsonResponse($periods);
		}
		exit;
	}

	public function pjActionGetPrice()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
			{
				$this->set('price_arr', pjPriceModel::factory()->getPrice(
					$_GET['cid'],
					date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']),
					date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']),
					$this->option_arr,
					@$_GET['c_adults'],
					(int) $this->option_arr['o_bf_children'] !== 1 ? @$_GET['c_children'] : 0
				));
			} elseif (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period') {
				$this->set('price_arr', pjPeriodModel::factory()->getPrice(
					$_GET['cid'],
					date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']),
					date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']),
					$this->option_arr,
					@$_GET['c_adults'],
					(int) $this->option_arr['o_bf_children'] !== 1 ? @$_GET['c_children'] : 0
				));
			}
		}
	}

	public function pjActionGetPaymentForm(){
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$booking_arr = pjReservationModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.calendar_id AND t2.model = 'pjCalendar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->select('t1.*, t2.content as calendar_name')
				->find($_GET['reservation_id'])
				->getData();

			$invoice_arr = pjInvoiceModel::factory()->find($_GET['invoice_id'])->getData();

			switch ($_GET['payment_method'])
			{
				case 'paypal':
					$this->set('params', array(
						'name' => 'abPaypal',
						'id' => 'abPaypal',
						'target' => '_self',
						'business' => $this->option_arr['o_paypal_address'],
						'item_name' => $booking_arr['calendar_name'],
						//'custom' => $booking_arr['id'],
						'custom' => $invoice_arr['uuid'],
						'amount' => $booking_arr['deposit'],
						'currency_code' => $this->option_arr['o_currency'],
						'return' => $this->option_arr['o_thankyou_page'],
						'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaypal&cid=' . $_GET['cid']
					));
					break;
				case 'authorize':
					$this->set('params', array(
						'name' => 'abAuthorize',
						'id' => 'abAuthorize',
						'timezone' => $this->option_arr['o_authorize_tz'],
						'transkey' => $this->option_arr['o_authorize_key'],
						'x_login' => $this->option_arr['o_authorize_mid'],
						'x_description' => __('front_payment_authorize_title', true),
						'x_amount' => $booking_arr['deposit'],
						//'x_invoice_num' => $booking_arr['id'],
						'x_invoice_num' => $invoice_arr['uuid'],
						'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
						'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize&cid=' . $_GET['cid']
					));
					break;

			}

			$this->set('booking_arr', $booking_arr);
			$this->set('get', $_GET);
		}
	}

	public function pjActionGetSummaryForm(){
		$this->setAjax(true);

		//if ($this->isXHR())
		//{

			$fechaInicio = date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']);
			$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
			if($conn->connect_errno){
					echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
			}

			$fecha = date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']);
			$idCalendario = $_GET['cid'];
			$sqlConfirmada = "SELECT SUM(c_adults + IFNULL(c_children, 0)) as confirmed FROM booking_abcalendar_reservations where date_from = '".$fecha."' and status = 'Confirmed' and calendar_id = ".$idCalendario;
			$confirmada = $conn->query($sqlConfirmada);
			foreach ($confirmada as $row1) {
				$ccC = $row1['confirmed'];
			}

			$sqlPendiente = "SELECT SUM(c_adults + IFNULL(c_children, 0)) as confirmed FROM booking_abcalendar_reservations where date_from = '".$fecha."' and status = 'Pending' and calendar_id = ".$idCalendario;
			$pendiente = $conn->query($sqlPendiente);
			foreach ($pendiente as $row1) {
				$ccP = $row1['confirmed'];
			}

			$sqlBookingPerDay = "SELECT * FROM booking_abcalendar_options where foreign_id = $idCalendario AND `key` = 'o_bookings_per_day'";
			$perday = $conn->query($sqlBookingPerDay);
			foreach ($perday as $row2) {
				$o_bookings_per_day = $row2['value'];
			}

			if($ccC == NULL && $ccP == NULL ){

				$disponibles  = $o_bookings_per_day;
				$totalReservacion = $_POST['c_adults'] + $_POST['c_children'];
				$cuenta = $disponibles - $totalReservacion;

			}else{

				$disponibles  = $o_bookings_per_day - ($ccC + $ccP);
				$totalReservacion = $_POST['c_adults'] + $_POST['c_children'];
				$cuenta = $disponibles - $totalReservacion;

			}

			if($cuenta > -1){

				echo '01';
				$_SESSION[$this->defaultCalendar] = array_merge($_SESSION[$this->defaultCalendar], $_POST);

				if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
				{
					$this->set('price_arr', pjPriceModel::factory()->getPrice(
						$_GET['cid'],
						date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']),
						date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']),
						$this->option_arr,
						@$_SESSION[$this->defaultCalendar]['c_adults'],
						(int) $this->option_arr['o_bf_children'] !== 1 ? @$_SESSION[$this->defaultCalendar]['c_children'] : 0
					));
				} elseif (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period') {
					$this->set('price_arr', pjPeriodModel::factory()->getPrice(
						$_GET['cid'],
						date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']),
						date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']),
						$this->option_arr,
						@$_SESSION[$this->defaultCalendar]['c_adults'],
						(int) $this->option_arr['o_bf_children'] !== 1 ? @$_SESSION[$this->defaultCalendar]['c_children'] : 0
					));
				}

				if ((int) $this->option_arr['o_bf_country'] !== 1 && isset($_SESSION[$this->defaultCalendar]['c_country'])){
					$this->set('country_arr', pjCountryModel::factory()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
						->where('t1.status', 'T')
						->find($_SESSION[$this->defaultCalendar]['c_country'])->getData());
				}
            }else{
                echo '00 '. $_POST['c_adults'] .' '.  $_POST['c_children'];
            }
		//}
	}

	public function pjActionImage(){
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');

		$w = isset($_GET['width']) && (int) $_GET['width'] > 0 ? intval($_GET['width']) : 100;
		$h = isset($_GET['height']) && (int) $_GET['height'] > 0 ? intval($_GET['height']) : 100;

		# Spatial_anti-aliasing. Make an image larger then it's intended
		$width = $w * 10;
		$height = $h * 10;

		$image = imagecreatetruecolor($width, $height);
		if (function_exists('imageantialias'))
		{
			imageantialias($image, true);
		}
		$backgroundColor = pjUtil::html2rgb($_GET['color1']);
		$color = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
		imagefill($image, 0, 0, $color);

		if (isset($_GET['color2']) && !empty($_GET['color2']))
		{
			if ($_GET['color1'] == $_GET['color2'])
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
				$backgroundColor = pjUtil::html2rgb($_GET['color2']);
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

		header('Content-Type: image/jpeg');
		imagejpeg($new_image, null, 100);
		imagedestroy($image);
		imagedestroy($new_image);
		exit;
	}

	private static function pjActionSetTime($timezone){
		$offset = $timezone / 3600;
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

		return;
	}

	public function pjActionGetAvailability(){
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$locale = isset($_GET['locale']) && !empty($_GET['locale']) ? (int) $_GET['locale'] : $this->pjActionGetLocale();

			$pjCalendarModel = pjCalendarModel::factory()
				->select("t1.*, t2.content AS `title`, t3.value AS `o_bookings_per_day`, t4.value AS `o_timezone`")
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='$locale'", 'left outer')
				->join('pjOption', "t3.foreign_id=t1.id AND t3.key='o_bookings_per_day'", 'left outer')
				->join('pjOption', "t4.foreign_id=t1.id AND t4.key='o_timezone'", 'left outer');
			$pjReservationModel = pjReservationModel::factory();
			$pjOptionModel = pjOptionModel::factory();
			$arr = $pjCalendarModel->orderBy('t1.id ASC')->findAll()->getData();

			$last_timezone = NULL;
			foreach ($arr as $k => $calendar)
			{
				# Set timezone per calendar
				if (isset($calendar['o_timezone']) && $last_timezone != $calendar['o_timezone'])
				{
					# Cache timezone
					$last_timezone = $calendar['o_timezone'];

					pjFront::pjActionSetTime($calendar['o_timezone']);
				}

				list($Y, $n) = explode("-", date("Y-n"));
				$year = isset($_GET['year']) && !empty($_GET['year']) ? (int) $_GET['year'] : $Y;
				$month = isset($_GET['month']) && !empty($_GET['month']) ? (int) $_GET['month'] : $n;

				$arr[$k]['date_arr'] = $pjReservationModel->getInfo(
					$calendar['id'],
					date("Y-m-d", mktime(0, 0, 0, $month, 1, $year)),
					date("Y-m-d", mktime(0, 0, 0, $month + 1, 0, $year)),
					$pjOptionModel->reset()->getPairs($calendar['id']),
					NULL,
					1
				);
			}

			$this->set('arr', $arr);
		}
	}

	public function pjActionLoadAvailabilityCss(){

		header("Content-Type: text/css; charset=utf-8");

		$arr = array(
			array('file' => 'ABCalendar.Availability.css', 'path' => PJ_CSS_PATH),
			array('file' => 'ABCalendar.Availability.txt', 'path' => PJ_CSS_PATH)
		);
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();

			if ($string !== FALSE)
			{
				echo str_replace(
					array('../img/', '[background_nav]'),
					array(PJ_IMG_PATH, '#187c9a'),
					$string) . "\n";
			}
		}

		$pjOptionModel = pjOptionModel::factory();
		$arr = pjCalendarModel::factory()->findAll()->getData();

		ob_start();
		@readfile(PJ_CSS_PATH . 'availability.txt');
		$string = ob_get_contents();
		ob_end_clean();

		foreach ($arr as $calendar)
		{
			$option_arr = $pjOptionModel->reset()->getPairs($calendar['id']);
			if ($string !== FALSE && isset($option_arr['o_background_available']))
			{
				echo str_replace(
					array(
						'[calendarContainer]',
						'[URL]',
						'[cell_width]',
						'[cell_height]',
						'[background_available]',
						'[c_background_available]',
						'[background_booked]',
						'[c_background_booked]',
						'[background_empty]',
						'[background_month]',
						'[background_past]',
						'[background_pending]',
						'[c_background_pending]',
						'[background_select]',
						'[background_weekday]',
						'[border_inner]',
						'[border_inner_size]',
						'[border_outer]',
						'[border_outer_size]',
						'[color_available]',
						'[color_booked]',
						'[color_legend]',
						'[color_month]',
						'[color_past]',
						'[color_pending]',
						'[color_weekday]',
						'[font_family]',
						'[font_family_legend]',
						'[font_size_available]',
						'[font_size_booked]',
						'[font_size_legend]',
						'[font_size_month]',
						'[font_size_past]',
						'[font_size_pending]',
						'[font_size_weekday]',
						'[font_style_available]',
						'[font_style_booked]',
						'[font_style_legend]',
						'[font_style_month]',
						'[font_style_past]',
						'[font_style_pending]',
						'[font_style_weekday]'
					),
					array(
						'.abCal-id-' . $calendar['id'],
						PJ_INSTALL_URL,
						43,
						31,
						$option_arr['o_background_available'],
						str_replace('#', '', $option_arr['o_background_available']),
						$option_arr['o_background_booked'],
						str_replace('#', '', $option_arr['o_background_booked']),
						$option_arr['o_background_empty'],
						$option_arr['o_background_month'],
						$option_arr['o_background_past'],
						$option_arr['o_background_pending'],
						str_replace('#', '', $option_arr['o_background_pending']),
						$option_arr['o_background_select'],
						$option_arr['o_background_weekday'],
						$option_arr['o_border_inner'],
						$option_arr['o_border_inner_size'],
						$option_arr['o_border_outer'],
						$option_arr['o_border_outer_size'],
						$option_arr['o_color_available'],
						$option_arr['o_color_booked'],
						$option_arr['o_color_legend'],
						$option_arr['o_color_month'],
						$option_arr['o_color_past'],
						$option_arr['o_color_pending'],
						$option_arr['o_color_weekday'],
						$option_arr['o_font_family'],
						$option_arr['o_font_family_legend'],
						$option_arr['o_font_size_available'],
						$option_arr['o_font_size_booked'],
						$option_arr['o_font_size_legend'],
						$option_arr['o_font_size_month'],
						$option_arr['o_font_size_past'],
						$option_arr['o_font_size_pending'],
						$option_arr['o_font_size_weekday'],
						$option_arr['o_font_style_available'],
						$option_arr['o_font_style_booked'],
						$option_arr['o_font_style_legend'],
						$option_arr['o_font_style_month'],
						$option_arr['o_font_style_past'],
						$option_arr['o_font_style_pending'],
						$option_arr['o_font_style_weekday']
					),
					$string
				);
			}
		}

		exit;
	}

	public function pjActionLoadCss(){
		$arr = array(
			array('file' => 'ABCalendar.css', 'path' => PJ_CSS_PATH),
			array('file' => 'ABFonts.min.css', 'path' => PJ_CSS_PATH)
		);

		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();

			if ($string !== FALSE)
			{
				echo str_replace(
					array('../img/', '../fonts/'),
					array(PJ_IMG_PATH, PJ_FONT_PATH),
					$string) . "\n";
			}
		}

		ob_start();
		@readfile(PJ_CSS_PATH . 'ABCalendar.txt');
		$string = ob_get_contents();
		ob_end_clean();

		if ($string !== FALSE && isset($this->option_arr['o_show_week_numbers']))
		{
			echo str_replace(
				array(
					'[calendarContainer]',
					'[URL]',
					'[cell_width]',
					'[cell_height]',
					'[background_available]',
					'[c_background_available]',
					'[background_booked]',
					'[c_background_booked]',
					'[background_empty]',
					'[background_month]',
					'[background_nav]',
					'[background_nav_hover]',
					'[background_past]',
					'[background_pending]',
					'[c_background_pending]',
					'[background_select]',
					'[background_weekday]',
					'[border_inner]',
					'[border_inner_size]',
					'[border_outer]',
					'[border_outer_size]',
					'[color_available]',
					'[color_booked]',
					'[color_legend]',
					'[color_month]',
					'[color_past]',
					'[color_pending]',
					'[color_weekday]',
					'[font_family]',
					'[font_family_legend]',
					'[font_size_available]',
					'[font_size_booked]',
					'[font_size_legend]',
					'[font_size_month]',
					'[font_size_past]',
					'[font_size_pending]',
					'[font_size_weekday]',
					'[font_style_available]',
					'[font_style_booked]',
					'[font_style_legend]',
					'[font_style_month]',
					'[font_style_past]',
					'[font_style_pending]',
					'[font_style_weekday]'
				),
				array(
					'#abWrapper_' . $_GET['cid'],
					PJ_INSTALL_URL,
					number_format((100 / ((int) $this->option_arr['o_show_week_numbers'] === 1 ? 8 : 7)), 2, '.', ''),
					number_format(100 / 8, 2, '.', ''),
					$this->option_arr['o_background_available'],
					str_replace('#', '', $this->option_arr['o_background_available']),
					$this->option_arr['o_background_booked'],
					str_replace('#', '', $this->option_arr['o_background_booked']),
					$this->option_arr['o_background_empty'],
					$this->option_arr['o_background_month'],
					$this->option_arr['o_background_nav'],
					$this->option_arr['o_background_nav_hover'],
					$this->option_arr['o_background_past'],
					$this->option_arr['o_background_pending'],
					str_replace('#', '', $this->option_arr['o_background_pending']),
					$this->option_arr['o_background_select'],
					$this->option_arr['o_background_weekday'],
					$this->option_arr['o_border_inner'],
					$this->option_arr['o_border_inner_size'],
					$this->option_arr['o_border_outer'],
					$this->option_arr['o_border_outer_size'],
					$this->option_arr['o_color_available'],
					$this->option_arr['o_color_booked'],
					$this->option_arr['o_color_legend'],
					$this->option_arr['o_color_month'],
					$this->option_arr['o_color_past'],
					$this->option_arr['o_color_pending'],
					$this->option_arr['o_color_weekday'],
					$this->option_arr['o_font_family'],
					$this->option_arr['o_font_family_legend'],
					$this->option_arr['o_font_size_available'],
					$this->option_arr['o_font_size_booked'],
					$this->option_arr['o_font_size_legend'],
					$this->option_arr['o_font_size_month'],
					$this->option_arr['o_font_size_past'],
					$this->option_arr['o_font_size_pending'],
					$this->option_arr['o_font_size_weekday'],
					$this->option_arr['o_font_style_available'],
					$this->option_arr['o_font_style_booked'],
					$this->option_arr['o_font_style_legend'],
					$this->option_arr['o_font_style_month'],
					$this->option_arr['o_font_style_past'],
					$this->option_arr['o_font_style_pending'],
					$this->option_arr['o_font_style_weekday']
				),
				$string
			);
		}
		exit;
	}

	public function pjActionBookingSave()
	{

		//****************************************************************//
		// ACTUALIZO LA URL EN LA TABLA OPTION E INSERTO EL MD5 EN       //
		// 			LA TABLA TOKENS 			         //
		//****************************************************************//
		$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
		if($conn->connect_errno){
		     echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}
		$idCalendario = $_GET["cid"];
		$datetime = date('Y-m-d H:i:s') ;

		//INSERTO EN LA TABLA TOKEN PARA PODER VERIFICAR
		$sql1 = "INSERT INTO tokens (id_calendario,uuid,consumido,created_at,updated_at) VALUES($idCalendario,'$ramdomKey',false,'$datetime','$datetime') ";
		$conn->query($sql1);

		$this->setAjax(true);

		//if ($this->isXHR())
		//{
		$data = array();
		if (isset($_SESSION[$this->defaultCalendar]) && is_array($_SESSION[$this->defaultCalendar]) && count($_SESSION[$this->defaultCalendar]) > 0)
		{
			$data['ip'] = $_SERVER['REMOTE_ADDR'];
			$data['calendar_id'] = $_GET['cid'];
			$data['uuid'] = pjUtil::uuid();
			$data['status'] = ucfirst($this->option_arr['o_status_if_not_paid']);
			$data['locale_id'] = $this->pjActionGetLocale();

			$data['date_from'] = date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']);
			$data['date_to'] = date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']);
			$data['price_based_on'] = $this->option_arr['o_price_based_on'];
			$data['sales_origin'] = 'Desktop';

			$resp = $this->pjActionCheckDt($data['date_from'], $data['date_to'], $data['calendar_id'], NULL, TRUE);
			if ($resp['status'] == 'ERR')
			{
				pjAppController::jsonResponse($resp);
			}

			$data = array_merge($_SESSION[$this->defaultCalendar], $data);

			if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
			{
				$price = pjPriceModel::factory()->getPrice(
					$data['calendar_id'],
					$data['date_from'],
					$data['date_to'],
					$this->option_arr,
					@$data['c_adults'],
					(int) $this->option_arr['o_bf_children'] !== 1 ? @$data['c_children'] : 0
				);
			} elseif (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period') {
				$price = pjPeriodModel::factory()->getPrice(
					$data['calendar_id'],
					$data['date_from'],
					$data['date_to'],
					$this->option_arr,
					@$data['c_adults'],
					(int) $this->option_arr['o_bf_children'] !== 1 ? @$data['c_children'] : 0
				);
			}

			// ***********************************************************
			// SE LE SUMA AL TOTAL SI EL CHECK ESTA MARCADO
			// ***********************************************************
			if($_GET['check'] === true || $_GET['check'] === 'true'){
				$data['amount'] = @$price['amount'] + 2;
				$data['deposit'] = @$price['deposit'] + 2;
				$data['tax'] = @$price['tax'];
				$data['security'] = @$price['security'];
			}else{
				$data['amount'] = @$price['amount'];
				$data['deposit'] = @$price['deposit'];
				$data['tax'] = @$price['tax'];
				$data['security'] = @$price['security'];
			}
			// ***********************************************************
			// ***********************************************************

			// ***********************************************************
			// SE LE RESTA AL TOTAL SI TIENE UN CUPON
			// ***********************************************************
			if((int) $_GET['cupon'] > 0){
				$operacion = $data['amount'] - ( ( $data['amount'] * (float)$_GET['cupon']) / 100);
				$data['amount'] = $operacion;
				$data['deposit'] = $operacion;

				$cupon_id = $_GET['cupon_id'];
				//actualizar el cupon
				$sql = "UPDATE booking_abcalendar_coupon SET estado = 1 WHERE id = $cupon_id";
				$conn->query($sql);
			}
			// ***********************************************************
			// ***********************************************************

			if (isset($data['payment_method']) && $data['payment_method'] != 'creditcard')
			{
				unset($data['cc_type']);
				unset($data['cc_num']);
				unset($data['cc_exp_month']);
				unset($data['cc_exp_year']);
				unset($data['cc_code']);
			}

			$pjReservationModel = new pjReservationModel();
			if (!$pjReservationModel->validates($data))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Reservations data does not validate.'));
			}

			$reservation_id = $pjReservationModel->setAttributes($data)->insert()->getInsertId();
			if ($reservation_id === false || (int) $reservation_id === 0)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Reservation was not saved.'));
			}

			$invoice_arr = $this->pjActionGenerateInvoice($reservation_id);

			// ***********************************************************
			// SE ACTUALIZA EL ID DE LA RESERVA QUE CONSUMIO EL CUPON
			// ***********************************************************
			if((int) $_GET['cupon'] > 0){
				$cupon_id = $_GET['cupon_id'];
				//actualizar el cupon
				$sql = "UPDATE booking_abcalendar_coupon SET id_res_cons = $reservation_id WHERE id = $cupon_id";
				$conn->query($sql);
			}
			// ***********************************************************
			// ***********************************************************

			$_SESSION[$this->defaultCalendar] = NULL;
			unset($_SESSION[$this->defaultCalendar]);

			if (isset($_SESSION[$this->defaultCaptcha]))
			{
				$_SESSION[$this->defaultCaptcha] = NULL;
				unset($_SESSION[$this->defaultCaptcha]);
			}

			$calendar_arr = pjCalendarModel::factory()->find($_GET['cid'])->getData();

			$params = $pjReservationModel->reset()
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
					AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.content AS country, t3.content AS confirm_subject, t4.content AS confirm_tokens", PJ_SALT))
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.field='confirm_subject' AND t3.locale=t1.locale_id", 'left outer')
				->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.field='confirm_tokens' AND t4.locale=t1.locale_id", 'left outer')
				->find($reservation_id)->getData();
			if (!empty($params))
			{
				$this->notify(3, NULL, $params);
				$this->notify(4, $calendar_arr['user_id'], $params);
				pjFront::pjActionConfirmSend($this->option_arr, $params, 'confirm');
			}

			/*****************************************************************/
			/*                         PARA EL PAGO CON CASH                               */
			/*****************************************************************/
			if (isset($data['payment_method']) && $data['payment_method'] == 'cash')
			{
				/*$sqlCash = "SELECT content FROM booking_abcalendar_multi_lang WHERE model = 'pjCalendar' AND  field = 'name' AND foreign_id =". $data['calendar_id'];
				$cash = $conn->query($sqlCash);
				$nombreCalendario = "";
				foreach ($cash as $row2) {
							$nombreCalendario = $row2['content'];
						}

				$conn->query("INSERT INTO cashes (id_reserva, nombreCalendario, estadoPago, fechaPago, montoPago,consumido, created_at, updated_at)
				VALUES(".$reservation_id.",'".$nombreCalendario."', 'PorProcesar','".date("Y-m-d H:i:s")."', '".$data['amount']."',false ,'".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."')");*/
				$tokenReserva = md5($reservation_id);
				$sql = "UPDATE booking_abcalendar_reservations SET token_consulta = '$tokenReserva'
						WHERE id = $reservation_id";
				$conn->query($sql);
				/*RECUPERO EL NOMBRE DEL CALENDARIO*/
				$sqlCash = "SELECT content FROM booking_abcalendar_multi_lang WHERE model = 'pjCalendar' AND  field = 'name' AND foreign_id =". $data['calendar_id'];
				$cash = $conn->query($sqlCash);
				$nombreCalendario = "";
				foreach ($cash as $row2) {
							$nombreCalendario = $row2['content'];
						}

				/*GUARDO LA INFORMACION EN LA TABLA DE PAGOS*/
				$conn->query("INSERT INTO pagos(`calendario_id`, `reservacion_id`, `nombre_calendario`, `tipo`, `estado_pago`, `fecha_pago`, `consumido`,`token`)
				VALUES(".$data['calendar_id'].",".$reservation_id.",'".$nombreCalendario."', 'cash','Pendiente', '".date("Y-m-d H:i:s")."',0,'".$tokenReserva."')");
				$urlCash = PJ_URL_LARAVEL1.'/confirmacionEfectivo/'.$tokenReserva;

			}

			/*****************************************************************/
			/*              PARA EL PAGO CON TARJETA DE CREDITO                  */
			/*     ARMO LA INFORMACION Y REDIRECCIONO A PAGO MEDIOS  */
			/*****************************************************************/
			if (isset($data['payment_method']) && $data['payment_method'] == 'creditcard'){

				$tokenReserva = md5($reservation_id);
				$sql = "UPDATE booking_abcalendar_reservations SET token_consulta = '$tokenReserva'
						WHERE id = $reservation_id";
				$conn->query($sql);
				/*RECUPERO EL NOMBRE DEL CALENDARIO*/
				$sqlCash = "SELECT content FROM booking_abcalendar_multi_lang WHERE model = 'pjCalendar' AND  field = 'name' AND foreign_id =". $data['calendar_id'];
				$cash = $conn->query($sqlCash);
				$nombreCalendario = "";
				foreach ($cash as $row2) {
					$nombreCalendario = $row2['content'];
				}

				/*GUARDO LA INFORMACION EN LA TABLA DE PAGOS*/
				$conn->query("INSERT INTO pagos(`calendario_id`, `reservacion_id`, `nombre_calendario`, `tipo`, `estado_pago`, `fecha_pago`, `consumido`,`token`)
				VALUES(".$data['calendar_id'].",".$reservation_id.",'".$nombreCalendario."', 'tdc','Pendiente', '".date("Y-m-d H:i:s")."',0,'".$ramdomKey."')");

				/* ********************************************************** */
				/* GUARDO LA INFORMACION EN LA TABLA DE CUPONES */
				/* ********************************************************** */
				$ramdomCode = $this->generateRandomString();
				$fechaExpiracion = date('Y-m-d H:i:s');
				$fechaExpiracionFinal = date('Y-m-d H:i:s', strtotime($fechaExpiracion. ' + 10 days'));

				$conn->query("INSERT INTO booking_abcalendar_coupon(`id_res_origen`, `codigo`, `fecha_expiracion`)
				VALUES(".$reservation_id.", '".$ramdomCode."','".$fechaExpiracionFinal."')");
				/* ********************************************************** */
				/* ********************************************************** */

			
	/*ARMO EL ARRAY CON LA INFORMACION PARA payphone*/
	$dataOrden = array(	
		//'commerce_id' => '8226', //ID unico por comercio test
		//'commerce_id' => '8116', //ID unico por comercio produccion
			'documentId' => $data['c_cedula'] , //Identificaci�n del tarjeta habiente (RUC, C�dula, Pasaporte)
		//	'customer_name' => $data['c_name'], //Nombres del tarjeta habiente
		//	'customer_lastname' => $data['c_lastname'], //Apellidos del tarjeta habiente
			'phoneNumber' => $data['c_phone'],  //Tel�fonos del tarjeta habiente
		//	'customer_address' => $data['c_address'],  //Direcci�n del tarjeta habiente
			'email' => $data['c_email'],  //Correo electr�nico del tarjeta habiente
	//		'customer_language' => 'es',  //Idioma del tarjeta habiente
	//		'order_description' => $nombreCalendario,  //Descripci�n de la �rden
			'amount' => $data['amount'], //Monto total de la �rden
			'tax' => 12, //iMPUESTOS
			'amountWithoutTax' =>$data['amount'], //Monto sin impuestos
			
			'clientTransactionId' =>$reservation_id, 
			
		//	'id' => $reservation_id, //id de la reservacion
			//'response_url' => PJ_URL_LARAVEL.'/confirmacion',
		'response_url' => 'https://iwannatrip.com/confirmacionBeta',
		
		);

	//$url = "https://app.pagomedios.com/api/setorder/"; //URL del servicio web REST
$url = "https://pay.payphonetodoesposible.com/api/button/V2/Confirm/";
	$params = http_build_query( $dataOrden ); //Tranformamos un array en formato GET

	$data = json_encode($dataOrden);
//Iniciar Llamada
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://pay.payphonetodoesposible.com/api/button/V2/Confirm");
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt_array($curl, array(
CURLOPT_HTTPHEADER => array(
"Authorization: Bearer qHfKKza3RdNFijd_ncwEHPAX1kPG4jWG_FVJNIk6gLA3FfuuGCg-KLQRa9GSTZTEDk_uYV2t786McjZH-InCeYvRbih4mM7kKi06pBG6BWOIPZx9cakVh5-2Cn4c6Bny0Il8sB9_gmEorIdhxrxuIXN4rUMAxD2wPPiObGZqJdpb5BQL71igvY-2Fx0DQMcQ6plCzPbgjk8-Mf-OGgtA1eJKz1dCU42vvDw0kfVQzIZEGipAm0AirVFLTIK1hjCh9hU9DZ6344MyILOHYL7vTGz8EiwyrUSXR3jUC1KVOTJxNksc67Lw4ie5VDKFGbJqjHT0KA", "Content-Type:application/json"),
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);
curl_close($curl);

				    	
				$array = json_decode($response);

				// SE HACE UNA SEGUNDA LLAMADA AL SERVICIO PARA LOS CASOS QUE EN EL PRIMER INTENTO
				// NO SE PUEDA OBTENER LA URL DE RESPUESTA
				if(!$array->data->payment_url){

					$params = http_build_query( $dataOrden );
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url.'?'.$params);
					curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					$response = curl_exec($curl);
					curl_close($curl);
					$array = json_decode($response);

				}

				if($array->status == 1){
					$urltdc = $array->data->payment_url;
				}else if($array->status == 0){
					$urltdc = PJ_URL_LARAVEL1.'/errorpagotdc/'.$tokenReserva;
				}

			}

			if (isset($data['payment_method']) && $data['payment_method'] == 'creditcard'){

				if($array->data->payment_url){
					$urltdc = $array->data->payment_url;
				}else{
					$urltdc = 1;
				}

				// pjAppController::jsonResponse(array(
				// 	'status' => 'OK',
				// 	'code' => 200,
				// 	'reservation_id' => $reservation_id,
				// 	'invoice_id' => @$invoice_arr['data']['id'],
				// 	'payment_method' => @$data['payment_method'],
				// 	'url' => $array->data->payment_url,
				// 	'array' => $array,
				// 	'orden' => $dataOrden,
				// 	'data"creditcard' => $url.'?'.$params
				// ));

				pjAppController::jsonResponse(array(
					'status' => 'OK', 'code' => 200, 'text' => 'Reservation was saved.',
					'reservation_id' => $reservation_id,
					'invoice_id' => @$invoice_arr['data']['id'],
					'payment_method' => @$data['payment_method']
				));

			}elseif (isset($data['payment_method']) && $data['payment_method'] == 'cash'){
				pjAppController::jsonResponse(array(
					'status' => 'OK', 'code' => 200,
					'reservation_id' => $reservation_id,
					'invoice_id' => @$invoice_arr['data']['id'],
					'payment_method' => @$data['payment_method'],
					'url' => $urlCash
				));
			}else{
				pjAppController::jsonResponse(array(
					'status' => 'OK', 'code' => 200, 'text' => 'Reservation was saved.',
					'reservation_id' => $reservation_id,
					'invoice_id' => @$invoice_arr['data']['id'],
					'payment_method' => @$data['payment_method']
				));
			}


		} else {
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing or empty params.'));
		}
		//}
		exit;

	}

	/* ***************************************************************************** */
	/* INICIO NUEVOS METODOS PARA EL BOOKING LIGHT
	/* ***************************************************************************** */
	public function pjActionBookingSaveLight()
	{

		$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
		if($conn->connect_errno){
		     echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}
		$idCalendario = $_GET["cid"];
		$datetime = date('Y-m-d H:i:s');

		//INSERTO EN LA TABLA TOKEN PARA PODER VERIFICAR
		$sql1 = "INSERT INTO tokens (id_calendario,uuid,consumido,created_at,updated_at) VALUES($idCalendario,'$ramdomKey',false,'$datetime','$datetime') ";
		$conn->query($sql1);

		$this->setAjax(true);

		$data = array();

		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		$data['calendar_id'] = $_GET['cid'];
		$data['uuid'] = pjUtil::uuid();
		$data['status'] = ucfirst($this->option_arr['o_status_if_not_paid']);
		$data['locale_id'] = $this->pjActionGetLocale();
		$data['date_from'] = gmdate("Y-m-d", $_POST['start_dt']);
		$data['date_to'] = gmdate("Y-m-d", $_POST['end_dt']);
		$data['price_based_on'] = $this->option_arr['o_price_based_on'];
		$data['c_cedula'] = $_POST['c_cedula'];
		$data['c_name'] = $_POST['c_name'];
		$data['c_lastname'] = $_POST['c_lastname'];
		$data['c_phone'] = $_POST['c_phone'];
		$data['c_email'] = $_POST['c_email'];
		$data['c_adults'] = $_POST['c_adults'];
		$data['c_children'] = 0;
		$data['c_address'] = $_POST['c_address'];
		$data['amount'] = $_POST['amount'];
		$data['payment_method'] = $_POST['payment_method'];
		$data['sales_origin'] = 'Mobile';

		$resp = $this->pjActionCheckDt($data['date_from'], $data['date_to'], $data['calendar_id'], NULL, TRUE);
		if ($resp['status'] == 'ERR')
		{
			pjAppController::jsonResponse($resp);
		}

		if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
		{
			$price = pjPriceModel::factory()->getPrice(
				$data['calendar_id'],
				$data['date_from'],
				$data['date_to'],
				$this->option_arr,
				@$data['c_adults'],
				(int) $this->option_arr['o_bf_children'] !== 1 ? @$data['c_children'] : 0
			);
		} elseif (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period') {
			$price = pjPeriodModel::factory()->getPrice(
				$data['calendar_id'],
				$data['date_from'],
				$data['date_to'],
				$this->option_arr,
				@$data['c_adults'],
				(int) $this->option_arr['o_bf_children'] !== 1 ? @$data['c_children'] : 0
			);
		}

		// ***********************************************************
		// SE LE SUMA AL TOTAL SI EL CHECK ESTA MARCADO
		// ***********************************************************
		if($_GET['check'] === true || $_GET['check'] === 'true'){
			$data['amount'] = @$price['amount'] + 2;
			$data['deposit'] = @$price['deposit'] + 2;
			$data['tax'] = @$price['tax'];
			$data['security'] = @$price['security'];
		}else{
			$data['amount'] = @$price['amount'];
			$data['deposit'] = @$price['deposit'];
			$data['tax'] = @$price['tax'];
			$data['security'] = @$price['security'];
		}
		// ***********************************************************
		// ***********************************************************

		// ***********************************************************
		// SE LE RESTA AL TOTAL SI TIENE UN CUPON
		// ***********************************************************
		if($_POST['cupon'] > 0){
			$operacion = $data['amount'] - ( ( $data['amount'] * (float)$_POST['cupon']) / 100);
			$data['amount'] = $operacion;
			$data['deposit'] = $operacion;

			$cupon_id = $_POST['cupon_id'];
			//actualizar el cupon
			$sql = "UPDATE booking_abcalendar_coupon SET estado = 1 WHERE id = $cupon_id";
			$conn->query($sql);
		}
		// ***********************************************************
		// ***********************************************************

			if (isset($data['payment_method']) && $data['payment_method'] != 'creditcard')
			{
				unset($data['cc_type']);
				unset($data['cc_num']);
				unset($data['cc_exp_month']);
				unset($data['cc_exp_year']);
				unset($data['cc_code']);
			}

			$pjReservationModel = new pjReservationModel();
			if (!$pjReservationModel->validates($data))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Reservations data does not validate.'));
				//pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => $data));
			}

			$reservation_id = $pjReservationModel->setAttributes($data)->insert()->getInsertId();
			if ($reservation_id === false || (int) $reservation_id === 0)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Reservation was not saved.'));
			}

			$invoice_arr = $this->pjActionGenerateInvoice($reservation_id);

		// ***********************************************************
		// SE ACTUALIZA EL ID DE LA RESERVA QUE CONSUMIO EL CUPON
		// ***********************************************************
		if($_POST['cupon'] > 0){
			$cupon_id = $_POST['cupon_id'];
			//actualizar el cupon
			$sql = "UPDATE booking_abcalendar_coupon SET id_res_cons = $reservation_id WHERE id = $cupon_id";
			$conn->query($sql);
		}
		// ***********************************************************
		// ***********************************************************

			$_SESSION[$this->defaultCalendar] = NULL;
			unset($_SESSION[$this->defaultCalendar]);

			if (isset($_SESSION[$this->defaultCaptcha]))
			{
				$_SESSION[$this->defaultCaptcha] = NULL;
				unset($_SESSION[$this->defaultCaptcha]);
			}

			$calendar_arr = pjCalendarModel::factory()->find($_GET['cid'])->getData();

			// pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => $data, 'post' => $_POST, 'precio'=> $price['amount']));
			// exit;

			$params = $pjReservationModel->reset()
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
					AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.content AS country, t3.content AS confirm_subject, t4.content AS confirm_tokens", PJ_SALT))
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.field='confirm_subject' AND t3.locale=t1.locale_id", 'left outer')
				->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.field='confirm_tokens' AND t4.locale=t1.locale_id", 'left outer')
				->find($reservation_id)->getData();
			if (!empty($params))
			{
				$this->notify(3, NULL, $params);
				$this->notify(4, $calendar_arr['user_id'], $params);
				pjFront::pjActionConfirmSend($this->option_arr, $params, 'confirm');
			}

			/*****************************************************************/
			/*              PARA EL PAGO CON TARJETA DE CREDITO                  */
			/*     ARMO LA INFORMACION Y REDIRECCIONO A PAGO MEDIOS  */
			/*****************************************************************/
			if (isset($data['payment_method']) && $data['payment_method'] == 'creditcard'){

				$tokenReserva = md5($reservation_id);
				$sql = "UPDATE booking_abcalendar_reservations SET token_consulta = '$tokenReserva'
						WHERE id = $reservation_id";
				$conn->query($sql);
				/*RECUPERO EL NOMBRE DEL CALENDARIO*/
				$sqlCash = "SELECT content FROM booking_abcalendar_multi_lang WHERE model = 'pjCalendar' AND  field = 'name' AND foreign_id =". $data['calendar_id'];
				$cash = $conn->query($sqlCash);
				$nombreCalendario = "";
				foreach ($cash as $row2) {
					$nombreCalendario = $row2['content'];
				}

				/*GUARDO LA INFORMACION EN LA TABLA DE PAGOS*/
				$conn->query("INSERT INTO pagos(`calendario_id`, `reservacion_id`, `nombre_calendario`, `tipo`, `estado_pago`, `fecha_pago`, `consumido`,`token`)
				VALUES(".$data['calendar_id'].",".$reservation_id.",'".$nombreCalendario."', 'tdc','Pendiente', '".date("Y-m-d H:i:s")."',0,'".$ramdomKey."')");

				/* ********************************************************** */
				/* GUARDO LA INFORMACION EN LA TABLA DE CUPONES */
				/* ********************************************************** */
				$ramdomCode = $this->generateRandomString();
				$fechaExpiracion = date('Y-m-d H:i:s');
				$fechaExpiracionFinal = date('Y-m-d H:i:s', strtotime($fechaExpiracion. ' + 10 days'));

				$conn->query("INSERT INTO booking_abcalendar_coupon(`id_res_origen`, `codigo`, `fecha_expiracion`)
				VALUES(".$reservation_id.", '".$ramdomCode."','".$fechaExpiracionFinal."')");
				/* ********************************************************** */
				/* ********************************************************** */

				/*ARMO EL ARRAY CON LA INFORMACION PARA PAGO MEDIOS*/
				$dataOrden = array(
					//'commerce_id' => '8226', //ID unico por comercio test
					'commerce_id' => '8116', //ID unico por comercio produccion
					'customer_id' => $data['c_cedula'] , //Identificaci�n del tarjeta habiente (RUC, C�dula, Pasaporte)
					'customer_name' => $data['c_name'], //Nombres del tarjeta habiente
					'customer_lastname' => $data['c_lastname'], //Apellidos del tarjeta habiente
					'customer_phones' => $data['c_phone'],  //Tel�fonos del tarjeta habiente
					'customer_address' => $data['c_address'],  //Direcci�n del tarjeta habiente
					'customer_email' => $data['c_email'],  //Correo electr�nico del tarjeta habiente
					'customer_language' => 'es',  //Idioma del tarjeta habiente
					'order_description' => $nombreCalendario,  //Descripci�n de la �rden
					'order_amount' => $data['amount'], //Monto total de la �rden
					//'order_amount' => 200, //Monto total de la �rden
					'order_id' => $reservation_id, //id de la reservacion
					//'response_url' => PJ_URL_LARAVEL1.'/confirmacion',
					'response_url' => 'https://iwanatrip.com/confirmacionBeta',
					);

				$url = "https://app.redypago.com/api/setorder/"; //URL del servicio web REST

				$params = http_build_query( $dataOrden ); //Tranformamos un array en formato GET
				//Consumo del servicio Rest
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url.'?'.$params);
				//curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$response = curl_exec($curl);
				curl_close($curl);
				$array = json_decode($response);

				// SE HACE UNA SEGUNDA LLAMADA AL SERVICIO PARA LOS CASOS QUE EN EL PRIMER INTENTO
				// NO SE PUEDA OBTENER LA URL DE RESPUESTA
				if(!$array->data->payment_url){

					$params = http_build_query( $dataOrden );
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url.'?'.$params);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					$response = curl_exec($curl);
					curl_close($curl);
					$array = json_decode($response);

				}

				if($array->status == 1){
					$urltdc = $array->data->payment_url;
				}else if($array->status == 0){
					$urltdc = PJ_URL_LARAVEL1.'/errorpagotdc/'.$tokenReserva;
				}

			}

			if (isset($data['payment_method']) && $data['payment_method'] == 'creditcard'){

				if($array->data->payment_url){
					$urltdc = $array->data->payment_url;
				}else{
					$urltdc = 1;
				}

				pjAppController::jsonResponse(array(
					'status' => 'OK',
					'code' => 200,
					'reservation_id' => $reservation_id,
					'invoice_id' => @$invoice_arr['data']['id'],
					'payment_method' => @$data['payment_method'],
					'url' => $array->data->payment_url,
					'array' => $array,
					'orden' => $dataOrden
				));
			}elseif (isset($data['payment_method']) && $data['payment_method'] == 'cash'){
				pjAppController::jsonResponse(array(
					'status' => 'OK', 'code' => 200,
					'reservation_id' => $reservation_id,
					'invoice_id' => @$invoice_arr['data']['id'],
					'payment_method' => @$data['payment_method'],
					'url' => $urlCash
				));
			}else{
				pjAppController::jsonResponse(array(
					'status' => 'OK', 'code' => 200, 'text' => 'Reservation was saved.',
					'reservation_id' => $reservation_id,
					'invoice_id' => @$invoice_arr['data']['id'],
					'payment_method' => @$data['payment_method']
				));
			}


		// } else {
		// 	pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing or empty params.'));
		// }
		//}
		exit;

	}

	public function pjActionGetPriceBookingLight()
	{
		$this->setAjax(true);

		$data = array();
		$data['calendar_id'] = $_GET['cid'];
		$data['c_adults'] = $_GET['c_adults'];
		$data['date_from'] = date("Y-m-d", $_GET['start_dt']);
		$data['date_to'] = date("Y-m-d", $_GET['end_dt']);


		if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
		{
			$price = pjPriceModel::factory()->getPrice(
				$data['calendar_id'],
				$data['date_from'],
				$data['date_to'],
				$this->option_arr,
				@$data['c_adults'],
				0
			);
		} elseif (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period') {
			$price = pjPeriodModel::factory()->getPrice(
				$data['calendar_id'],
				$data['date_from'],
				$data['date_to'],
				$this->option_arr,
				@$data['c_adults'],
				0
			);
		}

		$data['amount'] = @$price['amount'];
		$data['deposit'] = @$price['deposit'];
		$data['tax'] = @$price['tax'];
		$data['security'] = @$price['security'];


		pjAppController::jsonResponse($data);

	}

	public function pjActionGetSummaryFormBookingLight(){

		$this->setAjax(true);

		$fechaInicio = date("Y-m-d", $_POST['start_dt']);
		$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
		if($conn->connect_errno){
				echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}

		$fecha = date("Y-m-d", $_POST['start_dt']);
		$idCalendario = $_GET['cid'];
		$sqlConfirmada = "SELECT SUM(c_adults + IFNULL(c_children, 0)) as confirmed FROM booking_abcalendar_reservations where date_from = '".$fecha."' and status = 'Confirmed' and calendar_id = ".$idCalendario;
		$confirmada = $conn->query($sqlConfirmada);
		foreach ($confirmada as $row1) {
			$ccC = $row1['confirmed'];
		}

		$sqlPendiente = "SELECT SUM(c_adults + IFNULL(c_children, 0)) as confirmed FROM booking_abcalendar_reservations where date_from = '".$fecha."' and status = 'Pending' and calendar_id = ".$idCalendario;
		$pendiente = $conn->query($sqlPendiente);
		foreach ($pendiente as $row1) {
			$ccP = $row1['confirmed'];
		}

		$sqlBookingPerDay = "SELECT * FROM booking_abcalendar_options where foreign_id = $idCalendario AND `key` = 'o_bookings_per_day'";
		$perday = $conn->query($sqlBookingPerDay);
		foreach ($perday as $row2) {
			$o_bookings_per_day = $row2['value'];
		}

		if($ccC == NULL && $ccP == NULL ){

			$disponibles  = $o_bookings_per_day;
			$totalReservacion = $_POST['c_adults'] + $_POST['c_children'];
			$cuenta = $disponibles - $totalReservacion;

		}else{

			$disponibles  = $o_bookings_per_day - ($ccC + $ccP);
			$totalReservacion = $_POST['c_adults'] + $_POST['c_children'];
			$cuenta = $disponibles - $totalReservacion;

		}

		if($cuenta > -1){
			pjAppController::jsonResponse(array(
				'status' => 'OK',
				'code' => 200,
				'disponibilidad' => 1
			));
		}else{
			pjAppController::jsonResponse(array(
				'status' => 'OK',
				'code' => 200,
				'disponibilidad' => 0
			));
		}

			// if($cuenta > -1){

			// 	echo '01';
			// 	$_SESSION[$this->defaultCalendar] = array_merge($_SESSION[$this->defaultCalendar], $_POST);

			// 	if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
			// 	{
			// 		$this->set('price_arr', pjPriceModel::factory()->getPrice(
			// 			$_GET['cid'],
			// 			date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']),
			// 			date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']),
			// 			$this->option_arr,
			// 			@$_SESSION[$this->defaultCalendar]['c_adults'],
			// 			(int) $this->option_arr['o_bf_children'] !== 1 ? @$_SESSION[$this->defaultCalendar]['c_children'] : 0
			// 		));
			// 	} elseif (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period') {
			// 		$this->set('price_arr', pjPeriodModel::factory()->getPrice(
			// 			$_GET['cid'],
			// 			date("Y-m-d", $_SESSION[$this->defaultCalendar]['start_dt']),
			// 			date("Y-m-d", $_SESSION[$this->defaultCalendar]['end_dt']),
			// 			$this->option_arr,
			// 			@$_SESSION[$this->defaultCalendar]['c_adults'],
			// 			(int) $this->option_arr['o_bf_children'] !== 1 ? @$_SESSION[$this->defaultCalendar]['c_children'] : 0
			// 		));
			// 	}

			// 	if ((int) $this->option_arr['o_bf_country'] !== 1 && isset($_SESSION[$this->defaultCalendar]['c_country'])){
			// 		$this->set('country_arr', pjCountryModel::factory()
			// 			->select('t1.*, t2.content AS name')
			// 			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
			// 			->where('t1.status', 'T')
			// 			->find($_SESSION[$this->defaultCalendar]['c_country'])->getData());
			// 	}
            // }
	}

	public function verifyCouponBookingLight(){

		$this->setAjax(true);

		$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
		if($conn->connect_errno){
				echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}

		$code = $_POST['code'].trim();
		$adults = (int) $_POST['c_adults'];

		$sql = "SELECT id, cantidad, fecha_expiracion, min_pass FROM booking_abcalendar_coupon where codigo = '".$code."' and estado = 0 ";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {

			foreach ($result as $row1) {
				$id = $row1['id'];
				$cantidad = $row1['cantidad'];
				$fechaExp = $row1['fecha_expiracion'];
				$minPass = $row1['min_pass'];
			}

			$fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
			$fecha_entrada = strtotime($fechaExp);

			if($adults >= $minPass){

				if($fecha_actual > $fecha_entrada){
					// EL CUPON HA CADUCADO
					pjAppController::jsonResponse(array(
						'status' => 'OK',
						'code' => 200,
						'cupon' => 2,
						'message' => 'The Coupon has expired',
					));
				}else{
					// EL CUPON SE PUEDE ACTUALIZAR
					pjAppController::jsonResponse(array(
						'status' => 'OK',
						'code' => 200,
						'cupon' => 1,
						'cantidad' => $cantidad,
						'id' => $id
					));
				}
			}else{
				// EL CUPON NO SE PUEDE ACTUALIZAR POR EL NUMERO DE PASAJEROS
				pjAppController::jsonResponse(array(
					'status' => 'OK',
					'code' => 200,
					'cupon' => 3,
					'cantidad' => $cantidad,
					'passengers' => $minPass,
					'message' => 'Valid coupon from '. $minPass .' passengers',
					'id' => $id
				));
			}
		}else{
			// EL CUPON HA SIDO UTILIZADO O NO EXISTE
			pjAppController::jsonResponse(array(
				'status' => 'OK',
				'code' => 200,
				'cupon' => 0,
				'message' => 'You can not use this coupon'
			));
		}

	}

	private function generateRandomString($length = 5)
	{
        return substr(str_shuffle(str_repeat($x='123456789ABCDEFGHJKLMNPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
	/* ***************************************************************************** */
	/* FIN NUEVOS METODOS PARA EL BOOKING LIGHT
	/* ***************************************************************************** */


	public function pjActionLocale()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				$this->pjActionSetLocale($_GET['locale_id']);
				$this->loadSetFields(true);
			}
		}
		exit;
	}

	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}

	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}

	public function isXHR()
	{
		// CORS
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}

	static protected function allowCORS()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
}
?>
