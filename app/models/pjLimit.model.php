<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLimitModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'limits';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'calendar_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'min_nights', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'max_nights', 'type' => 'tinyint', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjLimitModel($attr);
	}
}
?>