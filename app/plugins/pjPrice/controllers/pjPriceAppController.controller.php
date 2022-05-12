<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPriceAppController extends pjPlugin
{
	public function __construct()
	{
		$this->setLayout('pjActionAdmin');
	}
	
	public static function getConst($const)
	{
		$registry = pjRegistry::getInstance();
		$store = $registry->get('pjPrice');
		return isset($store[$const]) ? $store[$const] : NULL;
	}
	
	public function isPriceReady()
	{
		$reflector = new ReflectionClass('pjPlugin');
		try {
			//Try to find out 'isPriceReady' into parent class
			$ReflectionMethod = $reflector->getMethod('isPriceReady');
			return $ReflectionMethod->invoke(new pjPlugin(), 'isPriceReady');
		} catch (ReflectionException $e) {
			//echo $e->getMessage();
			//If failed to find it out, denied access, or not :)
			return false;
		}
	}
	
	public function getForeignId()
	{
		$reflector = new ReflectionClass('pjPlugin');
		try {
			//Try to find out 'getForeignId' into parent class
			$ReflectionMethod = $reflector->getMethod('getForeignIdPrice');
			return $ReflectionMethod->invoke(new pjPlugin(), 'getForeignIdPrice');
		} catch (ReflectionException $e) {
			//echo $e->getMessage();
			//If failed to find it out, denied access, or not :)
			return parent::getForeignId();
		}
	}
}
?>