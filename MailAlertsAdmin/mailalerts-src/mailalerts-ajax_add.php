<?php

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/mailalerts.php');

if (!$id_product = intval(Tools::getValue('id_product')))
	die ('0');
$id_product_attribute = intval(Tools::getValue('id_product_attribute'));

if (!$cookie->isLogged())
{
	$id_customer = 0;
	$customer_email = Tools::getValue('customer_email');
	
	if (!Validate::isEmail($customer_email))
		die ('0');
	if ($customer_email == Tools::getValue('no_email'))
		die ('0');
	
	// Check if already in DB
	if (Db::getInstance()->ExecuteS('
	SELECT *
	FROM `'._DB_PREFIX_.'mailalert_customer_oos`
	WHERE `id_customer` = '.intval($id_customer).'
	AND `customer_email` = \''.pSQL($customer_email).'\'
	AND `id_product` = '.intval($id_product).'
	AND `id_product_attribute` = '.intval($id_product_attribute)))
    {

        $params = array(
            'id_customer' => $id_customer,
            'customer_email' => $customer_email,
            'id_product' => $id_product,
            'name_product' => Tools::getValue('name_product'),
            'id_product_attribute' => $id_product_attribute,
            'already_registered_alert' => 'Yes',
        );

        $mailalerts = new Mailalerts();
        $mailalerts->sendNewAlertEmails($params);

		die('1');
    }
}
else
{
	$id_customer = intval($cookie->id_customer);
	$customer_email = 0;
}

$params = array(
    'id_customer' => $id_customer,
    'customer_email' => $customer_email,
    'id_product' => $id_product,
    'name_product' => Tools::getValue('name_product'),
    'id_product_attribute' => $id_product_attribute,
    'already_registered_alert' => 'No',
);

$mailalerts = new Mailalerts();
$mailalerts->sendNewAlertEmails($params);

if (Db::getInstance()->Execute('
	REPLACE INTO `'._DB_PREFIX_.'mailalert_customer_oos` (`id_customer`, `customer_email`, `id_product` , `id_product_attribute`)
	VALUES ('.intval($id_customer).', \''.pSQL($customer_email).'\', '.intval($id_product).', '.intval($id_product_attribute).')'))
	die ('1');

die ('0');

?>
