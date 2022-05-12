
<?php

$calendarInfo = pjCalendarModel::factory()->find($_POST['calendar_id'])->getData();
$agrupamientoInfo = pjCalendarGroupModel::factory()->find($calendarInfo['id_agrupamiento'])->getData();

$usuServ = "SELECT id,id_usuario_operador FROM usuario_servicios WHERE id = ".$agrupamientoInfo['id_usuario_servicio'];
foreach ($conn->query($usuServ) as $row1) {
        $idUsuServicio = $row1['id'];
        $usuOpe = $row1['id_usuario_operador'];
    }

$queryOperador = "SELECT * FROM usuario_operadores WHERE id_usuario_op = ".$usuOpe;

foreach ($conn->query($queryOperador) as $row2) {
        $idUsuarioOperador = $row2['id_usuario_op']; 
        $nombreOperador = $row2['nombre_contacto_operador_1'];
        $empresaOperador = $row2['nombre_empresa_operador'];
        $direccionOperador = $row2['direccion_empresa_operador'];
        $telefonoOperador = $row2['telf_contacto_operador_1'];
        $correoOperador = $row2['email_contacto_operador'];
    }

$queryLogoOperador = "SELECT images.* FROM booking_abcalendar_calendars 
                    INNER JOIN images on images.id_auxiliar=booking_abcalendar_calendars.id_usuario_servicio 
                    WHERE booking_abcalendar_calendars.id = ".$_POST['calendar_id']." 
                    AND id_catalogo_fotografia=1 
                    AND estado_fotografia=1
                    AND profile_pic = 1";		        						 		

$arrayLogo = array();					 
foreach ($conn->query($queryLogoOperador) as $row3) {
        $arrayLogo[] = $row3;
    }		        			

if($_POST['status'] == 'Confirmed'){
$estadoReserva = 'Confirmado';
}elseif($_POST['status'] == 'Pending'){
$estadoReserva = 'Pendiente';
}elseif ($_POST['status'] == 'Cancelled') {
$estadoReserva = 'Cancelado';
}else{
$estadoReserva = 'Sin Definir';	
}


$correo = new PHPMailer(true);
$correo->SMTPDebug = 4;
$correo->Debugoutput = 'html';          	

$correo->Host = 'smtp.gmail.com';
$correo->SMTPAuth = true;
$correo->Username = "info@iwannatrip.com";
$correo->Password = "bcmnarkkibrxtfxd";
$correo->SMTPSecure = 'tls';
$correo->Port = 587;					
$correo->SetFrom('info@iwannatrip.com', "iWaNaTrip");
$asunto = "Confirmacion Reserva iWaNaTrip.com";
$correo->Subject = $asunto;

if(empty($arrayLogo)){
	$textodocumento = "<center><img src='".PJ_INSTALL_PATH."app/web/img/no-image-available.png'></center>";
}else{
	$imagen = $arrayLogo[0]['filename'];
	$textodocumento = "<center><img src='https://iwanatrip.com/public/images/icon/".$imagen."'></center>";
}						

$textodocumento .= "<h1>Informaci&oacute;n del Operador del Servicio</h1>";
$textodocumento .= "<p><b>Nombre del Tour:</b> ".$agrupamientoInfo['nombre']."</p>";
$textodocumento .= "<p><b>Detalle del Tour:</b> ". utf8_decode($agrupamientoInfo['descripcion'])."</p>";
$textodocumento .= "<p><b>Nombre del Operador:</b> ".$nombreOperador."</p>";
$textodocumento .= "<p><b>Empresa del Operador:</b> ".$empresaOperador."</p>";
$textodocumento .= "<p><b>Direeci&oacute;n del Operador:</b> ".$direccionOperador."</p>";
$textodocumento .= "<p><b>Tel&eacute;fono del Operador:</b> ".$telefonoOperador."</p>";
$textodocumento .= "<p><b>Correo del Operador:</b> ".$correoOperador."</p>";
$textodocumento .= "<br>";
$textodocumento .= "<h1>Informaci&oacute;n de la Reservaci&oacute;n:</h1>";
$textodocumento .= "<p><b>Nombre:</b> ".$_POST['c_name']."</p>";
$textodocumento .= "<p><b>Correo:</b> ".$_POST['c_email']."</p>";
$textodocumento .= "<p><b>Estado de la Reservaci&oacute;n:</b> ".$estadoReserva."</p>";
$textodocumento .= "<p><b>Reserva Desde:</b> ".$_POST['date_from']."</p>";
$textodocumento .= "<p><b>Reserva Hasta:</b> ".$_POST['date_to']."</p>";
$textodocumento .= "<p><b>Adultos:</b> ".$_POST['c_adults']."</p>";
$textodocumento .= "<p><b>Ni&ntilde;os:</b> ".$_POST['c_children']."</p>";
$textodocumento .= "<p><b>Monto de la Reservacion:</b> ".$_POST['amount']."</p>";
$textodocumento .= "<br>";
$textodocumento .= "<h1>Codigo de la Reservaci&oacute;n:</h1>";
$textodocumento .= "<center><img src='https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=".$_POST['uuid']."&choe=UTF-8' /></center>";
$correo->MsgHTML(utf8_decode("<br/>").$textodocumento);
$correo->AddAddress($_POST['c_email']);
if(isset($calendarInfo['correo_operador'])){
	$correo->AddAddress($calendarInfo['correo_operador'],'Operador del Servicio');
}

if(!$correo->Send()) {
    echo "Hubo un error al enviar correo " . $correo->ErrorInfo;
}else{
    echo "Envio Exitoso Correo";
    $correo->ClearAddresses();	
}