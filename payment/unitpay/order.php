<?php
//header('Content-Type: text/html; charset=utf-8');

PHPShopObj::loadClass('delivery');
if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));


$PHPShopSystem = new PHPShopSystem();
$ndsEnabled = $PHPShopSystem->getParam('nds_enabled');
$nds = $PHPShopSystem->getParam('nds');
$deliveryNds = $this->PHPShopDelivery->getParam('ofd_nds');

$domain = $SysValue['unitpay']['domain'];
$public_key = $SysValue['unitpay']['public_key'];
$secret_key = $SysValue['unitpay']['secret_key'];

$id = $_POST['ouid'];

$inv_desc  = "PHPShopPaymentService";

$out_summ  = number_format($GLOBALS['SysValue']['other']['total'], 2, '.', '');

$mnt_currency = $GLOBALS['PHPShopSystem']->getDefaultValutaIso();

$PHPShopCart = new PHPShopCart();

//$deliveryPrice = $this->PHPShopDelivery->getPrice($out_summ);
$deliveryPrice = $this->delivery;

$items = array();

$total = $deliveryPrice;

foreach($PHPShopCart->_CART as $item) {
	if($this->discount > 0)
		$price = $item['price']  - ($product['price']  * $this->discount  / 100);
	else
		$price = $item['price'];
				
	$items[] = array(
		'name' => iconv("Windows-1251", "UTF-8", $item['name']),
		'count' => $item["num"],
		'price' => number_format($price, 2, '.', ''),
		'currency' => $mnt_currency,
		'nds' => $ndsEnabled ? getTaxRates($nds) : "none",
		'type' => 'commodity'
	);
	
	$total = number_format($total + (int) $item['num'] * $price, 2, '.', '');
}

if($deliveryPrice > 0) {
	$items[] = array(
		'name' => 'Доставка',
		'count' => 1,
		'price' => number_format($deliveryPrice, 2, '.', ''),
		'currency' => $mnt_currency,
		'nds' => getTaxRates($deliveryNds),
		'type' => 'service'
	);
}

$cartItems = base64_encode(json_encode($items));

$desc = "Оплата заказа № ".$id;

$signature = hash('sha256', join('{up}', array(
	$id,
	$mnt_currency,
	$desc,
	$out_summ ,
	$secret_key
)));

function getTaxRates($rate){
	switch (intval($rate)){
		case 10:
			$vat = 'vat10';
			break;
		case 20:
			$vat = 'vat20';
			break;
		case 0:
			$vat = 'vat0';
			break;
		default:
			$vat = 'none';
	}

	return $vat;
}

$this->set('pay_url', 'https://' . $domain . '/pay/' . $public_key);
$this->set('account', $id);
$this->set('sum', $out_summ);
$this->set('signature', $signature);
$this->set('currency', $mnt_currency);
$this->set('customerEmail', $_POST["mail"]);
$this->set('customerPhone', (isset($_POST["tel_new"]) && $_POST["tel_new"] != "" ? preg_replace('/\D/', '', $_POST["tel_new"]) : ""));
$this->set('desc', $desc);
$this->set('cartItems', $cartItems);

$disp=ParseTemplateReturn(__DIR__ . "/payment_forma.tpl", true);
?>