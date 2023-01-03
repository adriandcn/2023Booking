<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCalendars extends pjAdmin
{
	public function pjActionCreate(){
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner())
		{

			if (isset($_POST['calendar_create']))
			{

				$id = pjCalendarModel::factory($_POST)->insert()->getInsertId();

				if ($id !== false && (int) $id > 0){
					$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
					if($mysqli->connect_errno){
					        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
					}

					$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
						->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
						->where('t2.file IS NOT NULL')
						->orderBy('t1.sort ASC')->findAll()->getData();

					$this->models['Option']->init($id);
					$this->models['Option']->initConfirmation($id, $locale_arr);
					$err = 'ACR03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjCalendar');
					}

					$data = $this->models['Option']->reset()->getAllPairs($id);
					pjUtil::pjActionGenerateImages($id, $data);

					//************************************************************************//
					//          SELECT Y UPDATE AL SEACHENGINE PARA EL; USUARIO SERVICIO          //
					//************************************************************************//
					$nombreCalendario = $_POST['i18n'][2]['name'];
					$descripcion = $_POST['descripcion'];


					$search = $nombreCalendario." ".$descripcion;
					$conn->query("INSERT INTO searchengine (id_usuario_servicio, search, estado_search, created_at, updated_at,tipo_busqueda) VALUES(".$id.",'".$search."', '1','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."','8')");

					//*******************************************************************//
					//     UPDATE DE LOS DATOS PREDETERMINADOS DEL CALENDARIO  //
					//  		PAYPAL, AUTHORIZE, EMAIL, CURRENCY                  //
					//*******************************************************************//

					$pjOptionModel = pjOptionModel::factory();
					$ramdomKey = md5(uniqid(rand(1,6)));
					$url_Paypal = PJ_PAYPAL_URL.$ramdomKey;
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

	    				for($i = 0; $i < $contador; $i++){
	    					if($dataKey[$i] ==  "o_bf_lastname" ){
		    					$conn->query("INSERT INTO booking_abcalendar_options(foreign_id, `key`, tab_id, `value`, label, type, `order`, is_visible, style) VALUES($id,'o_bf_lastname','4','1|2|3::3','No|Yes|Yes (Required)','enum','1','1',NULL)");
	    					}elseif ($dataKey[$i] == "o_bf_cedula") {
	    						$conn->query("INSERT INTO booking_abcalendar_options(foreign_id, `key`, tab_id, `value`, label, type, `order`, is_visible, style) VALUES($id,'o_bf_cedula', '4','1|2|3::3', 'No|Yes|Yes (Required)','enum','2','1','NULL')");
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

				} else {
					$err = 'ACR04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCalendars&action=pjActionIndex&err=$err");
			} else {
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

				$this->set('user_arr', pjUserModel::factory()->orderBy('t1.name ASC')->findAll()->getData());
				$this->set('agrupamiento_arr', pjCalendarGroupModel::factory()->where('id_usuario_servicio',$_SESSION['usuario_servicio'])->orderBy('t1.nombre ASC')->findAll()->getData());

				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCalendars.js');
				$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			}
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionFotos(){

		$this->checkLogin();
		if(!empty($_FILES)){
			//*****************************************************//
			//                SUBO EL ARCHIVO A LA CARPETA                     //
			//*****************************************************//
			// $directorio = '/home/iwannatrip/public_html/BookiW/app/web/uploads/';
			// $directorio1 = '/home/iwannatrip/public_html/BookiW/app/web/icon/';
			$directorio = PJ_URL_UPLOAD.'uploads/';
			$directorio1 = PJ_URL_UPLOAD.'icon/';			
			$targetDir = $directorio;
			$id_auxiliar = trim($_POST['calendar_id']);
			$usuarioServ = trim($_POST['id_usuario_servicio']);
			$nombreFoto =  $_FILES['file']['name'];
			$fileName = $id_auxiliar.$_FILES['file']['name'];
			$fileName1 = "icon".$id_auxiliar.$_FILES['file']['name'];
			$targetFile = $targetDir.$fileName;
			$targetFileIcon = $targetDir.$fileName1;
			$targetFileIconDest = $directorio1.$fileName;

			if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){

				$imagen = new Imagick($targetFile);
				$imagen->thumbnailImage(300, 300);
				$imagen->writeImage($targetFileIcon);
				$source = $targetFileIcon;
				$dest = $targetFileIconDest;
				copy($source, $dest);
				unlink($source);

				//*****************************************************//
				//           GUARDO EN LA BASE DE DATOS IMAGES                //
				//*****************************************************//
				$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
				if($mysqli->connect_errno){
				        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
				}
				$conn->query("INSERT INTO images (filename, original_name, created_at, updated_at, id_auxiliar, id_catalogo_fotografia, id_usuario_servicio, estado_fotografia)
					VALUES('".$fileName."','".$nombreFoto."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."',
						'".$id_auxiliar."', 10, ".$usuarioServ.", 1 )");

			}
		}

	}

	public function pjActionCargarFotos(){

		$idCalendario = $_GET['calendar'];
		$this->setAjax(true);
		//*****************************************************//
		//  BUSCO EN LA BASE LAS IMAGENES DEL CALENDARIO     //
		//*****************************************************//
		$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
		if($mysqli->connect_errno){
		 echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}

		$sql = "SELECT * FROM images WHERE id_auxiliar = $idCalendario AND estado_fotografia = 1 AND id_catalogo_fotografia = 10";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			//$resp = $result->num_rows;
			$id = array();
			$filename = array();
		    	while($row = $result->fetch_assoc()) {
		             	$id[] = $row["id"];
		             	$filename[] = $row["filename"];
    			}
    			$resp = $filename;
		} else {
			$resp = $result->num_rows;
		}

		//$resp = $result ;
		pjAppController::jsonResponse($resp);
		exit;
	}

	public function pjActionDeleteFotos(){
		$idFoto = $_GET['foto'];
		$this->setAjax(true);
		//*****************************************************//
		//  ELIMINO DE LA BD LA FOTO DEL CALENDARIO                //
		//*****************************************************//
		$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
		if($mysqli->connect_errno){
		 echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}

		$sql = "UPDATE images SET estado_fotografia = 0  WHERE id = $idFoto ";

	    	if ($conn->query($sql) === TRUE) {
		             $resp = true;
		} else {
		            $resp = false;
		}
		pjAppController::jsonResponse($resp);
		exit;
	}

	public function pjActionDeleteCalendar(){
		$this->setAjax(true);

		if ($this->isXHR() && $this->isAdmin())
		{
			$resp = array();
			if (pjCalendarModel::factory()->where('id', $_GET['id'])->where('id !=', $this->getForeignId())->limit(1)->eraseAll()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjCalendar')->where('foreign_id', $_GET['id'])->eraseAll();
				pjOptionModel::factory()->where('foreign_id', $_GET['id'])->eraseAll();
				if (pjObject::getPlugin('pjPrice') !== NULL)
				{
					pjPriceModel::factory()->where('foreign_id', $_GET['id'])->eraseAll();
				}
				if (pjObject::getPlugin('pjPeriod') !== NULL)
				{
					$pjPeriodModel = pjPeriodModel::factory();
					$pid = $pjPeriodModel->where('foreign_id', $_GET['id'])->findAll()->getDataPair(null, 'id');
					if (!empty($pid))
					{
						$pjPeriodModel->eraseAll();
						pjPeriodPriceModel::factory()->whereIn('period_id', $pid)->eraseAll();
					}
				}
				$this->pjActionDeleteImages($_GET['id']);

				pjReservationModel::factory()->where('calendar_id', $_GET['id'])->eraseAll();
				$resp['code'] = 200;
			} else {
				$resp['code'] = 100;
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}

	private function pjActionDeleteImages($cid){
		$arr = array(
			PJ_UPLOAD_PATH . '%u_reserved_start.jpg',
			PJ_UPLOAD_PATH . '%u_reserved_end.jpg',
			PJ_UPLOAD_PATH . '%u_pending_pending.jpg',
			PJ_UPLOAD_PATH . '%u_reserved_pending.jpg',
			PJ_UPLOAD_PATH . '%u_pending_reserved.jpg',
			PJ_UPLOAD_PATH . '%u_reserved_reserved.jpg',
			PJ_UPLOAD_PATH . '%u_pending_start.jpg',
			PJ_UPLOAD_PATH . '%u_pending_end.jpg',
		);

		if (is_array($cid))
		{
			foreach ($cid as $id)
			{
				foreach ($arr as $img)
				{
					@unlink(sprintf($img, $id));
				}
			}
		} else {
			foreach ($arr as $img)
			{
				@unlink(sprintf($img, $cid));
			}
		}
	}

	public function pjActionDeleteCalendarBulk(){
		$this->setAjax(true);

		if ($this->isXHR() && $this->isAdmin())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjCalendarModel::factory()->whereIn('id', $_POST['record'])->where('id !=', $this->getForeignId())->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjCalendar')->whereIn('foreign_id', $_POST['record'])->where('foreign_id !=', $this->getForeignId())->eraseAll();
				pjOptionModel::factory()->whereIn('foreign_id', $_POST['record'])->where('foreign_id !=', $this->getForeignId())->eraseAll();
				if (pjObject::getPlugin('pjPrice') !== NULL)
				{
					pjPriceModel::factory()->whereIn('foreign_id', $_POST['record'])->where('foreign_id !=', $this->getForeignId())->eraseAll();
				}
				if (pjObject::getPlugin('pjPeriod') !== NULL)
				{
					$pjPeriodModel = pjPeriodModel::factory();
					$pid = $pjPeriodModel->whereIn('foreign_id', $_POST['record'])
						->where('foreign_id !=', $this->getForeignId())
						->findAll()->getDataPair(null, 'id');
					if (!empty($pid))
					{
						$pjPeriodModel->eraseAll();
						pjPeriodPriceModel::factory()->whereIn('period_id', $pid)->eraseAll();
					}
				}
				$this->pjActionDeleteImages($_POST['record']);
				pjReservationModel::factory()->whereIn('calendar_id', $_POST['record'])->where('calendar_id !=', $this->getForeignId())->eraseAll();
			}
		}
		exit;
	}

	public function pjActionGetCalendar(){
		$this->setAjax(true);

		if ($this->isXHR() && $this->isAdmin())
		{
			$pjCalendarModel = pjCalendarModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCalendar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjUser', "t3.id=t1.user_id", 'left');
				//->where('t1.id_usuario_servicio', $_SESSION['usuario_servicio']);


			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjCalendarModel->escapeString($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjCalendarModel->where('t2.content LIKE', "%$q%");
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjCalendarModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjCalendarModel->select('t1.id, t1.user_id, t2.content AS name, t3.name AS user_name')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		elseif ($this->isXHR() && $this->isOwner())
		{

			$pjCalendarModel = pjCalendarModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCalendar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjUser', "t3.id=t1.user_id", 'left')
				->where('t1.id_usuario_servicio', $_SESSION['usuario_servicio']);
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjCalendarModel->escapeString($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjCalendarModel->where('t2.content LIKE', "%$q%");
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjCalendarModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjCalendarModel->select('t1.id, t1.user_id, t2.content AS name, t3.name AS user_name')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCalendars.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
		elseif ($this->isOwner()){
			$_SESSION["comprobar"] = true;
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCalendarsOwner.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionSaveCalendar()
	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isAdmin())
		{
			$pjCalendarModel = pjCalendarModel::factory();
			if (!in_array($_POST['column'], $pjCalendarModel->getI18n()))
			{
				$pjCalendarModel->where('id', $_GET['id'])->limit(1)
						   ->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjCalendar');
			}
		}
		exit;
	}

	private function __getCalendar($cid, $year, $month, $view=1)
	{

		print_r($cid);
		print_r($year);
		print_r($month);
	
		
		$ABCalendar = new pjABCalendar();
		$ABCalendar
			->setShowNextLink((int) $view > 1 ? false : true)
			->setShowPrevLink((int) $view > 1 ? false : true)
			->setPrevLink("")
			->setNextLink("")
			->set('calendarId', $cid)
			->set('reservationsInfo', pjReservationModel::factory()
				->getInfo(
					$cid,
					date("Y-m-d", mktime(0, 0, 0, $month, 1, $year)),
					date("Y-m-d", mktime(23, 59, 59, $month + $view, 0, $year)),
					$this->option_arr, NULL,
					1
				)
			)
			->set('options', $this->option_arr)
			->set('weekNumbers', (int) $this->option_arr['o_show_week_numbers'] === 1 ? true : false)
			->setStartDay($this->option_arr['o_week_start'])
			->setDayNames(__('day_names', true))
			->setMonthNames(__('months', true))
		;

		dd($ABCalendar);
		if (pjObject::getPlugin('pjPeriod') !== NULL && $this->option_arr['o_price_plugin'] == 'period')
		{
			$ABCalendar->set('periods', pjPeriodModel::factory()->getPeriodsPerDay($cid, $month, $year, $view, $this->option_arr['o_price_based_on'] == 'days'));
		}
		if ((int) $this->option_arr['o_show_prices'] === 1)
		{
			if (pjObject::getPlugin('pjPrice') !== NULL && $this->option_arr['o_price_plugin'] == 'price')
			{
				$price_arr = pjPriceModel::factory()->getPricePerDay(
					$cid,
					date("Y-m-d", mktime(0, 0, 0, $month, 1, $year)),
					date("Y-m-d", mktime(0, 0, 0, $month + $view, 1, $year)),
					$this->option_arr
				);
				$ABCalendar
					->set('prices', $price_arr['priceData'])
					->set('showPrices', true);
			}
		}

		$this->set('ABCalendar', $ABCalendar);
	}

	public function pjActionGetCal(){
		$this->setAjax(true);
		//if ($this->isXHR() && $this->isAdmin())
		if ($this->isXHR())
		{
			$this->__getCalendar($_GET['cid'], $_GET['year'], $_GET['month']);
		}
	}

	public function pjActionView()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				if ((int) pjCalendarModel::factory()->where('t1.id', $_GET['id'])->findCount()->getData() !== 1)
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCalendars&action=pjActionIndex");
				}
				$this->setForeignId($_GET['id']);
			}

			$this->__getCalendar($this->getForeignId(), date("Y"), date("n"));

			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendCss('index.php?controller=pjFront&action=pjActionLoadCss&cid=' . $this->getForeignId() . '&' . rand(1,99999), PJ_INSTALL_URL, true);
			$this->appendJs('pjAdminCalendars.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
		elseif ($this->isOwner())
		{

			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				if ((int) pjCalendarModel::factory()->where('t1.id', $_GET['id'])->findCount()->getData() !== 1)
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCalendars&action=pjActionIndex");
				}
				$this->setForeignId($_GET['id']);
			}

			$this->__getCalendar($this->getForeignId(), date("Y"), date("n"));
			print_r($_GET['id']);
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendCss('index.php?controller=pjFront&action=pjActionLoadCss&cid=' . $this->getForeignId() . '&' . rand(1,99999), PJ_INSTALL_URL, true);
			$this->appendJs('pjAdminCalendars.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
		 else {
			$this->set('status', 2);
		}

	}

	public function pjActionPrices()
	{
		if ($this->option_arr['o_price_plugin'] == 'price' && pjObject::getPlugin('pjPrice') !== NULL)
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjPrice&action=pjActionIndex");
		} elseif ($this->option_arr['o_price_plugin'] == 'period' && pjObject::getPlugin('pjPeriod') !== NULL) {
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjPeriod&action=pjActionIndex");
		}
		exit;
	}
}
?>
