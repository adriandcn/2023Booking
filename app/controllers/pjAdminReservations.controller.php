<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_CONTROLLERS_PATH . 'pjAdmin.controller.php';
/* PHP MAILER LIBRARIES*/
require_once PJ_CONTROLLERS_PATH . 'PHPMailer/class.phpmailer.php';
require_once PJ_CONTROLLERS_PATH . 'PHPMailer/PHPMailerAutoload.php';
include_once(PJ_CONTROLLERS_PATH .'PHPMailer/class.phpmailer.php');

class pjAdminReservations extends pjAdmin
{
	public function pjActionCheckUnique()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['uuid']) && !empty($_GET['uuid']))
			{
				$pjReservationModel = pjReservationModel::factory();
				if (isset($_GET['id']) && (int) $_GET['id'] > 0)
				{
					$pjReservationModel->where('t1.id !=', $_GET['id']);
				}
				$cnt = $pjReservationModel->where('t1.uuid', $_GET['uuid'])->findCount()->getData();
				echo (int) $cnt === 0 ? 'true' : 'false';
			} else {
				echo 'false';
			}
		}
		exit;
	}

	public function pjActionCreate()	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner() || $this->isEditor())
		{
			if ($this->isOwner())
			{
				$calendars = $this->get('calendars');
				if (empty($calendars))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR19");
				}
			}

			if (isset($_POST['reservation_create']))
			{
				$pjReservationModel = pjReservationModel::factory();
				if (0 != $pjReservationModel->where('t1.uuid', $_POST['uuid'])->findCount()->getData())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
				}

				$data = array();
				$data['date_from'] = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
				$data['date_to'] = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
				$data['price_based_on'] = $this->option_arr['o_price_based_on'];
				$data['ip'] = $_SERVER['REMOTE_ADDR'];
				$data['locale_id'] = $this->getLocaleId();

				$insert_id = $pjReservationModel->reset()->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					/*******************************************************************/
					/*   			INICIO ENVIO DE CORREO 							   */
					/*******************************************************************/
					$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
					if($conn->connect_errno){
					     echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
					}
					
					$tokenReserva = md5($insert_id);
					$sql = "UPDATE booking_abcalendar_reservations SET token_consulta = '$tokenReserva'
							WHERE id = $insert_id";
					$conn->query($sql);
					/*RECUPERO EL NOMBRE DEL CALENDARIO*/
					$sqlCash = "SELECT content FROM booking_abcalendar_multi_lang WHERE model = 'pjCalendar' AND  field = 'name' AND foreign_id =". $_POST['calendar_id'];
					$cash = $conn->query($sqlCash);
					$nombreCalendario = "";
					foreach ($cash as $row2) {
						$nombreCalendario = $row2['content'];
					}
					
					/*GUARDO LA INFORMACION EN LA TABLA DE PAGOS*/
					$conn->query("INSERT INTO pagos(`calendario_id`, `reservacion_id`, `nombre_calendario`, `tipo`, `estado_pago`, `fecha_pago`, `consumido`,`token`)
					VALUES(".$_POST['calendar_id'].",".$insert_id.",'".$nombreCalendario."', 'tdc','Confirmado', '".date("Y-m-d H:i:s")."',1,'".$tokenReserva."')");					

					$conn->query("INSERT INTO delivery_book(id_reserva, enviado) VALUES($insert_id,0)");	
					
					// GUARDO LA INFORMACION EN LA TABLA DE CUPONES 
					$ramdomCode = $this->generateRandomString();
					$fechaExpiracion = date('Y-m-d H:i:s');
					$fechaExpiracionFinal = date('Y-m-d H:i:s', strtotime($fechaExpiracion. ' + 10 days'));        

					$conn->query("INSERT INTO booking_abcalendar_coupon(`id_res_origen`, `codigo`, `fecha_expiracion`)
					VALUES(".$insert_id.", '".$ramdomCode."','".$fechaExpiracionFinal."')");				
					
					/*******************************************************************/
					/*   			FIN ENVIO DE CORREO 				               */
					/*******************************************************************/
					$invoice_arr = $this->pjActionGenerateInvoice($insert_id);

					$params = $pjReservationModel
						->reset()
						->select(sprintf("t1.*,
							AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
							AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
							AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
							AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
							t2.content AS country", PJ_SALT))
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
						->find($insert_id)->getData();
					if (!empty($params))
					{
						$this->notify(3, NULL, $params);
						$calendar_arr = pjCalendarModel::factory()->find($_POST['calendar_id'])->getData();
						if (!empty($calendar_arr))
						{
							$this->notify(4, $calendar_arr['user_id'], $params);
						}
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR03");
				} else {
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR04");
				}
			}

			pjObject::import('Model', 'pjCountry:pjCountry');
			$this->set('country_arr', pjCountryModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.status', 'T')
				->orderBy('`name` ASC')->findAll()->getData()
			);

			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminReservations.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	private function generateRandomString($length = 5) 
	{
        return substr(str_shuffle(str_repeat($x='123456789ABCDEFGHJKLMNPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }		

	public function pjActionCreateInvoice()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged())
		{
			$response = $this->pjActionGenerateInvoice($_POST['id']);
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionDeleteReservation()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$response = array();
			if (pjReservationModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjReservation')->where('foreign_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionDeleteReservationBulk()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjReservationModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjReservation')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}

	public function pjActionExportReservation()
	{
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjReservationModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Reservations-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}

	public function pjActionGetMessage()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$locale_id = $this->getLocaleId();
			if(isset($_GET['locale_id']) && (int) $_GET['locale_id'] > 0)
			{
				$locale_id = (int) $_GET['locale_id'];
			}
			$calendar_arr = pjCalendarModel::factory()
				->select("t2.content AS confirm_tokens, t3.content AS confirm_subject")
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.locale='".$locale_id."' AND t2.field='confirm_tokens'", 'inner')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.id AND t3.locale='".$locale_id."' AND t3.field='confirm_subject'", 'inner')
				->find($_POST['calendar_id'])->getData();

			if (isset($_POST['locale_id']) && (int) $_POST['locale_id'] > 0 && isset($_POST['c_country']) && (int) $_POST['c_country'] > 0)
			{
				pjObject::import('Model', 'pjCountry:pjCountry');
				$country_arr = pjCountryModel::factory()
					->select('t1.*, t2.content AS country')
					->join('pjMultiLang', sprintf("t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='%u'", $_POST['locale_id']), 'left outer')
					->find($_POST['c_country'])
					->getData();
				if (!empty($country_arr))
				{
					$_POST['country'] = $country_arr['country'];
				}
			}

			$tokens = pjAppController::getTokens($_POST, $this->option_arr);

			$response = array(
				'subject' => str_replace($tokens['search'], $tokens['replace'], @$calendar_arr['confirm_subject']),
				'body' => str_replace($tokens['search'], $tokens['replace'], @$calendar_arr['confirm_tokens'])
			);
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionCalcPrice()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$response = array();

			$date_from = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
			$date_to = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
			if ($date_from === FALSE || $date_to === FALSE)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Given date(s) are empty.'));
			}
			if (!isset($_POST['calendar_id']) || (int) $_POST['calendar_id'] <= 0)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Calendar is empty or invalid.'));
			}

			if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
			{
				pjObject::import('Model', 'pjPrice:pjPrice');
				$response = pjPriceModel::factory()->getPrice(
					$_POST['calendar_id'],
					$date_from,
					$date_to,
					$this->option_arr,
					isset($_POST['c_adults']) ? $_POST['c_adults'] : NULL,
					isset($_POST['c_children']) ? $_POST['c_children'] : NULL
				);
				$response['status'] = 'OK';
			} elseif (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period') {
				pjObject::import('Model', 'pjPeriod:pjPeriod');
				$response = pjPeriodModel::factory()->getPrice(
					$_POST['calendar_id'],
					$date_from,
					$date_to,
					$this->option_arr,
					isset($_POST['c_adults']) ? $_POST['c_adults'] : NULL,
					isset($_POST['c_children']) ? $_POST['c_children'] : NULL
				);
				$response['status'] = 'OK';
			}
			$response['total'] = @$response['amount'] + @$response['tax'];
			$response['deposit'] = @$response['deposit'];
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionGetReservation(){
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjReservationModel = pjReservationModel::factory()->join('pjCalendar', 't2.id=t1.calendar_id', 'inner');

			if (isset($_GET['uuid']) && !empty($_GET['uuid']))
			{
				$q = $pjReservationModel->escapeString($_GET['uuid']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjReservationModel->where("t1.uuid LIKE '%$q%'");
			}

			if (isset($_GET['calendar_id']) && (int) $_GET['calendar_id'] > 0)
			{
				$pjReservationModel->where('t1.calendar_id', $_GET['calendar_id']);
			}

			if (isset($_GET['date']) && !empty($_GET['date']))
			{
				$pjReservationModel->where(sprintf("('%s' BETWEEN t1.date_from AND t1.date_to)", $pjReservationModel->escapeString($_GET['date'])));
			}

			if (isset($_GET['status']) && !empty($_GET['status']))
			{
				$pjReservationModel->where('t1.status', $_GET['status']);
			}

			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjReservationModel->escapeString($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjReservationModel->where("(t1.uuid LIKE '%$q%' OR t1.c_name LIKE '%$q%' OR t1.c_email LIKE '%$q%' OR t1.c_phone LIKE '%$q%')");
			}

			if (isset($_GET['time']) && !empty($_GET['time']))
			{
				$pjReservationModel->where(sprintf("'%s' BETWEEN `date_from` AND `date_to`", date("Y-m-d", $_GET['time'])));
			}

			if (isset($_GET['c_name']) && !empty($_GET['c_name']))
			{
				$q = $pjReservationModel->escapeString($_GET['c_name']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjReservationModel->where("t1.c_name LIKE '%$q%'");
			}

			if (isset($_GET['c_email']) && !empty($_GET['c_email']))
			{
				$q = $pjReservationModel->escapeString($_GET['c_email']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjReservationModel->where("t1.c_email LIKE '%$q%'");
			}

			if (isset($_GET['amount_from']) && (float) $_GET['amount_from'] > 0)
			{
				$pjReservationModel->where('t1.amount >=', $_GET['amount_from']);
			}

			if (isset($_GET['amount_to']) && (float) $_GET['amount_to'] > 0)
			{
				$pjReservationModel->where('t1.amount <=', $_GET['amount_to']);
			}

			if (isset($_GET['last_7days']) && (int) $_GET['last_7days'] === 1)
			{
				$pjReservationModel->where('(DATE(t1.created) BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE())');
			}

			if (isset($_GET['current_week']) && (int) $_GET['current_week'] === 1)
			{
				$monday = strtotime('last monday', strtotime('tomorrow'));
				$sunday = strtotime('next sunday', strtotime('yesterday'));

				$pjReservationModel
					->where('t1.date_from <=', date("Y-m-d", $sunday))
					->where('t1.date_to >=', date("Y-m-d", $monday));
			}

			if (isset($_GET['date_from']) && !empty($_GET['date_from']) && isset($_GET['date_to']) && !empty($_GET['date_to']))
			{
				$pjReservationModel->where(sprintf("((`date_from` BETWEEN '%1\$s' AND '%2\$s') OR (`date_to` BETWEEN '%1\$s' AND '%2\$s'))",
					pjUtil::formatDate($_GET['date_from'], $this->option_arr['o_date_format']),
					pjUtil::formatDate($_GET['date_to'], $this->option_arr['o_date_format'])
				));
			} else {
				if (isset($_GET['date_from']) && !empty($_GET['date_from']))
				{
					$pjReservationModel->where('t1.date_from >=', pjUtil::formatDate($_GET['date_from'], $this->option_arr['o_date_format']));
				}
				if (isset($_GET['date_to']) && !empty($_GET['date_to']))
				{
					$pjReservationModel->where('t1.date_to <=', pjUtil::formatDate($_GET['date_to'], $this->option_arr['o_date_format']));
				}
			}

			if ($this->isOwner())
			{
				//$pjReservationModel->where('t2.user_id', $this->getUserId());
				$pjReservationModel->where('t2.id_usuario_servicio', $_SESSION['usuario_servicio']);
			}

			$column = 'date_from';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjReservationModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjReservationModel->select('t1.id, t1.calendar_id, t1.uuid, t1.date_from, t1.date_to, t1.status, t1.amount, t1.deposit, t1.c_name, t1.c_email, t3.content AS calendar')
				->join('pjMultiLang', "t3.model='pjCalendar' AND t3.foreign_id=t1.calendar_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach($data as $k => $v)
			{
				$name_arr = array();
				if(!empty($v['c_name']))
				{
					$name_arr[] = $v['c_name'];
				}
				if(!empty($v['c_email']))
				{
					$name_arr[] = $v['c_email'];
				}
				$v['c_name'] = implode("<br/>", $name_arr);
				$data[$k] = $v;
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner() || $this->isEditor())
		{
			$pjCalendarModel = pjCalendarModel::factory();

			if ($this->isOwner())
			{
				//$ver = $pjCalendarModel->where('t1.user_id', $this->getUserId());
				$pjCalendarModel->where('t1.id_usuario_servicio', $_SESSION['usuario_servicio']);
			}

			$this->set('calendar_arr', $pjCalendarModel
				->select('t1.id, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy('`name` ASC')
				->findAll()
				->getData()

			);


			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminReservations.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionSaveReservation()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjReservationModel = pjReservationModel::factory();
			if (!in_array($_POST['column'], $pjReservationModel->getI18n()))
			{
				$reservation = $pjReservationModel
					->select(sprintf("t1.*,
						AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
						t2.content AS country, t3.user_id", PJ_SALT))
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
					->join('pjCalendar', 't3.id=t1.calendar_id', 'left outer')
					->find($_GET['id'])->getData();
				if (in_array($_POST['column'], array('date_from', 'date_to')))
				{
					$_POST['value'] = pjUtil::formatDate($_POST['value'], $this->option_arr['o_date_format']);

					if ($_POST['column'] == 'date_from')
					{
						$date_from = $_POST['value'];
						$date_to = $reservation['date_to'];
					} elseif ($_POST['column'] == 'date_to') {
						$date_from = $reservation['date_from'];
						$date_to = $_POST['value'];
					}

					if (strtotime($date_from) > strtotime($date_to))
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid date range'));
					}

					$response = $this->pjActionCheckDt($date_from, $date_to, $reservation['calendar_id'], $reservation['id'], true);
					if ($response['status'] != 'OK')
					{
						pjAppController::jsonResponse($response);
					}
				}
				$pjReservationModel->set('id', $_GET['id'])->modify(array($_POST['column'] => $_POST['value']));

				if ($_POST['column'] == 'status' && $_POST['value'] == 'Cancelled' && $reservation['status'] != 'Cancelled')
				{
					$this->notify(5, NULL, $reservation);
					$this->notify(6, $reservation['user_id'], $reservation);
				}
			} else {
				MultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjReservation');
			}
		}
		exit;
	}

	public function pjActionSendMessage()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{

			if (!isset($_POST['c_email']) || empty($_POST['c_email']) || !pjValidation::pjActionEmail($_POST['c_email']))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Invalid or empty email.'));
			}

			$admin = pjUserModel::factory()->find(1)->getData();

			$pjEmail = new pjEmail();
			pjAppController::setFields($this->getLocaleId());
			$pjEmail
				->setTo($_POST['c_email'])
				->setFrom(@$admin['email'])
				->setSubject($_POST['subject'])
			;

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

			if ($pjEmail->send($_POST['message']))
			{
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to sent.'));
		}
		exit;
	}

	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner() || $this->isEditor())
		{
			$pjReservationModel = pjReservationModel::factory();

			if (isset($_REQUEST['id']) && (int) $_REQUEST['id'] > 0)
			{
				$pjReservationModel->where('t1.id', $_REQUEST['id']);
			} elseif (isset($_GET['uuid']) && !empty($_GET['uuid'])) {
				$pjReservationModel->where('t1.uuid', $_GET['uuid']);
			}

			$reservation = $pjReservationModel
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
					AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
					t2.content AS country, t3.user_id, t4.content AS calendar_name", PJ_SALT))
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale=t1.locale_id", 'left outer')
				->join('pjCalendar', 't3.id=t1.calendar_id', 'left outer')
				->join('pjMultiLang', "t4.model='pjCalendar' AND t4.foreign_id=t1.calendar_id AND t4.field='name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->limit(1)
				->findAll()->getData();

			if (empty($reservation) || count($reservation) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR08");
			}
			$reservation = $reservation[0];

			$calendar = pjCalendarModel::factory()->find($reservation['calendar_id'])->getData();

			if (empty($calendar) || count($calendar) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR09");
			}

			if ($this->isOwner())
			{
				if ($calendar['user_id'] != $this->getUserId())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR10");
				}
			}

			if (isset($_POST['reservation_update']))
			{
				if (0 != $pjReservationModel->reset()->where('t1.uuid', $_POST['uuid'])->where('t1.id !=', $_POST['id'])->findCount()->getData())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR02");
				}

				$data = array();
				$data['date_from'] = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
				$data['date_to'] = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
				$data['modified'] = date('Y-m-d H:i:s');

				$option_arr = $this->option_arr;
				if ($_POST['calendar_id'] != $this->getForeignId())
				{
					$option_arr = pjOptionModel::factory()->getPairs($_POST['calendar_id']);
				}
				$check = $this->pjActionCheckDt($data['date_from'], $data['date_to'], $_POST['calendar_id'], $_POST['id'], true);

				if ($check['status'] == 'ERR')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionUpdate&id=".$_POST['id']."&err=AR11");
				}
				$pjReservationModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));

				if ($reservation['status'] != 'Cancelled' && $_POST['status'] == 'Cancelled')
				{
					$this->notify(5, NULL, $reservation);
					$this->notify(6, $reservation['user_id'], $reservation);
				}

				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminReservations&action=pjActionIndex&err=AR01");
			} else {
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();

				$this->set('locale_arr', $locale_arr);
				$this->set('arr', $reservation);
			}

			$pjCalendarModel = pjCalendarModel::factory();
			if ($this->isOwner())
			{
				$pjCalendarModel->where('t1.user_id', $this->getUserId());
			}
			$this->set('listing_arr', $pjCalendarModel
				->select('t1.id, t2.content AS title')
				->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->orderBy('title ASC')
				->findAll()->getData()
			);

			pjObject::import('Model', 'pjCountry:pjCountry');
			$this->set('country_arr', pjCountryModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where(sprintf("t1.status != IF(t1.id != '%u', 'F', 'WHATEVER')", $reservation['c_country']))
				->orderBy('`name` ASC')->findAll()->getData()
			);

			pjObject::import('Model', 'pjInvoice:pjInvoice');
			$this->set('invoice_arr', pjInvoiceModel::factory()
				->where('t1.order_id', $reservation['uuid'])
				->findAll()
				->getData()
			);

			$OptionModel = pjOptionModel::factory();
			$this->__option_arr = $OptionModel->getPairs($reservation['calendar_id']);
			$this->set('__option_arr', $this->__option_arr);

			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminReservations.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);

		} else {
			$this->set('status', 2);
		}
	}

	private function pjActionGetAvailability($year, $month)
	{
		$pjCalendarModel = pjCalendarModel::factory()
			->select("t1.*, t2.content AS title, t3.value AS o_bookings_per_day")
			->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjOption', "t3.foreign_id=t1.id AND t3.key='o_bookings_per_day'", 'left outer');
		$pjReservationModel = pjReservationModel::factory();
		if ($this->isOwner())
		{
			//$pjCalendarModel->where('t1.user_id', $this->getUserId());
			$pjCalendarModel->where('t1.id_usuario_servicio', $_SESSION['usuario_servicio']);
		}
		$arr = $pjCalendarModel->orderBy('t1.id ASC')->groupBy('t1.id')->findAll()->getData();

		foreach ($arr as $k => $calendar)
		{
			$arr[$k]['date_arr'] = $pjReservationModel->getInfo(
				$calendar['id'],
				date("Y-m-d", mktime(0, 0, 0, $month, 1, $year)),
				date("Y-m-d", mktime(0, 0, 0, $month + 1, 0, $year)),
				$this->models['Option']->reset()->getPairs($calendar['id']),
				NULL,
				1
			);
		}
		return $arr;
	}

	public function pjActionGetDashboard()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$this->set('arr', $this->pjActionGetAvailability($_GET['year'], $_GET['month']));
		}
	}

	public function pjActionDashboard()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner() || $this->isEditor())
		{
			//*****************************************************************//
			// QUERY QUE LISTA LOS CALENDARIOS POR USUARIO SERVICIOS //
			// 	IR A public function pjActionGetAvailability  		   //
			//*****************************************************************//
			list($year, $month) = explode("-", date("Y-n"));
			$arr = $this->pjActionGetAvailability($year, $month);
			$this->set('arr', $arr);

			pjObject::import('Model', array('pjLocale:pjLocale', 'pjLocale:pjLocaleLanguage'));
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->orderBy('t1.sort ASC')->findAll()->getData();
			$this->set('locale_arr', $locale_arr);

			foreach ($arr as $calendar)
			{
				$this->appendCss('index.php?controller=pjAdminReservations&action=pjActionLoadCss&cid=' . $calendar['id'], PJ_INSTALL_URL, true);
			}
			$this->appendJs('pjAdminReservations.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionLoadCss()
	{
		$option_arr = pjOptionModel::factory()->getPairs($_GET['cid']);

		ob_start();
		@readfile(PJ_CSS_PATH . 'availability.txt');
		$string = ob_get_contents();
		ob_end_clean();

		header("Content-Type: text/css; charset=utf-8");
		if ($string !== FALSE)
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
					'.cal-id-' . $_GET['cid'],
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
		exit;
	}

	public function pjActionCheckDates()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged())
		{
			$resp = $this->pjActionCheckDt($_GET['date_from'], $_GET['date_to'], @$_GET['calendar_id'], @$_GET['id'], true);
			pjAppController::jsonResponse($resp);
		}
		exit;
	}

	public function pjActionExport()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner() || $this->isEditor())
		{
			if(isset($_POST['reservation_export']))
			{
				$pjReservationModel = pjReservationModel::factory();
				$pjCalendarModel = pjCalendarModel::factory();

				$arr = array();

				if(isset($_POST['calendar_id']) && !empty($_POST['calendar_id']))
				{
					$pjCalendarModel->where('t1.id', $_POST['calendar_id']);
				}
				$calendar_arr = $pjCalendarModel
					->where('t1.id_usuario_servicio', $_SESSION['usuario_servicio'])
					->findAll()
					->getData();
				foreach($calendar_arr as $k => $v)
				{
					$option_arr = $this->models['Option']->reset()->getAllPairs($v['id']);
					list(, $week_start) = explode("::", $option_arr['o_week_start']);

					$pjReservationModel->reset();
					$pjReservationModel->where('t1.calendar_id', $v['id']);
					if($_POST['period'] == 'next')
					{
						$column = 'date_from';
						$direction = 'ASC';

						$where_str = pjUtil::getComingWhere($_POST['coming_period'], $week_start);
						if($where_str != '')
						{
							$pjReservationModel->where($where_str);
						}
					}else if($_POST['period'] == 'all'){
						$column = 'created';
						$direction = 'ASC';
					}else if($_POST['period'] == 'range'){
						$column = 'created';
						$direction = 'ASC';
						$date_from = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
						$date_to = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
						$where_str = "((t1.date_from BETWEEN '$date_from' AND '$date_to') OR
							   (t1.date_to BETWEEN '$date_from' AND '$date_to') OR
							   (t1.date_from <= '$date_from' AND t1.date_to >= '$date_to'))";
						$pjReservationModel->where($where_str);
					}else{
						$column = 'created';
						$direction = 'ASC';
						$where_str = pjUtil::getMadeWhere($_POST['made_period'], $week_start);
						if($where_str != '')
						{
							$pjReservationModel->where($where_str);
						}
					}

					$_arr= $pjReservationModel
						->select('t1.id, t1.calendar_id, t1.uuid, t1.date_from, t1.date_to, t1.status, t1.amount,
								  t1.deposit, t1.c_name, t1.c_email, t1.c_phone, t1.c_phone, t1.c_adults, t1. c_children,
								  t1.c_notes, t1.c_address, t1.c_city, t1.c_country, t1.c_state, t1.c_zip, t1.ip, t1.payment_method, t1.created, t1.modified,
								  t2.content AS calendar')
						->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.calendar_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy("$column $direction")
						->findAll()
						->getData();

					foreach($_arr as $v)
					{
						if($this->option_arr['o_price_based_on'] == 'nights')
						{
							$v['date_from'] = $v['date_from'] . ' ' . "12:00:00";
							$v['date_to'] = $v['date_to'] . ' ' . "12:00:00";
						}else{
							$v['date_from'] = $v['date_from'] . ' ' . "00:00:00";
							$v['date_to'] = $v['date_to'] . ' ' . "23:59:59";
						}
						$arr[] = $v;
					}
				}

				if($_POST['type'] == 'file')
				{
					$this->setLayout('pjActionEmpty');

					if($_POST['format'] == 'csv')
					{
						$csv = new pjCSV();
						$csv
							->setHeader(true)
							->setName("Export-".time().".csv")
							->process($arr)
							->download();
					}
					if($_POST['format'] == 'xml')
					{
						$xml = new pjXML();
						$xml
							->setEncoding('UTF-8')
							->setName("Export-".time().".xml")
							->process($arr)
							->download();
					}
					if($_POST['format'] == 'ical')
					{
						$ical = new pjICal();
						$ical
							->setName("Export-".time().".ics")
							->setProdID('Availability Booking Calendar')
							->setSummary('c_name')
							->setLocation('calendar')
							->setTimezone(pjUtil::getTimezoneName($this->option_arr['o_timezone']))
							->process($arr)
							->download();
					}
					exit;
				}else{
					$pjPasswordModel = pjPasswordModel::factory();
					$password = md5($_POST['password'].PJ_SALT);
					$arr = $pjPasswordModel
						->where("t1.password", $password)
						->limit(1)
						->findAll()
						->getData();
					if (count($arr) != 1)
					{
						$pjPasswordModel->setAttributes(array('password' => $password))->insert();
					}
					$this->set('password', $password);
				}
			}

			$calendar_arr = pjCalendarModel::factory()
				->select('t1.id, t1.user_id, t2.content AS name')
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCalendar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->orderBy("`name` ASC")
				->where('t1.id_usuario_servicio', $_SESSION['usuario_servicio'])
				->findAll()
				->getData();
			$this->set('calendar_arr', $calendar_arr);

			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminReservations.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionExportFeed()
	{
		$this->setLayout('pjActionEmpty');
		$access = true;
		if(isset($_GET['p']))
		{
			$pjPasswordModel = pjPasswordModel::factory();
			$arr = $pjPasswordModel
				->where('t1.password', $_GET['p'])
				->limit(1)
				->findAll()
				->getData();
			if (count($arr) != 1)
			{
				$access = false;
			}
		}else{
			$access = false;
		}
		if($access == true)
		{
			$arr = $this->pjGetFeedData($_GET);
			if(!empty($arr))
			{
				if($_GET['format'] == 'xml')
				{
					$xml = new pjXML();
					echo $xml
						->setEncoding('UTF-8')
						->process($arr)
						->getData();

				}
				if($_GET['format'] == 'csv')
				{
					$csv = new pjCSV();
					echo $csv
						->setHeader(true)
						->process($arr)
						->getData();

				}
				if($_GET['format'] == 'ical')
				{
					$ical = new pjICal();
					echo $ical
						->setProdID('Availability Booking Calendar')
						->setSummary('c_name')
						->setLocation('calendar')
						->setTimezone(pjUtil::getTimezoneName($this->option_arr['o_timezone']))
						->process($arr)
						->getData();

				}
			}
		}else{
			__('lblNoAccessToFeed');
		}
		exit;
	}
	public function pjGetFeedData($get)
	{
		$arr = array();
		$status = true;
		$type = '';
		$period = '';
		if(isset($get['period']))
		{
			if(!ctype_digit($get['period']))
			{
				$status = false;
			}else{
				$period = $get['period'];
			}
		}else{
			$status = false;
		}
		if(isset($get['type']))
		{
			if(!ctype_digit($get['type']))
			{
				$status = false;
			}else{
				$type = $get['type'];
			}
		}else{
			$status = false;
		}
		if($status == true && $type != '' && $period != '')
		{
			$pjReservationModel = pjReservationModel::factory();
			$pjCalendarModel = pjCalendarModel::factory();

			if(isset($get['calendar_id']) && !empty($get['calendar_id']))
			{
				$pjCalendarModel->where('t1.id', $get['calendar_id']);
			}
			$calendar_arr = $pjCalendarModel
				->findAll()
				->getData();

			foreach($calendar_arr as $k => $v)
			{
				$option_arr = $this->models['Option']->reset()->getAllPairs($v['id']);
				list(, $week_start) = explode("::", $option_arr['o_week_start']);

				$pjReservationModel->reset();
				$pjReservationModel->where('t1.calendar_id', $v['id']);
				if($type == '1')
				{
					$column = 'date_from';
					$direction = 'ASC';

					$where_str = pjUtil::getComingWhere($period, $week_start);
					if($where_str != '')
					{
						$pjReservationModel->where($where_str);
					}
				}else{
					$column = 'created';
					$direction = 'DESC';
					$where_str = pjUtil::getMadeWhere($period, $week_start);
					if($where_str != '')
					{
						$pjReservationModel->where($where_str);
					}
				}
				$_arr = $pjReservationModel
					->select('t1.id, t1.calendar_id, t1.uuid, t1.date_from, t1.date_to, t1.status, t1.amount,
							  t1.deposit, t1.c_name, t1.c_email, t1.c_phone, t1.c_phone, t1.c_adults, t1. c_children,
							  t1.c_notes, t1.c_address, t1.c_city, t1.c_country, t1.c_state, t1.c_zip, t1.ip, t1.payment_method, t1.created, t1.modified,
							  t2.content AS calendar')
					->join('pjMultiLang', "t2.model='pjCalendar' AND t2.foreign_id=t1.calendar_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("$column $direction")
					->findAll()
					->getData();
				foreach($_arr as $v)
				{
					if($this->option_arr['o_price_based_on'] == 'nights')
					{
						$v['date_from'] = $v['date_from'] . ' ' . "12:00:00";
						$v['date_to'] = $v['date_to'] . ' ' . "12:00:00";
					}else{
						$v['date_from'] = $v['date_from'] . ' ' . "00:00:00";
						$v['date_to'] = $v['date_to'] . ' ' . "23:59:59";
					}
					$arr[] = $v;
				}
			}
		}
		return $arr;
	}

	public function pjActionGetAdults(){
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged())
		{
			$OptionModel = pjOptionModel::factory();
			$this->option_arr = $OptionModel->getPairs($_GET['id']);
			$this->set('option_arr', $this->option_arr);
		}
	}

	public function pjActionGetChildren(){
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged())
		{
			$OptionModel = pjOptionModel::factory();
			$this->option_arr = $OptionModel->getPairs($_GET['id']);
			$this->set('option_arr', $this->option_arr);
		}
	}
}
?>
