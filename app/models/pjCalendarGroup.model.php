<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCalendarGroupModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'agrupamiento';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'id_usuario_servicio', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'nombre', 'type' => 'varchar', 'default' => ':NULL'),		
		array('name' => 'nombre_eng', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'descripcion', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'descripcion_eng', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'tags', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'tags_eng', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'estado', 'type' => 'boolean', 'default' => ':NULL'),
		array('name' => 'orden', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'key_words', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'key_words_eng', 'type' => 'varchar', 'default' => ':NULL'),		
		array('name' => 'lat', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'lng', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'instrucciones', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'instrucciones_eng', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'createat', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'updateat', 'type' => 'datetime', 'default' => ':NOW()')
	);

	public static function factory($attr=array())
	{
		return new pjCalendarGroupModel($attr);
	}
}
?>
