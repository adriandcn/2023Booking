<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjInvoiceConfigModel extends pjInvoiceAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_invoice_config';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'y_logo', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'y_country', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'y_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'y_phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'y_fax', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'y_email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'y_url', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'p_accept_payments', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'p_accept_paypal', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'p_accept_authorize', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'p_accept_creditcard', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'p_accept_cash', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'p_accept_bank', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'p_authorize_tz', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'p_authorize_key', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'p_authorize_mid', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'p_authorize_hash', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'si_include', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_shipping_address', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_company', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_name', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_address', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_street_address', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_city', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_state', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_zip', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_phone', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_fax', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_email', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_url', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_date', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_terms', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_is_shipped', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'si_shipping', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'o_booking_url', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'o_qty_is_int', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'o_use_qty_unit_price', 'type' => 'tinyint', 'default' => 1)
	);
	
	protected $i18n = array('y_company', 'y_name', 'y_street_address', 'y_city', 'y_state', 'y_template', 'p_paypal_address', 'p_bank_account');
	
	public static function factory($attr=array())
	{
		return new pjInvoiceConfigModel($attr);
	}
	
	public function getConfigData($locale_id, $pk=1)
	{
		$arr = $this->find($pk)->getData();
		
		$i18n = pjMultiLangModel::factory()
			->where('t1.model', 'pjInvoiceConfig')
			->where('t1.foreign_id', $pk)
			->where('t1.locale', $locale_id)
			->whereIn('t1.field', $this->getI18n())
			->findAll()
			->getDataPair('field', 'content');
		
		return array_merge($arr, $i18n);
	}
}
?>