<?

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));

if(isset($_GET['account'])){
$order_metod="Unitpay";
$success_function=true; // Выключаем функцию обновления статуса заказа
$my_crc = "NoN";
$crc = "NoN";
$inv_id = $_GET['account'];
}
?>
