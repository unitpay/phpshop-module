<?

$SysValue = parse_ini_file("../../phpshop/inc/config.ini", 1);
while (list($section, $array) = each($SysValue))
    while (list($key, $value) = each($array))
        $SysValue['other'][chr(73) . chr(110) . chr(105) . ucfirst(strtolower($section)) . ucfirst(strtolower($key))] = $value;

function callback( $data, $SysValue )
{
    $method = '';
    $params = array();
    if ((isset($data['params'])) && (isset($data['method'])) && (isset($data['params']['signature']))) {
        $params = $data['params'];
        $method = $data['method'];
        $signature = $params['signature'];

        $secret_key = $SysValue['unitpay']['secret_key'];

        if (empty($signature)) {
            $status_sign = false;
        } else {
            $status_sign = verifySignature($params, $method, $secret_key);
        }
    } else {
        $status_sign = false;
    }
//    $status_sign = true;
    if ($status_sign) {
        switch ($method) {
            case 'check':
                $result = check($params, $SysValue);
                break;
            case 'pay':
                $result = pay($params, $SysValue);
                break;
            case 'error':
                $result = error($params, $SysValue);
                break;
            default:
                $result = array('error' =>
                    array('message' => 'неверный метод')
                );
                break;
        }
    } else {
        $result = array('error' =>
            array('message' => 'неверная сигнатура')
        );
    }
    hardReturnJson($result);
}
function check( $params, $SysValue )
{

        $link_db = mysqli_connect($SysValue['connect']['host'], $SysValue['connect']['user_db'], $SysValue['connect']['pass_db']);
        mysqli_select_db($link_db,$SysValue['connect']['dbase']);

        $sql = "select sum from " . $SysValue['base']['table_name1'] . " where uid=\"" . mysqli_real_escape_string($link_db, $params['account']) . "\" limit 1";
        $r = mysqli_query($link_db,$sql);
        $num = @mysqli_num_rows($r);

        if (!empty($num)) {

            $row = mysqli_fetch_row($r);
            $total = $row[0];

            //general setting id currency
            $sql = "select dengi from " . $SysValue['base']['table_name3'] . " limit 1";
            $r = mysqli_query($link_db,$sql);
            $row = mysqli_fetch_row($r);

            //iso code from currency id
            $sql = "select iso from " . $SysValue['base']['table_name24'] . " where id=\"" . $row[0] . "\" limit 1";
            $r = mysqli_query($link_db,$sql);
            $row = mysqli_fetch_row($r);

            $ISOCode = $row[0];

            if ((float)$total != (float)$params['orderSum']) {
                $result = array('error' =>
                    array('message' => 'не совпадает сумма заказа')
                );
            }elseif ($ISOCode != $params['orderCurrency']) {
                $result = array('error' =>
                    array('message' => 'не совпадает валюта заказа')
                );
            }
            else{

                $result = array('result' =>
                    array('message' => 'Запрос успешно обработан')
                );
            }

        } else {
            $result = array('error' =>
                array('message' => 'заказа не существует')
            );
        }


    return $result;
}
function pay( $params, $SysValue )
{
    $link_db = mysqli_connect($SysValue['connect']['host'], $SysValue['connect']['user_db'], $SysValue['connect']['pass_db']);
    mysqli_select_db($link_db,$SysValue['connect']['dbase']);

    $sql = "select sum from " . $SysValue['base']['table_name1'] . " where uid=\"" . mysqli_real_escape_string($link_db, $params['account']) . "\" limit 1";
    $r = mysqli_query($link_db,$sql);
    $num = @mysqli_num_rows($r);

    if (!empty($num)) {

        $row = mysqli_fetch_row($r);
        $total = $row[0];

        //general setting id currency
        $sql = "select dengi from " . $SysValue['base']['table_name3'] . " limit 1";
        $r = mysqli_query($link_db,$sql);
        $row = mysqli_fetch_row($r);

        //iso code from currency id
        $sql = "select iso from " . $SysValue['base']['table_name24'] . " where id=\"" . $row[0] . "\" limit 1";
        $r = mysqli_query($link_db,$sql);
        $row = mysqli_fetch_row($r);

        $ISOCode = $row[0];

        if ((float)$total != (float)$params['orderSum']) {
            $result = array('error' =>
                array('message' => 'не совпадает сумма заказа')
            );
        }elseif ($ISOCode != $params['orderCurrency']) {
            $result = array('error' =>
                array('message' => 'не совпадает валюта заказа')
            );
        }
        else{

            $arr = explode("-", $params['account']);
            $inv_id = $arr[0]."".$arr[1];

            $sql = "INSERT INTO " . $SysValue['base']['table_name33'] . " VALUES
            ($inv_id,'Unitpay','{$params['orderSum']}','" . date("U") . "')";
            $r = mysqli_query($link_db,$sql);


            $result = array('result' =>
                array('message' => 'Запрос успешно обработан')
            );
        }

    } else {
        $result = array('error' =>
            array('message' => 'заказа не существует')
        );
    }

    return $result;
}
function error( $params, $SysValue )
{
    $result = array('result' =>
        array('message' => 'Запрос успешно обработан')
    );
    return $result;
}
function getSignature($method, array $params, $secretKey)
{
    ksort($params);
    unset($params['sign']);
    unset($params['signature']);
    array_push($params, $secretKey);
    array_unshift($params, $method);
    return hash('sha256', join('{up}', $params));
}
function verifySignature($params, $method, $secret)
{
    return $params['signature'] == getSignature($method, $params, $secret);
}
function hardReturnJson( $arr )
{
    header('Content-Type: application/json');
    $result = json_encode($arr);
    die($result);
}

$data = $_GET;
callback($data, $SysValue);