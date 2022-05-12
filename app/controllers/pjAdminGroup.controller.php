<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjAdminGroup extends pjAdmin
{
	/************************************************************************/
	/*    FUNCION PARA CREAR LOS AGRUPAMIENTOS DEL USUARIO SERVICIO     */
	/************************************************************************/
	public function pjActionCreate(){
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner()){

			if (isset($_POST['agrupamiento_create'])){
				$data = array();
        		$data['id_usuario_servicio'] = $_POST['id_usuario_servicio'];
				$data['nombre'] = $_POST['nombre'];
				$data['nombre_eng'] = $_POST['nombre_eng'];
				$data['descripcion'] = $_POST['descripcion'];
				$data['descripcion_eng'] = $_POST['descripcion_eng'];
				$data['tags'] = $_POST['tags'];
				$data['tags_eng'] = $_POST['tags_eng'];
				$data['key_words'] = $_POST['key_words'];
				$data['key_words_eng'] = $_POST['key_words_eng'];
				$data['orden'] = $_POST['orden'];				
				$data['estado'] = $_POST['estado'];
				$data['block_days'] = $_POST['block_days'];
				$data['lat'] = $_POST['lat'];
				$data['lng'] = $_POST['lng'];
				$data['instrucciones'] = $_POST['instrucciones'];
				$data['instrucciones_eng'] = $_POST['instrucciones_eng'];
				$data['createat'] = date("Y-m-d H:i:s");
				$data['updateat'] = date("Y-m-d H:i:s");
				// print_r($data);
				// die();
				$id = pjCalendarGroupModel::factory($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0){
					//********************************************************************************//
					//  SELECT Y UPDATE AL SEACHENGINE PARA EL; USUARIO SERVICIO SEARCH ENGINE     //
					//*********************************************************************************//
					$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
					if($conn->connect_errno){
					     echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
					}

					$search = $_POST['nombre']." ".$_POST['descripcion']." ".$_POST['tags'];
					$conn->query("INSERT INTO searchengine (id_usuario_servicio, search, estado_search, created_at, updated_at,tipo_busqueda) VALUES(".$id.",'".$search."', '1','".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."','7')");
					// INSERTO EN LA TABLA DE MANTENIMIENTO LOS DIAS BLOQUEADOS
					$conn->query("INSERT INTO booking_abcalendar_maintenance (after_day, id_agrupamiento) VALUES(".$data['block_days'].",".$id.")");
					$err = 'ACR01';
					
				} else {
					$err = 'ACR04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminGroup&action=pjActionIndex&err=$err");
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
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminGroup.js');
				$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			}
		} else {
			$this->set('status', 2);
		}
	}

	/******************************************************************************/
	/*         FUNCION QUE ACTUALIZA LOS DATOS DE UN AGRUPAMIENTO            */
	/******************************************************************************/
	public function pjActionUpdate(){
		$this->checkLogin();

		if ($this->isAdmin() || $this->isOwner() )
		{
			if (isset($_POST['options_update']))
			{

				if(!isset($_POST['activo'])){
					$activo = 0;
				}else{
					$activo = 1;
				}
				$pjUpdateCalendar = pjCalendarGroupModel::factory()->set('id', $this->getForeignId())->modify(
					array(
						'nombre' => $_POST['nombre'],
						'nombre_eng' => $_POST['nombre_eng'],
						'descripcion' => $_POST['descripcion'],
						'descripcion_eng' => $_POST['descripcion_eng'],
						'orden'=>$_POST['orden'],
						'estado' => $activo,
						'tags'=>$_POST['tags'],
						'tags_eng'=>$_POST['tags_eng'],
						'key_words'=>$_POST['key_words'],
						'key_words_eng'=>$_POST['key_words_eng'],
						'lat'=>$_POST['lat'],
						'lng'=>$_POST['lng'],
						'instrucciones'=>$_POST['instrucciones'],
						'instrucciones_eng'=>$_POST['instrucciones_eng'],
						'updateat'=>date("Y-m-d H:i:s")));
				/*ACTUALIZO EL SEARCH ENGINE*/
				$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
				if($conn->connect_errno){
				     echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
				}
				// ACTUALIZO LOS DIAS BLOQUEADOS PARA EL AGRUPAMIENTO
				$conn->query("UPDATE booking_abcalendar_maintenance SET after_day = ".$_POST['block_days']." WHERE id_agrupamiento = ".$this->getForeignId()." ");

				$searchEngine = "SELECT search FROM searchengine WHERE id_usuario_servicio = '".$this->getForeignId()."'  AND tipo_busqueda = 7";
				$conn->query($searchEngine);
				$search = "";
				foreach ($conn->query($searchEngine) as $row1) {
	        				$search = $row1['search'];
	        			}
	        			$search = $search." ".$_POST['nombre']." ".$_POST['descripcion']." ".$_POST['tags'];
	        			$updateSearchEngine = "UPDATE searchengine SET search = '$search'
    					WHERE id_usuario_servicio = '".$this->getForeignId()."' AND  tipo_busqueda = 7";
    				$conn->query($updateSearchEngine);

				$err ="ACR02";
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminGroup&action=pjActionIndex&err=$err");
			}
		} else {
			$this->set('status', 2);
		}
	}

	/******************************************************************************/
	/*    FUNCION PARA GUARDAR LAS FOTOS DE UN AGRUPAMIENTO                 */
	/******************************************************************************/
	public function pjActionFotos(){
		$this->checkLogin();
		if(!empty($_FILES)){
			
			//*****************************************************//
			//                SUBO EL ARCHIVO A LA CARPETA                     //
			//*****************************************************//
			$directorio = PJ_URL_UPLOAD.'uploads/';
			$directorio1 = PJ_URL_UPLOAD.'icon/'; 			
			$targetDir = $directorio;
			$id_auxiliar = trim($_POST['agrupamiento_id']);
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
				//           GUARDO EN LA BASE DE DATOS IMAGES       //
				//*****************************************************//
				$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
				if($mysqli->connect_errno){
				        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
				}
				$conn->query("INSERT INTO images (filename, original_name, created_at, updated_at, id_auxiliar, id_catalogo_fotografia, id_usuario_servicio, estado_fotografia)
					VALUES('".$fileName."','".$nombreFoto."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."',
						'".$id_auxiliar."', 11, ".$usuarioServ.", 1 )");

			}
		}
	}

	/******************************************************************************/
	/*    FUNCION QUE CARGA LAS FOTOS DE UN AGRUPAMIENTO                       */
	/******************************************************************************/
	public function pjActionCargarFotos(){

		$idCalendario = $_GET['agrupamiento'];
		$this->setAjax(true);
		//*****************************************************//
		//  BUSCO EN LA BASE LAS IMAGENES DEL CALENDARIO     //
		//*****************************************************//
		$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
		if($conn->connect_errno){
		 echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}

		$sql = "SELECT * FROM images WHERE id_auxiliar = $idCalendario AND estado_fotografia = 1 AND id_catalogo_fotografia = 11";
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

	/******************************************************************************/
	/*    FUNCION QUE BORRA FOTOS DE UN AGRUPAMIENTO                              */
	/******************************************************************************/
	public function pjActionDeleteFotos()	{
		$idFoto = $_GET['foto'];
		$this->setAjax(true);
		//*****************************************************//
		//  ELIMINO DE LA BD LA FOTO DEL CALENDARIO                //
		//*****************************************************//
		$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
		if($conn->connect_errno){
		 echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
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

	/******************************************************************************/
	/*		FUNCION QUE BORRA UN AGRUPAMIENTO                              */
	/******************************************************************************/
	public function pjActionDeleteCalendar(){
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$resp = array();
			if ( pjCalendarGroupModel::factory()->set('id', $_GET['id'])->modify(array('estado' => 0)) )
			{
				$resp['code'] = 200;
			} else {
				$resp['code'] = 100;
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}

	/******************************************************************************/
	/*		FUNCION QUE BORRA VARIOS AGRUPAMIENTOS                      */
	/******************************************************************************/
	public function pjActionDeleteCalendarBulk(){
		$this->setAjax(true);

		//if ($this->isXHR() && $this->isAdmin()){
		if ($this->isXHR()){
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjCalendarGroupModel::factory()->whereIn('id',$_POST['record'])
						   	   ->modifyAll(array('estado' => 0));
			}
		}
		exit;
	}

	// Metodo para Renderizar la lista de los agrupamientos //
	public function pjActionGetCalendar(){

		$this->setAjax(true);
		if ($this->isXHR() && $this->isAdmin())	{
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

			$column = 'nombre';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}


			$total = pjCalendarGroupModel::factory()->where('estado', 1)->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			$data = pjCalendarGroupModel::factory()->select('t1.id, t1.nombre as nombre, t1.descripcion as descripcion, t1.tags as tags, t1.estado as estado')->where('t1.estado', 1)->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));

		}elseif ($this->isXHR() && $this->isOwner()){
			/*PAGINACION DE LOS AGRUPAMIENTOS*/
			$pjCalendarModel = pjCalendarModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCalendar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjUser', "t3.id=t1.user_id", 'left')
				->where('t1.id_usuario_servicio', $_SESSION['usuario_servicio'])
				->where('t1.estado', 1);

			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjCalendarModel->escapeString($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), trim($q));
				$pjCalendarModel->where('t2.content LIKE', "%$q%");
			}

			$column = 'id';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = pjCalendarGroupModel::factory()
						->where('id_usuario_servicio',$_SESSION['usuario_servicio'])
						->where('estado',1)
						->findCount()->getData();

			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			/*	$data = pjCalendarGroupModel::factory()->select('t1.id, t1.nombre as nombre, t1.descripcion as descripcion, t1.tags as tags, t1.estado as estado')
				->where('id_usuario_servicio',$_SESSION['usuario_servicio'])
				->where('estado',1)
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();*/

			$data = pjCalendarGroupModel::factory()
					->select('t1.id, t1.nombre as nombre, t1.descripcion as descripcion, t1.descripcion_eng as descripcion_eng, t1.tags as tags, t1.estado as estado')
				->where('id_usuario_servicio',$_SESSION['usuario_servicio'])
				->where('estado', 1)
				->orderBy("$column $direction")
				->limit($rowCount, $offset)->findAll()->getData();

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	/******************************************************************************/
	/*FUNCION QUE LISTA TODOS LOS AGRUPAMIENTOS DEL USUARIO SERVICIO           */
	/******************************************************************************/
	public function pjActionIndex(){
		$this->checkLogin();
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminGroup.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
		elseif ($this->isOwner()){
			$_SESSION["comprobar"] = true;
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminGroupOwner.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionSaveCalendar(){
		$this->setAjax(true);

		if ($this->isXHR() && $this->isAdmin())
		{
			$pjCalendarModel = pjCalendarGroupModel::factory();
			if (!in_array($_POST['column'], $pjCalendarModel->getI18n()))
			{
				$pjCalendarModel->where('id', $_GET['id'])->limit(1)
						   ->modifyAll(array($_POST['column'] => $_POST['value']));
			}
		}
		exit;
	}

	private function __getCalendar($cid, $year, $month, $view=1){
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

	public function pjActionGetCal()	{
		$this->setAjax(true);

		if ($this->isXHR() && $this->isAdmin())
		{
			$this->__getCalendar($_GET['cid'], $_GET['year'], $_GET['month']);
		}
	}

	public function pjActionView(){
		$this->checkLogin();

		if ($this->isAdmin())
		{

			//****************************************************************//
			//  BUSCO EN LA BASE LAS IMAGENES DEL AGRUPAMIENTO     //
			//****************************************************************//
			$idCalendario = $_GET['id'];
			$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
			if($mysqli->connect_errno){
			 echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}
			$sql = "SELECT id,filename,descripcion_fotografia FROM images WHERE id_auxiliar = $idCalendario AND estado_fotografia = 1 AND id_catalogo_fotografia = 11";
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
			
			//****************************************************************//
			//  	OBTENGO LOS DIAS QUE SE BLOQUEAN PARA el AGRUPAMIENTO     //
			//****************************************************************//
			$idAGrupamiento = $_GET["id"];			
			$sqlDays = "SELECT after_day FROM booking_abcalendar_maintenance WHERE id_agrupamiento = $idAGrupamiento ORDER BY created_at DESC LIMIT 1";
			$sqlDaysRes = $conn->query($sqlDays);
			if(mysqli_num_rows($sqlDaysRes) > 0){
				foreach ($sqlDaysRes as $res) {
					$this->set('block_days', $res['after_day']);
				}
			}else{
				$this->set('block_days', 0);		
			}			
			//****************************************************************//
			//  	OBTENGO LOS DIAS QUE SE BLOQUEAN PARA el AGRUPAMIENTO     //
			//****************************************************************//			

			$this->__getCalendar($this->getForeignId(), date("Y"), date("n"));
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendCss('index.php?controller=pjFront&action=pjActionLoadCss&cid=' . $this->getForeignId() . '&' . rand(1,99999), PJ_INSTALL_URL, true);
			$this->appendJs('pjAdminGroupOwner.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			$this->set('arr', pjCalendarGroupModel::factory()->where('id',$_GET['id'])->orderBy('t1.nombre ASC')->findAll()->getData());
		}
		elseif ($this->isOwner())
		{

			//****************************************************************//
			//  BUSCO EN LA BASE LAS IMAGENES DEL AGRUPAMIENTO     //
			//****************************************************************//
			$idCalendario = $_GET['id'];
			$conn = new mysqli(PJ_DB_HOST, PJ_DB_USERNAME, PJ_DB_PASS, PJ_DB_NAME);
			if($mysqli->connect_errno){
			 echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}
			$sql = "SELECT id,filename,descripcion_fotografia FROM images WHERE id_auxiliar = $idCalendario AND estado_fotografia = 1 AND id_catalogo_fotografia = 11";
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

			//****************************************************************//
			//  	OBTENGO LOS DIAS QUE SE BLOQUEAN PARA el AGRUPAMIENTO     //
			//****************************************************************//
			$idAGrupamiento = $_GET["id"];			
			$sqlDays = "SELECT after_day FROM booking_abcalendar_maintenance WHERE id_agrupamiento = $idAGrupamiento ORDER BY created_at DESC LIMIT 1";
			$sqlDaysRes = $conn->query($sqlDays);
			if(mysqli_num_rows($sqlDaysRes) > 0){
				foreach ($sqlDaysRes as $res) {
					$this->set('block_days', $res['after_day']);
				}
			}else{
				$this->set('block_days', 0);		
			}			
			//****************************************************************//
			//  	OBTENGO LOS DIAS QUE SE BLOQUEAN PARA el AGRUPAMIENTO     //
			//****************************************************************//

			$this->__getCalendar($this->getForeignId(), date("Y"), date("n"));
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendCss('index.php?controller=pjFront&action=pjActionLoadCss&cid=' . $this->getForeignId() . '&' . rand(1,99999), PJ_INSTALL_URL, true);
			$this->appendJs('pjAdminGroupOwner.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			$this->set('arr', pjCalendarGroupModel::factory()->where('id',$_GET['id'])->where('id_usuario_servicio',$_SESSION['usuario_servicio'])->orderBy('t1.nombre ASC')->findAll()->getData());

		}
		 else {
			$this->set('status', 2);
		}
	}

	public function pjActionPrices()	{
		if ($this->option_arr['o_price_plugin'] == 'price' && pjObject::getPlugin('pjPrice') !== NULL)
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjPrice&action=pjActionIndex");
		} elseif ($this->option_arr['o_price_plugin'] == 'period' && pjObject::getPlugin('pjPeriod') !== NULL) {
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjPeriod&action=pjActionIndex");
		}
		exit;
	}

	public function pjActionGetReservation(){
		$results = print_r($_POST['record'], true);
	           $file = "/var/www/html/pruebaFacturas.txt";
		$open = fopen($file,"a");
		if ( $open ) {
		    fwrite($open,$results);
		    fclose($open);
		}
		$this->setAjax(true);
		exit;
	}


}
?>
