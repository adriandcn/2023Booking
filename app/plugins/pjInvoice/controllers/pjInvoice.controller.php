<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjInvoice extends pjInvoiceAppController
{
	public $invoiceErrors = 'InvoiceErrors';
	
	private function sortTimezones(Array $array)
	{
		$ordered = array();
		$orderArray = array('-43200', '-39600', '-36000', '-32400', '-28800', '-25200', '-21600', '-18000', '-14400', '-10800', '-7200', '-3600', '0', '3600', '7200', '10800', '14400', '18000', '21600', '25200', '28800', '32400', '36000', '39600', '43200', '46800');
		foreach($orderArray as $key) {
			if(array_key_exists($key,$array)) {
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		return $ordered + $array;
	}
	
	public function pjActionCheckUniqueId()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && isset($_GET['uuid']))
		{
			$pjInvoiceModel = pjInvoiceModel::factory();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjInvoiceModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjInvoiceModel->where('t1.uuid', $_GET['uuid'])->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionAddItem()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			if (isset($_POST['invoice_add']))
			{
				$insert_id = pjInvoiceItemModel::factory($_POST)->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					$pjInvoiceModel = pjInvoiceModel::factory();
					$invoice = $pjInvoiceModel->find($_POST['invoice_id'])->getData();
					if (!empty($invoice))
					{
						$total = (float) $invoice['total'] + (float) $_POST['amount'];
						$pjInvoiceModel->modify(array('total' => $total));
						pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'total' => $total));
					}
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			
			if (isset($_GET['invoice_id']) && (int) $_GET['invoice_id'] > 0)
			{
				$this->set('arr', pjInvoiceModel::factory()->find($_GET['invoice_id'])->getData());
			}
			$this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));
		}
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

		$invoice_arr = $pjInvoiceModel->where('t1.uuid', $_POST['x_invoice_num'])->limit(1)->findAll()->getDataIndex(0);
		if ($invoice_arr === FALSE || empty($invoice_arr))
		{
			$this->log('Invoice not found');
			exit;
		}
		$config_arr = pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId());
		
		$params = array(
			'transkey' => @$config_arr['p_authorize_key'],
			'x_login' => @$config_arr['p_authorize_mid'],
			'md5_setting' => @$config_arr['p_authorize_hash'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		
		$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Invoice confirmed as paid');
			$pjInvoiceModel->reset()->set('id', $invoice_arr['id'])->modify(array('status' => 'paid', 'modified' => ':NOW()'));
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Invoice not confirmed as paid. ' . $response['response_reason_text']);
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
		
		if (!isset($_POST['custom']))
		{
			$this->log('Missing arguments');
			exit;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		
		$invoice_arr = $pjInvoiceModel->where('t1.uuid', $_POST['custom'])->limit(1)->findAll()->getDataIndex(0);
		if ($invoice_arr === FALSE || empty($invoice_arr))
		{
			$this->log('Invoice not found');
			exit;
		}
		$config_arr = pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId());

		$params = array(
			'txn_id' => @$invoice_arr['txn_id'],
			'paypal_address' => $config_arr['p_paypal_address'],
			'deposit' => @$invoice_arr['total'],
			'currency' => @$invoice_arr['currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);

		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Invoice confirmed as paid');
			$pjInvoiceModel->reset()->set('id', $invoice_arr['id'])->modify(array(
				'status' => 'paid',
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Invoice not confirmed as paid');
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$params = $this->getParams();
		if (!isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT))
		{
			return array('status' => 'ERR', 'code' => '101', 'text' => 'Key is not set or invalid');
		}
		
		$locale_id = isset($params['locale_id']) && !empty($params['locale_id']) ? $params['locale_id'] : $this->getLocaleId();
		$config = pjInvoiceConfigModel::factory()->getConfigData($locale_id);
		$config['id'] = NULL;
		unset($config['id']);
		
		$data = array_merge($config, $params);
		
		$invoice_id = pjInvoiceModel::factory($data)->insert()->getInsertId();
		if ($invoice_id !== FALSE && (int) $invoice_id > 0)
		{
			if (isset($params['items']) && is_array($params['items']) && !empty($params['items']))
			{
				$pjInvoiceItemModel = pjInvoiceItemModel::factory();
				foreach ($params['items'] as $item)
				{
					$item['invoice_id'] = $invoice_id;
					$pjInvoiceItemModel->reset()->setAttributes($item)->insert();
				}
			}
			return array('status' => 'OK', 'code' => '200', 'text' => 'Invoice has been created.', 'data' => array_merge($data, array('id' => $invoice_id)));
		} else {
			return array('status' => 'ERR', 'code' => '100', 'text' => 'Invoice has not been created.');
		}
	}
	
	public function pjActionCreateInvoice()
	{
		$this->checkLogin();
		
		if (!$this->isInvoiceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjInvoiceItemModel = pjInvoiceItemModel::factory();
			
		if (isset($_POST['invoice_create']))
		{
			$data = array();
			$data['foreign_id'] = $this->getForeignId();
			$data['issue_date'] = !empty($_POST['issue_date']) ? pjUtil::formatDate($_POST['issue_date'], $this->option_arr['o_date_format']) : NULL;
			$data['due_date'] = !empty($_POST['due_date']) ? pjUtil::formatDate($_POST['due_date'], $this->option_arr['o_date_format']) : NULL;
			$data['s_date'] = !empty($_POST['s_date']) ? pjUtil::formatDate($_POST['s_date'], $this->option_arr['o_date_format']) : NULL;
			$data = array_merge($_POST, $data);
			if (!$pjInvoiceModel->validates($data))
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN06");
			}
			$invoice_id = $pjInvoiceModel->setAttributes($data)->insert()->getInsertId();
			if ($invoice_id !== false && (int) $invoice_id > 0)
			{
				$pjInvoiceItemModel
					->where('tmp', $_POST['tmp'])
					->modifyAll(array(
						'invoice_id' => $invoice_id,
						'tmp' => ":NULL"
					));
				$err = "PIN07";
			} else {
				$err = "PIN08";
			}
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=$err");
		} else {
			
			if (isset($_REQUEST['items']) && !empty($_REQUEST['items']))
			{
				$pjInvoiceItemModel->where('tmp', $_REQUEST['tmp'])->eraseAll();
				foreach ($_REQUEST['items'] as $item)
				{
					$item['tmp'] = $_REQUEST['tmp'];
					$pjInvoiceItemModel->reset()->setAttributes($item)->insert();
				}
			}
			$this->set('uuid', pjInvoiceModel::factory()->getInvoiceID());
			$this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));
			
			$this
				->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
				->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
				->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
				->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
			;
		}
	}
	
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$response = array();
			if (pjInvoiceModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				pjInvoiceItemModel::factory()->where('invoice_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjInvoiceModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjInvoiceItemModel::factory()->whereIn('invoice_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionDeleteItem()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceItemModel = pjInvoiceItemModel::factory();
			$invoice_item = $pjInvoiceItemModel->find($_GET['id'])->getData();
			
			if (!empty($invoice_item) && $pjInvoiceItemModel->erase()->getAffectedRows() == 1)
			{
				$pjInvoiceModel = pjInvoiceModel::factory();
				$invoice = $pjInvoiceModel->find($invoice_item['invoice_id'])->getData();
				if (!empty($invoice))
				{
					$total = (float) $invoice['total'] - (float) $invoice_item['amount'];
					$pjInvoiceModel->modify(array('total' => $total));
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'total' => $total));
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionDeleteLogo()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceConfigModel = pjInvoiceConfigModel::factory();
			
			$arr = $pjInvoiceConfigModel->find(1)->getData();
			if (!empty($arr) && !empty($arr['y_logo']))
			{
				@clearstatcache();
				if (is_file($arr['y_logo']))
				{
					@unlink($arr['y_logo']);
				}
				$pjInvoiceConfigModel->set('id', 1)->modify(array('y_logo' => ':NULL'));
			}
		}
		exit;
	}

	public function pjActionEditItem()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			if (isset($_POST['invoice_edit']))
			{
				pjInvoiceItemModel::factory()->set('id', $_POST['id'])->modify($_POST);
				$response = array('code' => 200);
				pjAppController::jsonResponse($response);
			}
			
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$this->set('arr', pjInvoiceItemModel::factory()
					->select('t1.*, t2.currency')
					->join('pjInvoice', 't2.id=t1.invoice_id', 'left outer')
					->find($_GET['id'])->getData());
				$this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));
			}
		}
	}
	
	public function pjActionGetInvoices()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjCalendarModel = pjCalendarModel::factory()
						->select('id')
						->where('id_usuario_servicio', $_SESSION['usuario_servicio'])
						->findAll()->getData();
			$array_calendarios = array();
			foreach ($pjCalendarModel as $key => $value) {
				$array_calendarios[] = $value['id'];
			}
			$pjInvoiceModel = pjInvoiceModel::factory()->whereIn('t1.foreign_id', $array_calendarios);


			if (isset($_GET['foreign_id']))
			{
				$foreign_arr = $this->get('foreign_arr');

				if ((int) $_GET['foreign_id'] > 0 && $foreign_arr !== FALSE && !empty($foreign_arr))
				{
					$pjInvoiceModel->where('t1.foreign_id', $_GET['foreign_id']);
				}
			}

			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjInvoiceModel->escapeStr($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjInvoiceModel
					->where('t1.uuid LIKE', "%$q%")
					->orWhere('t1.order_id LIKE', "%$q%")
					->orWhere('t1.b_company LIKE', "%$q%")
					->orWhere('t1.b_name LIKE', "%$q%")
					->orWhere('t1.b_email LIKE', "%$q%")
					->orWhere('t1.s_company LIKE', "%$q%")
					->orWhere('t1.s_name LIKE', "%$q%")
					->orWhere('t1.s_email LIKE', "%$q%")
				;
			}

			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjInvoiceModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjInvoiceModel->orderBy("`$column` $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach ($data as $k => $v)
			{
				$data[$k]['total_formated'] = pjUtil::formatCurrencySign(number_format($v['total'], 2), !empty($v['currency']) ? $v['currency'] : $this->option_arr['o_currency']);
			}

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction','pjCalendarModel'));
		}
		exit;
	}	
	public function pjActionGetItems()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceItemModel = pjInvoiceItemModel::factory();
			
			$column = 'id';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			$pjInvoiceItemModel->where('t1.id', -1);
			
			if (isset($_GET['invoice_id']) && (int) $_GET['invoice_id'] > 0)
			{
				$pjInvoiceItemModel->reset()->where('t1.invoice_id', $_GET['invoice_id']);
			}
			if (isset($_GET['tmp']) && !empty($_GET['tmp']))
			{
				$pjInvoiceItemModel->reset()->where('t1.tmp', $_GET['tmp']);
			}
			
			$data = $pjInvoiceItemModel
				->select('t1.*, t2.currency')
				->join('pjInvoice', 't2.id=t1.invoice_id', 'left outer')
				->orderBy("`$column` $direction")->findAll()->getData();
			foreach ($data as $k => $v)
			{
				$data[$k]['unit_price_formated'] = pjUtil::formatCurrencySign(number_format($v['unit_price'], 2), !empty($v['currency']) ? $v['currency'] : $this->option_arr['o_currency']);
				$data[$k]['amount_formated'] = pjUtil::formatCurrencySign(number_format($v['amount'], 2), !empty($v['currency']) ? $v['currency'] : $this->option_arr['o_currency']);
			}
			
			pjAppController::jsonResponse(compact('data', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!$this->isInvoiceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['invoice_post']))
		{
			if (isset($_FILES['y_logo']) && !empty($_FILES['y_logo']['tmp_name']))
			{
				$pjImage = new pjImage();
				$pjImage
					->setAllowedExt(array('png', 'gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'jif', 'jfi'))
					->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
				if ($pjImage->load($_FILES['y_logo']))
				{
					$hash = md5(uniqid(rand(), true));
					$original = 'app/web/invoices/' . $hash . '.' . $pjImage->getExtension();
					$thumb = 'app/web/invoices/' . $hash . '_thumb.png';
					if ($pjImage->save($original))
					{
						$pjImage->loadImage($original)->resizeSmart(120, 60)->saveImage($thumb);
						
						$_POST['y_logo'] = $thumb;
						@unlink($original);
					}
				} else {
					$time = time();
					$_SESSION[$this->invoiceErrors][$time] = $pjImage->getError();
				
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionIndex&err=PIN03&errTime=" . $time);
				}
			}
			$data = array();
			$data['p_accept_payments'] = isset($_POST['p_accept_payments']) ? 1 : 0;
			$data['p_accept_paypal'] = isset($_POST['p_accept_paypal']) ? 1 : 0;
			$data['p_accept_authorize'] = isset($_POST['p_accept_authorize']) ? 1 : 0;
			$data['p_accept_creditcard'] = isset($_POST['p_accept_creditcard']) ? 1 : 0;
			$data['p_accept_cash'] = isset($_POST['p_accept_cash']) ? 1 : 0;
			$data['p_accept_bank'] = isset($_POST['p_accept_bank']) ? 1 : 0;
			$data['si_include'] = isset($_POST['si_include']) ? 1 : 0;
			$data['si_shipping_address'] = isset($_POST['si_shipping_address']) ? 1 : 0;
			$data['si_company'] = isset($_POST['si_company']) ? 1 : 0;
			$data['si_name'] = isset($_POST['si_name']) ? 1 : 0;
			$data['si_address'] = isset($_POST['si_address']) ? 1 : 0;
			$data['si_street_address'] = isset($_POST['si_street_address']) ? 1 : 0;
			$data['si_city'] = isset($_POST['si_city']) ? 1 : 0;
			$data['si_state'] = isset($_POST['si_state']) ? 1 : 0;
			$data['si_zip'] = isset($_POST['si_zip']) ? 1 : 0;
			$data['si_phone'] = isset($_POST['si_phone']) ? 1 : 0;
			$data['si_fax'] = isset($_POST['si_fax']) ? 1 : 0;
			$data['si_email'] = isset($_POST['si_email']) ? 1 : 0;
			$data['si_url'] = isset($_POST['si_url']) ? 1 : 0;
			$data['si_date'] = isset($_POST['si_date']) ? 1 : 0;
			$data['si_terms'] = isset($_POST['si_terms']) ? 1 : 0;
			$data['si_is_shipped'] = isset($_POST['si_is_shipped']) ? 1 : 0;
			$data['si_shipping'] = isset($_POST['si_shipping']) ? 1 : 0;
			$data['o_qty_is_int'] = isset($_POST['o_qty_is_int']) ? 1 : 0;
			$data['o_use_qty_unit_price'] = isset($_POST['o_use_qty_unit_price']) ? 1 : 0;
			
			pjInvoiceConfigModel::factory()
				->set('id', 1)
				->modify(array_merge($_POST, $data));
			
			if (isset($_POST['i18n']))
			{
				pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], 1, 'pjInvoiceConfig');
			}
			
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionIndex&err=PIN02&tab_id=" . $_POST['tab_id']);
		}
		
		$this->set('timezones', $this->sortTimezones(__('timezones', true)));
		$this->set('country_arr', pjCountryModel::factory()
			->select('t1.*, t2.content AS `name`')
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->orderBy('`name` ASC')
			->findAll()->getData());
		
		$arr = pjInvoiceConfigModel::factory()->find(1)->getData();
		if (!empty($arr))
		{
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjInvoiceConfig');
		}
		
		$locale_arr = pjLocaleModel::factory()
			->select('t1.*, t2.file')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')
			->findAll()
			->getData();
		
		$lp_arr = array();
		foreach ($locale_arr as $item)
		{
			$lp_arr[$item['id']."_"] = $item['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
		}
		$this->set('lp_arr', $locale_arr);
		$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
		
		$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
		
		$this
			->set('arr', $arr)
			->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/')
			->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
			->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
			->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/')
			->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/')
			->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
			->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
		;
	}
	
	public function pjActionInvoices()
	{
		$this->checkLogin();
		
		if (!$this->isInvoiceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		$this
			->set('invoice_config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()))
			->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
			->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
			->appendCss('plugin_invoice.css', $this->getConst('PLUGIN_CSS_PATH'))
			->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
		;
	}
	
	public function pjActionPayment()
	{
		$this->setLayout('pjActionEmpty');
		
		$arr = pjInvoiceModel::factory()->where('t1.uuid', $_POST['uuid'])->limit(1)->findAll()->getDataIndex(0);
		if ($arr === FALSE || empty($arr))
		{
			return;
		}
		
		$config_arr = pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId());
		
		$data = array();
		if($_POST['payment_method'] == 'creditcard')
		{
			$data['cc_type'] = $_POST['cc_type'];
			$data['cc_num'] = $_POST['cc_num'];
			$data['cc_code'] = $_POST['cc_code'];
			$data['cc_exp_month'] = $_POST['cc_exp_month'];
			$data['cc_exp_year'] = $_POST['cc_exp_year'];
		}
		$data['payment_method'] = $_POST['payment_method'];
		pjInvoiceModel::factory()->set('id', $arr['id'])->modify($data);
		
		switch ($_POST['payment_method'])
		{
			case 'paypal':
				$this->set('params', array(
					'target' => '_self',
					'name' => 'pinPaypal',
					'id' => 'pinPaypal',
					'business' => $config_arr['p_paypal_address'],
					'item_name' => $arr['uuid'],
					'custom' => $arr['uuid'],
					'amount' => $arr['paid_deposit'],
					'currency_code' => $arr['currency'],
					'return' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&uuid=' . $arr['uuid'],
					'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionConfirmPaypal&cid=' . $arr['foreign_id']
				));
				break;
			case 'authorize':
				$this->set('params', array(
					'name' => 'pinAuthorize',
					'id' => 'pinAuthorize',
					'timezone' => $config_arr['p_authorize_tz'],
					'transkey' => $config_arr['p_authorize_key'],
					'x_login' => $config_arr['p_authorize_mid'],
					'x_description' => $arr['uuid'],
					'x_amount' => $arr['paid_deposit'],
					'x_invoice_num' => $arr['uuid'],
					'x_receipt_link_url' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&uuid=' . $arr['uuid'],
					'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionConfirmAuthorize&cid=' . $arr['foreign_id']
				));
				break;
		}
		
		$this
			->set('config_arr', $config_arr)
			->resetCss()
			->resetJs()
			->appendCss('invoice.css', $this->getConst('PLUGIN_CSS_PATH'))
		;
	}
	
	public function pjActionPrint()
	{
		$this->pjActionView();
	}
	
	public function pjActionSaveItem()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceItemModel = pjInvoiceItemModel::factory();
			if (!in_array($_POST['column'], $pjInvoiceItemModel->getI18n()))
			{
				$pjInvoiceItemModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjInvoiceItem');
			}
		}
		exit;
	}
	
	public function pjActionSend()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			if (isset($_GET['uuid']) && !empty($_GET['uuid']))
			{
				$arr = pjInvoiceModel::factory()
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.b_country AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjCountry' AND t4.foreign_id=t1.s_country AND t4.field='name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as y_country_title, t3.content as b_country_title, t3.content as s_country_title, 
						AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS cc_type, 
						AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS cc_num, 
						AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS cc_exp_month, 
						AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS cc_exp_year,
						AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS cc_code")
					->where('t1.uuid', $_GET['id'])
					->where('t1.order_id', $_GET['uuid'])
					->limit(1)->findAll()->getDataIndex(0);
				$this->set('arr', $arr);
				$this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));
			}
			
			if (isset($_POST['uuid']) && !empty($_POST['uuid']))
			{
				// Validate data
				$b_send = (isset($_POST['b_send']) && isset($_POST['b_email']) && !empty($_POST['b_email']) && pjValidation::pjActionEmail($_POST['b_email']));
				$s_send = (isset($_POST['s_send']) && isset($_POST['s_email']) && !empty($_POST['s_email']) && pjValidation::pjActionEmail($_POST['s_email']));
				if (!$b_send && !$s_send)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Email(s) not selected.'));
				}
				
				// Build message
				$arr = pjInvoiceModel::factory()->where('t1.uuid', $_POST['id'])->where('t1.order_id', $_POST['uuid'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr === FALSE || empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Invoice not found.'));
				}
				$arr['items'] = pjInvoiceItemModel::factory()->where('t1.invoice_id', $arr['id'])->findAll()->getData();
				$confi_arr = pjInvoiceConfigModel::factory()->find(1)->getData();
				$arr['y_logo'] = '<img src="'.PJ_INSTALL_URL.$confi_arr['y_logo'].'" />';
				$arr['o_use_qty_unit_price'] = $confi_arr['o_use_qty_unit_price'];
				
				$view_url = PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&id=' . $_POST['id'] . '&uuid=' . $_POST['uuid'];
				$view_url = '<a href="'.$view_url.'">'.$view_url.'</a>';
				// Send message
				$pjEmail = new pjEmail();
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
				
				if ($b_send && $s_send)
				{
					$pjEmail
						->setTo($_POST['b_email'])
						->setCc($_POST['s_email'])
					;
				} elseif ($b_send && !$s_send) {
					$pjEmail->setTo($_POST['b_email']);
				} elseif (!$b_send && $s_send) {
					$pjEmail->setTo($_POST['s_email']);
				}
				$message = '';
				if($arr['status'] == 'not_paid')
				{
					$message .= '<p>'.__('plugin_invoice_i_send_invoice_link', true).'</p>';
					$message .= $view_url . '<br/><br/><br/><br/>';
				}
				$message .= $this->pjActionTokenizer($arr);
				
				$master_admin = pjUserModel::factory()->find(1)->getData();
				$y_email = $master_admin['email'];
				if(!empty($arr['y_email']) && pjValidation::pjActionEmail($arr['y_email']))
				{
					$y_email = $arr['y_email'];
				}
				$result = $pjEmail
					->setContentType('text/html')
					->setFrom($y_email)
					->setReplyTo($y_email)
					->setSubject(__('plugin_invoice_send_subject', true))
					->send($message)
				;
				
				if ($result)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				} else {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email has not been sent.'));
				}
			}
		}
	}
	
	private function pjActionTokenizer($a, $locale_id=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = !empty($a['locale_id']) ? $a['locale_id'] : $this->getLocaleID();
		}
		
		$config = pjInvoiceConfigModel::factory()->getConfigData($locale_id);
		
		$items = "";
		if (isset($a['items']) && is_array($a['items']) && !empty($a['items']))
		{
			$items .= '<table style="width: 100%; border-collapse: collapse">';
			$items .= '<tr>';
			$items .= '<td style="border-bottom: solid 1px #000; border-top: solid 1px #000">'.__('plugin_invoice_i_description', true).'</td>';
			if($a['o_use_qty_unit_price'] == 1)
			{
				$items .= '<td style="border-bottom: solid 1px #000; border-top: solid 1px #000; text-align: right">'.__('plugin_invoice_i_qty', true).'</td>';
				$items .= '<td style="border-bottom: solid 1px #000; border-top: solid 1px #000; text-align: right">'.__('plugin_invoice_i_unit', true).'</td>';
			}
			$items .= '<td style="border-bottom: solid 1px #000; border-top: solid 1px #000; text-align: right">'.__('plugin_invoice_i_amount', true).'</td>';
			$items .= '</tr>';
			foreach ($a['items'] as $item)
			{
				$items .= '<tr>';
				$items .= sprintf('<td>%s<br>%s</td>', $item['name'], $item['description']);
				if($a['o_use_qty_unit_price'] == 1)
				{
					$items .= sprintf('<td style="text-align: right">%s</td>', number_format($item['qty'], (int) $config['o_qty_is_int'] === 0 ? 2 : 0));
					$items .= sprintf('<td style="text-align: right">%s</td>', number_format($item['unit_price'], 2));
				}
				$items .= sprintf('<td style="text-align: right">%s</td>', number_format($item['amount'], 2));
				$items .= '</tr>';
			}
			$items .= '</table>';
		}
		$statuses = __('plugin_invoice_statuses', true);
		$_yesno = __('plugin_invoice_yesno', true);
		return str_replace(
			array(
				'{uuid}',
				'{order_id}',
				'{issue_date}',
				'{due_date}',
				'{created}',
				'{modified}',
				'{status}',
				'{subtotal}',
				'{discount}',
				'{tax}',
				'{shipping}',
				'{total}',
				'{paid_deposit}',
				'{amount_due}',
				'{currency}',
				'{notes}',
				'{y_logo}',
				'{y_company}',
				'{y_name}',
				'{y_street_address}',
				'{y_country}',
				'{y_city}',
				'{y_state}',
				'{y_zip}',
				'{y_phone}',
				'{y_fax}',
				'{y_email}',
				'{y_url}',
				'{b_billing_address}',
				'{b_company}',
				'{b_name}',
				'{b_address}',
				'{b_street_address}',
				'{b_country}',
				'{b_city}',
				'{b_state}',
				'{b_zip}',
				'{b_phone}',
				'{b_fax}',
				'{b_email}',
				'{b_url}',
				'{s_shipping_address}',
				'{s_company}',
				'{s_name}',
				'{s_address}',
				'{s_street_address}',
				'{s_country}',
				'{s_city}',
				'{s_state}',
				'{s_zip}',
				'{s_phone}',
				'{s_fax}',
				'{s_email}',
				'{s_url}',
				'{s_date}',
				'{s_terms}',
				'{s_is_shipped}',
				'{items}'
			),
			array(
				$a['uuid'],
				$a['order_id'],
				pjUtil::formatDate($a['issue_date'], 'Y-m-d', $this->option_arr['o_date_format']),
				pjUtil::formatDate($a['due_date'], 'Y-m-d', $this->option_arr['o_date_format']),
				!empty($a['created']) ? date($this->option_arr['o_date_format'] . " H:i:s", strtotime($a['created'])) : NULL,
				!empty($a['modified']) ? date($this->option_arr['o_date_format'] . " H:i:s", strtotime($a['modified'])) : NULL,
				$statuses[$a['status']],
				number_format($a['subtotal'], 2),
				number_format($a['discount'], 2),
				number_format($a['tax'], 2),
				number_format($a['shipping'], 2),
				number_format($a['total'], 2),
				number_format($a['paid_deposit'], 2),
				number_format($a['amount_due'], 2),
				$a['currency'],
				$a['notes'],
				$a['y_logo'],
				$a['y_company'],
				$a['y_name'],
				$a['y_street_address'],
				$a['y_country_title'],
				$a['y_city'],
				$a['y_state'],
				$a['y_zip'],
				$a['y_phone'],
				$a['y_fax'],
				$a['y_email'],
				$a['y_url'],
				$a['b_billing_address'],
				$a['b_company'],
				$a['b_name'],
				$a['b_address'],
				$a['b_street_address'],
				$a['b_country_title'],
				$a['b_city'],
				$a['b_state'],
				$a['b_zip'],
				$a['b_phone'],
				$a['b_fax'],
				$a['b_email'],
				$a['b_url'],
				$a['s_shipping_address'],
				$a['s_company'],
				$a['s_name'],
				$a['s_address'],
				$a['s_street_address'],
				$a['s_country_title'],
				$a['s_city'],
				$a['s_state'],
				$a['s_zip'],
				$a['s_phone'],
				$a['s_fax'],
				$a['s_email'],
				$a['s_url'],
				pjUtil::formatDate($a['s_date'], 'Y-m-d', $this->option_arr['o_date_format']),
				$a['s_terms'],
				$_yesno[$a['s_is_shipped']],
				$items
			),
			$config['y_template']
		);
	}
	
	public function pjActionSaveInvoice()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$value = $_POST['value'];				
			pjInvoiceModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $value));
		}
		exit;
	}
	
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if (!$this->isInvoiceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		
		if (isset($_POST['invoice_update']))
		{
			$arr = $pjInvoiceModel->find($_POST['id'])->getData();
			if (empty($arr))
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04");
			}
			
			$data = array();
			$data['foreign_id'] = $arr['foreign_id'];
			$data['modified'] = ':NOW()';
			$data['issue_date'] = !empty($_POST['issue_date']) ? pjUtil::formatDate($_POST['issue_date'], $this->option_arr['o_date_format']) : NULL;
			$data['due_date'] = !empty($_POST['due_date']) ? pjUtil::formatDate($_POST['due_date'], $this->option_arr['o_date_format']) : NULL;
			$data['s_date'] = !empty($_POST['s_date']) ? pjUtil::formatDate($_POST['s_date'], $this->option_arr['o_date_format']) : NULL;
			$data = array_merge($_POST, $data);
			if (!$pjInvoiceModel->validates($data))
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN06");
			}
			$pjInvoiceModel->set('id', $_POST['id'])->modify($data);
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionUpdate&id=".$_POST['id']."&err=PIN05");
		}
		
		$arr = $pjInvoiceModel->find($_GET['id'])->getData();
		if (empty($arr))
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04");
		}
		$this->set('country_arr', pjCountryModel::factory()
			->select('t1.*, t2.content AS `name`')
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->orderBy('`name` ASC')
			->findAll()->getData());
		$this
			->set('arr', $arr)
			->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()))
			->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
			->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
			->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
			->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
		;
	}

	public function pjActionView()
	{
		$this->setLayout('pjActionEmpty');
		
		$arr = pjInvoiceModel::factory()
			->join('pjMultiLang', sprintf("t2.model='pjCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='%u'", $this->getLocaleId()), 'left outer')
			->join('pjMultiLang', sprintf("t3.model='pjCountry' AND t3.foreign_id=t1.b_country AND t3.field='name' AND t3.locale='%u'", $this->getLocaleId()), 'left outer')
			->join('pjMultiLang', sprintf("t4.model='pjCountry' AND t4.foreign_id=t1.s_country AND t4.field='name' AND t4.locale='%u'", $this->getLocaleId()), 'left outer')
			->select("t1.*, t2.content as y_country_title, t3.content as b_country_title, t3.content as s_country_title, 
						AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS cc_type, 
						AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS cc_num, 
						AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS cc_exp_month, 
						AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS cc_exp_year,
						AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS cc_code")
			->where('t1.uuid', @$_GET['id'])
			->where('t1.order_id', @$_GET['uuid'])
			->limit(1)
			->findAll()
			->getDataIndex(0);
		
		if ($arr === FALSE || empty($arr))
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04");
		}

		$arr['items'] = pjInvoiceItemModel::factory()->where('t1.invoice_id', $arr['id'])->findAll()->getData();
		$locale_id = !empty($arr['locale_id']) ? $arr['locale_id'] : $this->getLocaleId();
		$confi_arr = pjInvoiceConfigModel::factory()->getConfigData($locale_id);
		$arr['y_logo'] = '<img src="'.PJ_INSTALL_URL.$confi_arr['y_logo'].'" />';
		$arr['o_use_qty_unit_price'] = $confi_arr['o_use_qty_unit_price'];
		$this
			->set('arr', $arr)
			->set('config_arr', $confi_arr)
			->set('template', $this->pjActionTokenizer($arr, $locale_id))
			->resetCss()
			->resetJs()
			->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/')
			->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
			->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
			->appendCss('invoice.css', $this->getConst('PLUGIN_CSS_PATH'))
		;
	}
}
?>
