<?php


if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));


// регистрационная информация
$domain = $SysValue['unitpay']['domain'];
$public_key = $SysValue['unitpay']['public_key'];
$secret_key = $SysValue['unitpay']['secret_key'];


// параметры магазина
/*$mrh_ouid = explode("-", $_POST['ouid']);
$inv_id = $mrh_ouid[0]."".$mrh_ouid[1];     //номер счета*/
$id = $_POST['ouid'];

// описание покупки
$inv_desc  = "PHPShopPaymentService";

// сумма покупки
$out_summ  = number_format($GLOBALS['SysValue']['other']['total'], 2, '.', '');

// код валюты в заказе
$mnt_currency = $GLOBALS['PHPShopSystem']->getDefaultValutaIso();

// библиотека корзины
$PHPShopCart = new PHPShopCart();

/**
 * Шаблон вывода таблицы корзины
 */
function cartpaymentdetails($val) {
     $dis=$val['uid']."  ".$val['name']." (".$val['num']." шт. * ".$val['price'].") -- ".$val['total']."
";

    return $dis;
}

// вывод HTML страницы с кнопкой для оплаты
$disp= '
<div align="center">
<p>
Платежи через сервис <b>Unitpay</b> – это хороший выбор.
</p>
 <p><br></p>
 
<form method="GET" name="pay" id="pay" action="https://' . $domain . '/pay/' . $public_key . '">
    <input type="hidden" name="account" value="'.$id.'">
    <input type="hidden" name="sum" value="'.$out_summ.'">
    <input type="hidden" name="currency" value="'.$mnt_currency.'">
    <input type=hidden name="desc" value="'.iconv('windows-1251','utf-8',$PHPShopCart->display('cartpaymentdetails')).'">
        <table><tr><td><img src="images/shop/icon-client-new.gif"  width="16" height="16" border="0" align="left"><a href="javascript:pay.submit();">Оплатить через платежную систему</a></td></tr></table>
</form>
</div>';

?>