<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFotosModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'fotos';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'id_usuario_servicio', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'file_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'uploaded', 'type' => 'datetime', 'default' => ':NOW()')

	);

	public static function factory($attr=array())
	{
		return new pjFotosModel($attr);
	}
}
?>