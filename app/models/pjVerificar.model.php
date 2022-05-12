<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVerificarModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'verificacion_bookings';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'id_usuario', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'id_usuario_servicio', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'consumido', 'type' => 'boolean', 'default' => ':NULL'),
		//array('name' => 'uuid', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'uuid', 'type' => 'blob', 'default' => ':NULL'),
		array('name' => 'created_at', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'updated_at', 'type' => 'datetime', 'default' => ':NOW()')


	);

	public static function factory($attr=array())
	{
		return new pjVerificarModel($attr);
	}
}
?>