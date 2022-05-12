<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionCopy(){
		$this->setAjax(true);

		if ($this->isXHR())
                    
		{
			if (isset($_POST['calendar_id']) && (int) $_POST['calendar_id'] > 0 && isset($_POST['tab_id']) && (int) $_POST['tab_id'] > 0)
			{
				$pjOptionModel = pjOptionModel::factory();

				$src = $pjOptionModel->where('t1.foreign_id', $_POST['calendar_id'])->where('t1.tab_id', $_POST['tab_id'])->findAll()->getData();
				$src_pair = $pjOptionModel->getDataPair('key', 'value');
				$pjOptionModel->begin();
				foreach ($src as $option)
				{
					$pjOptionModel
						->reset()
						->where('foreign_id', $this->getForeignId())
						->where('`key`', $option['key'])
						->limit(1)
						->modifyAll(array('value' => $option['value']));
				}
				$pjOptionModel->commit();

				$pjLimitModel = pjLimitModel::factory();
				$limit_arr = $pjLimitModel->where('t1.calendar_id', $_POST['calendar_id'])->findAll()->getData();

				$pjLimitModel->reset()->begin();
				foreach($limit_arr as $limit)
				{
					$pjLimitModel
						->reset()
						->set('calendar_id', $this->getForeignId())
						->set('date_from', $limit['date_from'])
						->set('date_to', $limit['date_to'])
						->set('min_nights', $limit['min_nights'])
						->set('max_nights', $limit['max_nights'])
						->insert()
						;
				}
				$pjLimitModel->commit();

				$fields = array();
				if ((int) $_POST['tab_id'] === 5)
				{
					$fields = array('confirm_subject', 'confirm_tokens', 'payment_subject', 'payment_tokens');
				} elseif ((int) $_POST['tab_id'] === 6) {
					$fields = array('terms_url', 'terms_body');
				} elseif ((int) $_POST['tab_id'] === 2) {
					set_time_limit(300);
					pjUtil::pjActionGenerateImages($this->getForeignId(), $src_pair);
				}

				if (!empty($fields))
				{
					$pjMultiLangModel = pjMultiLangModel::factory();

					$src = $pjMultiLangModel
						->where('t1.model', 'pjCalendar')
						->where('t1.foreign_id', $_POST['calendar_id'])
						->whereIn('t1.field', $fields)
						->findAll()->getData();

					$pjMultiLangModel->begin();
					foreach ($src as $item)
					{
						$item['id'] = NULL;
						unset($item['id']);
						$item['foreign_id'] = $this->getForeignId();

						$pjMultiLangModel->prepare(sprintf(
							"INSERT INTO `%s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`)
							VALUES (NULL, :foreign_id, :model, :locale, :field, :content)
							ON DUPLICATE KEY UPDATE `content` = :content", $pjMultiLangModel->getTable())
						)->exec($item);
					}
					$pjMultiLangModel->commit();
				}
			}
		}
		exit;
	}


	public function pjActionRemoveImage()	{
		pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");

	}
	public function pjActionIndex(){
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (!isset($_GET['tab']) || (int) $_GET['tab'] <= 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&tab=1");
			}

			if (isset($_GET['cid']))
			{
				$this->setForeignId($_GET['cid']);
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=pjActionIndex&tab=" . $_GET['tab']);
			}

			if (isset($_GET['tab']) && in_array((int) $_GET['tab'], array(5,6)))
			{
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();

				$lp_arr = array();
				foreach ($locale_arr as $v)
				{
					$lp_arr[$v['id']."_"] = $v['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));

				$arr = array();
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjCalendar');
				$this->set('arr', $arr);

				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				if ($_GET['tab'] == 6)
				{
					$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				}
			} elseif (isset($_GET['tab']) && in_array((int) $_GET['tab'], array(10))) {
				$this->set('arr', pjLimitModel::factory()->where('t1.calendar_id', $this->getForeignId())->findAll()->getData());
			} else {
				$tab_id = isset($_GET['tab']) && (int) $_GET['tab'] > 0 ? (int) $_GET['tab'] : 1;
				$arr = pjOptionModel::factory()
					->where('foreign_id', $this->getForeignId())
					->where('tab_id', $tab_id)
					->orderBy('t1.order ASC')
					->findAll()
					->getData();
				$this->set('arr', $arr);

				$tmp = $this->models['Option']->reset()->where('foreign_id', $this->getForeignId())->findAll()->getData();
				$o_arr = array();
				foreach ($tmp as $item)
				{
					$o_arr[$item['key']] = $item;
				}
				$this->set('o_arr', $o_arr);

				$this->appendJs('jquery.miniColors.min.js', PJ_THIRD_PARTY_PATH . 'mini_colors/');
				$this->appendCss('jquery.miniColors.css', PJ_THIRD_PARTY_PATH . 'mini_colors/');

				if ($tab_id == 1)
				{
					$calendar_arr = pjCalendarModel::factory()->find($this->getForeignId())->getData();
					if (!empty($calendar_arr))
					{
						$calendar_arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($calendar_arr['id'], 'pjCalendar');
						$this->set('calendar_arr', $calendar_arr);
					}

					if ((int) $this->option_arr['o_multi_lang'] === 1)
					{
						$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
							->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
							->where('t2.file IS NOT NULL')
							->orderBy('t1.sort ASC')->findAll()->getData();

						$lp_arr = array();
						foreach ($locale_arr as $v)
						{
							$lp_arr[$v['id']."_"] = $v['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
						}
						$this->set('lp_arr', $locale_arr);
						$this->set('locale_str', pjAppController::jsonEncode($lp_arr));

						$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
						$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
					}
					$this->set('user_arr', pjUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData());
				}
			}
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
		}
		elseif ($this->isOwner())
		{

			if (!isset($_GET['tab']) || (int) $_GET['tab'] <= 0)
			{

				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions1&tab=1");
			}

			if (isset($_GET['cid']))
			{
				$this->setForeignId($_GET['cid']);
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=pjActionIndex&tab=" . $_GET['tab']);
			}

			if (isset($_GET['tab']) && in_array((int) $_GET['tab'], array(5,6)))
			{
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();

				$lp_arr = array();
				foreach ($locale_arr as $v)
				{
					$lp_arr[$v['id']."_"] = $v['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				$this->set('agrupamiento_arr', pjCalendarGroupModel::factory()->where('id_usuario_servicio',$_SESSION['usuario_servicio'])->orderBy('t1.nombre ASC')->findAll()->getData());

				$arr = array();
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjCalendar');
				$this->set('arr', $arr);

				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				if ($_GET['tab'] == 6)
				{
					$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				}
			} elseif (isset($_GET['tab']) && in_array((int) $_GET['tab'], array(10))) {
				$this->set('arr', pjLimitModel::factory()->where('t1.calendar_id', $this->getForeignId())->findAll()->getData());
			} else {
				$tab_id = isset($_GET['tab']) && (int) $_GET['tab'] > 0 ? (int) $_GET['tab'] : 1;
				$arr = pjOptionModel::factory()
					->where('foreign_id', $this->getForeignId())
					->where('tab_id', $tab_id)
					->orderBy('t1.order ASC')
					->findAll()
					->getData();
				$this->set('arr', $arr);

				$tmp = $this->models['Option']->reset()->where('foreign_id', $this->getForeignId())->findAll()->getData();
				$o_arr = array();
				foreach ($tmp as $item)
				{
					$o_arr[$item['key']] = $item;
				}
				$this->set('o_arr', $o_arr);

				$this->appendJs('jquery.miniColors.min.js', PJ_THIRD_PARTY_PATH . 'mini_colors/');
				$this->appendCss('jquery.miniColors.css', PJ_THIRD_PARTY_PATH . 'mini_colors/');

				if ($tab_id == 1)
				{
					$idCalendario = $this->getForeignId();
					//*****************************************************//
					//  BUSCO EN LA BASE LAS IMAGENES DEL CALENDARIO     //
					//*****************************************************//
					$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
					if($mysqli->connect_errno){
					 echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
					}

					$sql = "SELECT id,filename,descripcion_fotografia FROM images WHERE id_auxiliar = $idCalendario AND estado_fotografia = 1 AND id_catalogo_fotografia = 10";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						$infoFoto = array();
					    	while($row = $result->fetch_assoc()) {
					             	$infoFoto[] =  $row;
					            }
			    			$fotos_calendario = $infoFoto;
			    			$contador_fotos_calendario = count($fotos_calendario);
			    			$this->set('fotos_calendario', $fotos_calendario);
						$this->set('contador_fotos_calendario', $contador_fotos_calendario);
			    		}

					$calendar_arr = pjCalendarModel::factory()->find($this->getForeignId())->getData();
					if (!empty($calendar_arr))
					{
						$calendar_arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($calendar_arr['id'], 'pjCalendar');
						$this->set('calendar_arr', $calendar_arr);
					}

					if ((int) $this->option_arr['o_multi_lang'] === 1)
					{
						$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
							->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
							->where('t2.file IS NOT NULL')
							->orderBy('t1.sort ASC')->findAll()->getData();

						$lp_arr = array();
						foreach ($locale_arr as $v)
						{
							$lp_arr[$v['id']."_"] = $v['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
						}
						$this->set('lp_arr', $locale_arr);
						$this->set('locale_str', pjAppController::jsonEncode($lp_arr));

						$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
						$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
					}
					$this->set('agrupamiento_arr', pjCalendarGroupModel::factory()->where('id_usuario_servicio',$_SESSION['usuario_servicio'])->orderBy('t1.nombre ASC')->findAll()->getData());
					$this->set('user_arr', pjUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData());
				}
			}
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
		}
		else {
			$this->set('status', 2);
		}
	}

	public function pjActionInstall()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->orderBy('t1.sort ASC')->findAll()->getData();
			$this->set('locale_arr', $locale_arr);

			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionNotifications()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$this->set('o_arr', pjOptionModel::factory()->getPairs($this->getForeignId()));
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionPreview()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
	}

	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner() )
		{
			if (isset($_POST['options_update']))
			{
				if (isset($_POST['tab']) && in_array($_POST['tab'], array(5, 6))){
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $this->getForeignId(), 'pjCalendar');
					}
				} elseif (isset($_POST['tab']) && in_array($_POST['tab'], array(10))) {
					$pjLimitModel = pjLimitModel::factory();

					$pjLimitModel->where('calendar_id', $this->getForeignId())->eraseAll();
					$pjLimitModel->begin();
					$haystack = array();
					$dates = array();
					$overlaping = false;
					if (isset($_POST['date_from'], $_POST['date_to']) && !empty($_POST['date_from']))
					{
						foreach ($_POST['date_from'] as $k => $v)
						{
							if (empty($_POST['date_from'][$k]) || empty($_POST['date_to'][$k]) ||
								(empty($_POST['min_nights'][$k]) && empty($_POST['max_nights'][$k])) ||
								(!empty($_POST['min_nights'][$k]) && !empty($_POST['max_nights'][$k]) && (int) $_POST['min_nights'][$k] > (int) $_POST['max_nights'][$k])
							)
							{
								continue;
							}

							$overlap = false;
							$date_from = strtotime($_POST['date_from'][$k]);
							$date_to = strtotime($_POST['date_to'][$k]);
							foreach ($dates as $item)
							{
								if ($item['date_from'] <= $date_to && $item['date_to'] >= $date_from)
								{
									$overlap = true;
									$overlaping = true;
									break;
								}
							}
							if ($overlap)
							{
								continue;
							}

							$needle = $_POST['date_from'][$k] . "_" . $_POST['date_to'][$k];
							if (in_array($needle, $haystack))
							{
								continue;
							}
							array_push($haystack, $needle);
							array_push($dates, array('date_from' => strtotime($_POST['date_from'][$k]), 'date_to' => strtotime($_POST['date_to'][$k])));

							$pjLimitModel
								->reset()
								->set('calendar_id', $this->getForeignId())
								->set('date_from', pjUtil::formatDate($_POST['date_from'][$k], $this->option_arr['o_date_format']))
								->set('date_to', pjUtil::formatDate($_POST['date_to'][$k], $this->option_arr['o_date_format']))
								->set('min_nights', $_POST['min_nights'][$k])
								->set('max_nights', $_POST['max_nights'][$k])
								->insert()
							;
						}
					}
					$pjLimitModel->commit();
				} else {
					$OptionModel = pjOptionModel::factory();
					/*$OptionModel
						->where('foreign_id', $this->getForeignId())
						->where('type', 'bool')
						->where('tab_id', $_POST['tab'])
						->modifyAll(array('value' => '1|0::0'));*/
					//*******************************************************************//
					//     UPDATE DE LOS DATOS PREDETERMINADOS DEL CALENDARIO  //
					//  		PAYPAL, AUTHORIZE, EMAIL, CURRENCY                  //
					//*******************************************************************//

					$pjOptionModel = pjOptionModel::factory();
					$dataKey = ["o_allow_authorize","o_allow_bank","o_allow_cash","o_allow_creditcard",
	                    				"o_allow_paypal","o_authorize_hash","o_authorize_key","o_authorize_mid",
	                    				"o_authorize_tz", "o_bank_account","o_cancel_url","o_currency","o_paypal_address",
	                    				"o_send_email","o_smtp_host","o_smtp_pass","o_smtp_port","o_smtp_user",
	                    				"o_thankyou_page","o_bf_lastname","o_bf_cedula"];

	    				$dataValue = ["1|0::0","1|0::0","1|0::0","1|0::1","1|0::0",
	    						PJ_O_AUHTORIZE_HASH,PJ_O_AUHTORIZE_KEY,
	    						PJ_O_AUHTORIZE_MID,"-43200|-39600|-36000|-32400|-28800|-25200|-21600|-18000|-14400|-10800|-7200|-3600|0|3600|7200|10800|14400|18000|21600|25200|28800|32400|36000|39600|43200|46800::0","info",
	    						$_SERVER['PHP_SELF']."?controller=pjAdmin&action=pjActionError",
	    						"AED|AFN|ALL|AMD|ANG|AOA|ARS|AUD|AWG|AZN|BAM|BBD|BDT|BGN|BHD|BIF|BMD|BND|BOB|BOV|BRL|BSD|BTN|BWP|BYR|BZD|CAD|CDF|CHE|CHF|CHW|CLF|CLP|CNY|COP|COU|CRC|CUC|CUP|CVE|CZK|DJF|DKK|DOP|DZD|EEK|EGP|ERN|ETB|EUR|FJD|FKP|GBP|GEL|GHS|GIP|GMD|GNF|GTQ|GYD|HKD|HNL|HRK|HTG|HUF|IDR|ILS|INR|IQD|IRR|ISK|JMD|JOD|JPY|KES|KGS|KHR|KMF|KPW|KRW|KWD|KYD|KZT|LAK|LBP|LKR|LRD|LSL|LTL|LVL|LYD|MAD|MDL|MGA|MKD|MMK|MNT|MOP|MRO|MUR|MVR|MWK|MXN|MXV|MYR|MZN|NAD|NGN|NIO|NOK|NPR|NZD|OMR|PAB|PEN|PGK|PHP|PKR|PLN|PYG|QAR|RON|RSD|RUB|RWF|SAR|SBD|SCR|SDG|SEK|SGD|SHP|SLL|SOS|SRD|STD|SYP|SZL|THB|TJS|TMT|TND|TOP|TRY|TTD|TWD|TZS|UAH|UGX|USD|USN|USS|UYU|UZS|VEF|VND|VUV|WST|XAF|XAG|XAU|XBA|XBB|XBC|XBD|XCD|XDR|XFU|XOF|XPD|XPF|XPT|XTS|XXX|YER|ZAR|ZMK|ZWL::USD",
	    						PJ_O_PAYPAL_ADDRESS, "mail|smtp::smtp",PJ_O_SMTP_HOST,
	    						PJ_O_SMTP_PASS,PJ_O_SMTP_PORT, PJ_O_SMTP_USER,
	    						$url_Paypal,"1|2|3::3","1|2|3::3"];

	    				$dataVisible = ["0","0","0","0","0","0","0","0","0","0","0","1","0","0","0","0","0","0","0","1","1"];
	    				$contador = count($dataVisible);

	    				$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
	    				if($mysqli->connect_errno){
					        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
					}
					$id = $this->getForeignId();

 					for($i = 0; $i < $contador; $i++){
	    					if($dataKey[$i] ==  "o_bf_lastname" ){
		    					$conn->query("INSERT INTO booking_abcalendar_options(foreign_id, `key`, tab_id, `value`, label, type, `order`, is_visible, style) VALUES($id,'o_bf_lastname','4','1|2|3::3','No|Yes|Yes (Required)','enum','1','1',NULL)");
	    					}elseif ($dataKey[$i] == "o_bf_cedula") {
	    						$conn->query("INSERT INTO booking_abcalendar_options(foreign_id, `key`, tab_id, `value`, label, type, `order`, is_visible, style) VALUES($id,'o_bf_cedula', '4','1|2|3::3', 'No|Yes|Yes (Required)','enum','1','1','NULL')");
	    					}else{
		    					$sql = "UPDATE booking_abcalendar_options
		    					SET value = '$dataValue[$i]' , is_visible = '$dataVisible[$i]'
		    					WHERE foreign_id = $id AND  `key` = '$dataKey[$i]'";
		    					if ($conn->query($sql) === TRUE) {
						    		//echo "Record updated successfully";
								} else {
									echo "Error updating record: " . $conn->error;
								}
	    					}
	    			}



					foreach ($_POST as $key => $value)
					{
						if (preg_match('/value-(string|text|int|float|enum|color|bool)-(.*)/', $key) === 1)
						{
							list(, $type, $k) = explode("-", $key);
							if (!empty($k))
							{
								$OptionModel->reset();
								if (!in_array($k, $uniform))
								{
									$OptionModel->where('foreign_id', $this->getForeignId())->limit(1);
								}
								$OptionModel
									->where('`key`', $k)
									->modifyAll(array(
										'value' => $value
									));
							}
						}
					}
				}

				if (isset($_POST['tab']) && $_POST['tab'] == 1)
				{
					$idForaneoCalendar = $this->getForeignId();

					//***********************************************************//
					//           ACTUALIZA LA INFORMACION DEL CALENDARIO              //
					//***********************************************************//
					if (isset($idForaneoCalendar))
					{
						/*pjCalendarModel::factory()->set('id', $this->getForeignId())->modify(array('user_id' => $_POST['user_id']));*/
						//pjCalendarModel::factory()->set('id', $this->getForeignId())->modify(array('descripcion' => $_POST['descripcion']));
						//pjCalendarModel::factory()->set('id', $this->getForeignId())->modify(array('descripcion' => $_POST['descripcion'],'id_agrupamiento'=>$_POST['id_agrupamiento']));
						pjCalendarModel::factory()->set('id', $this->getForeignId())->modify(array('descripcion' => $_POST['descripcion'],'descripcion_eng' => $_POST['descripcion_eng'],'id_agrupamiento'=>$_POST['id_agrupamiento'],'correo_operador' => $_POST['correo_operador']));						

						/*ACTUALIZO EL SEARCH ENGINE*/
						$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
						if($mysqli->connect_errno){
						     echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
						}
						 $searchEngine = "SELECT search FROM searchengine WHERE id_usuario_servicio = '".$this->getForeignId()."'  AND tipo_busqueda = 8";
						$conn->query($searchEngine);
						$search = "";
						foreach ($conn->query($searchEngine) as $row1) {
			        				$search = $row1['search'];
			        			}
			        		$search = $search." ".$_POST['nombre']." ".$_POST['descripcion']." ".$_POST['tags'];
			        		$updateSearchEngine = "UPDATE searchengine SET search = '$search'
		    					WHERE id_usuario_servicio = '".$this->getForeignId()."' AND  tipo_busqueda = 8";
		    				$conn->query($updateSearchEngine);
					}
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $this->getForeignId(), 'pjCalendar');
					}
				}

				if (isset($_POST['tab']) && in_array($_POST['tab'], array(2)))
				{
					set_time_limit(300);
					$data = pjOptionModel::factory()->getAllPairs($this->getForeignId());
					pjUtil::pjActionGenerateImages($this->getForeignId(), $data);
				}

				if (isset($_POST['tab']))
				{
					switch ($_POST['tab'])
					{
						case '1':
							$err = 'AO01';
							break;
						case '2':
							$err = 'AO02';
							break;
						case '3':
							$err = 'AO03';
							break;
						case '4':
							$err = 'AO04';
							break;
						case '5':
							$err = 'AO05';
							break;
						case '6':
							$err = 'AO06';
							break;
						case '7':
							$err = 'AO07';
							break;
						case '8':
							$err = 'AO08';
							break;
						case '10':
							$err = !$overlaping ? 'AO10' : 'AO11';
							break;
					}
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . @$_POST['next_action'] . "&tab=" . @$_POST['tab'] . "&err=$err");
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>
