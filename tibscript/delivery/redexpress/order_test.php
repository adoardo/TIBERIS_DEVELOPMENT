<?php

// -----------------------------------------------------------------------------
// CONSTANT
// -----------------------------------------------------------------------------
$HandShake_PHP =			'GGFH_im_from_1c-tiberis-ru__675893Uy8F4AS2230';

$get_from_ip 		=	isset($_SERVER['REMOTE_ADDR']) 	? trim($_SERVER['REMOTE_ADDR']) : '127.0.0.1';

// -----------------------------------------------------------------------------
// Взятие параметров URL
// -----------------------------------------------------------------------------
$HandShake_1C 		=	isset($_POST['handshake']) 		? trim($_POST['handshake']) 	: null;
$json_data 		    =	isset($_POST['json_data']) 		? trim($_POST['json_data']) 	: null;
$ACTION 			=	isset($_REQUEST['action']) 		? trim($_REQUEST['action']) 	: null;
$org				= 	isset($_REQUEST['org']) 		? trim($_REQUEST['org']) 	: null;

$redURL = "https://redexpressservice.ru:9082/rxwbm";
//$redURL = "http://redexpressservice.ru:9081/rxwbm";

// -----------------------------------------------------------------------------

$STATUS = 'status:[ERROR no status yet]';
$red_invoice = "n/a"; // for log
$num1C = "n/a"; // for log
$request_action = $ACTION; // for log




$respond_data = array();
$respond_data["error"] = "Unknown error!";
$respond_data["invoice"] = "";


// 000000002 - Тиберис (логин\пас 100162)
// 000000005 - СК Тиберис (логин\пас 100152)
if ($org == '000000002') {
	$loginname = "100162";
	$passwname = "100162";
} elseif ($org == '000000005') {
	$loginname = "100152";
	$passwname = "100152";
} else {
	$loginname = "noname";
	$respond_data["error"] = "ERROR! Данная Организация не учтена в скрипте. Сейчас поддерживаются [ООО Тиберис] и [ООО СК Тиберис]";
	$STATUS = 'status:[ERROR wrong organization id '.$org.']';
}



// -----------------------------------------------------------------------------
// --------------- TEST  TEST  TEST  TEST  TEST  TEST  TEST  TEST  TEST  TEST
// -----------------------------------------------------------------------------
// Тест ... тестовая база

			$loginname = "100152";
			$passwname = "100152";

            $invoiceNumber = "100511321";

//			$redURL = "https://redexpressservice.ru:9082/rxwbm2";

// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------



// -----------------------------------------------------------------------------
// Основное условие
// -----------------------------------------------------------------------------



        // Создаем клента SOAP сервиса
        $client = new SoapClient( $redURL . "/wbmanage.asmx?WSDL" );

        // Входим в систему
        $args = new stdClass();
        $args->userName = $loginname;
        $args->password = $passwname;
        $res = $client->LogonTkn($args);

        if($res->LogonTknResult != "Success")
        {
            //echo $res->LogonTknResult;
			$respond_data["error"] = $res->LogonTknResult;
			$STATUS = 'status:[red LogonTkn ERROR: '.$respond_data["error"].']';
			write_text_log($request_action,$STATUS,$get_from_ip,$num1C,$red_invoice);
			echo json_encode($respond_data);
            exit;
        }
        // Токен, который будет использован в дальнейшем
        $token = $res->token;










        $args = new stdClass();
        $args->token = $token;
        $args->numberInvoice = $invoiceNumber;
        $res = $client->DeleteInvoiceTkn($args);

        if($res->DeleteInvoiceTkn != "Success")
        {
            $error = GetLastError($client,$token);
            //echo $error->errorText;
			$respond_data["error"] = $error->errorText;
			$STATUS = 'status:[red DeleteInvoiceTkn ERROR: '.$respond_data["error"].']';
        }
        else
        {
            $respond_data["error"] = "-"; // без ошибок
			$args = new stdClass();
            $args->token = $token;
            $args->number = $invoice->Number;
            $res = $client->DeleteInvoiceTkn($args);

			$red_invoice = $respond_data["invoice"]; // for log
			$STATUS = 'status:[OK]';

        }


        // Выход из системы
        $args->token = $token;
        $client->LogoutTkn($args);



echo $STATUS;


function write_text_log($request_action,$STATUS,$get_from_ip,$num1C,$red_invoice) {
	// txt log
	$fp = fopen('log_action.txt', 'a');
	fwrite($fp, "[".date('Y-m-d H:i:s', time())."] REDEXPRESS order.php | Action: ".$request_action.", ".$STATUS." * post_from_ip:[".$get_from_ip."] 1C: ".$num1C." red_invoice: ".$red_invoice."\n");
	fclose($fp);
}



// Функция получения кода ошибки
function getLastError($client, $token)
{
    $args = new stdClass();
    $args->token = $token;
    $res = $client->GetLastErrorTkn($args);

    return $res;
}





/*


object(stdClass)#12 (2) {
  ["GetInvoiceInfoResult"]=>
  string(7) "Success"
  ["invoiceInfo"]=>
  object(stdClass)#13 (26) {
    ["Number"]=>
    string(9) "100464177"
    ["ExistInvoice"]=>
    bool(false)
    ["NumberOrder"]=>
    string(17) "БЛОГ778787878"
    ["Barcodes"]=>
    object(stdClass)#14 (0) {
    }
    ["Consigner"]=>
    object(stdClass)#15 (2) {
      ["CompanyInfo"]=>
      object(stdClass)#16 (4) {
        ["IdClient"]=>
        string(4) "test"
        ["Organization"]=>
        string(29) "Тестовый клиент"
        ["ContactName"]=>
        string(18) "Петров И.И"
        ["Phone"]=>
        string(13) "+791600000000"
      }
      ["Address"]=>
      object(stdClass)#17 (7) {
        ["MailIndex"]=>
        string(6) "107077"
        ["Country"]=>
        string(12) "РОССИЯ"
        ["TypeRegion"]=>
        string(2) "г"
        ["Region"]=>
        string(12) "Москва"
        ["TypeStreet"]=>
        string(4) "ул"
        ["Street"]=>
        string(22) "15-я Парковая"
        ["House"]=>
        string(5) "25к1"
      }
    }
    ["Consignee"]=>
    object(stdClass)#18 (2) {
      ["CompanyInfo"]=>
      object(stdClass)#19 (2) {
        ["ContactName"]=>
        string(23) "Сидор Иванов"
        ["Phone"]=>
        string(12) "+79260000001"
      }
      ["Address"]=>
      object(stdClass)#20 (7) {
        ["Country"]=>
        string(12) "РОССИЯ"
        ["TypeRegion"]=>
        string(2) "г"
        ["Region"]=>
        string(12) "Москва"
        ["TypeStreet"]=>
        string(9) "пр-кт"
        ["Street"]=>
        string(24) "Измайловский"
        ["House"]=>
        string(2) "57"
        ["Apartament"]=>
        string(1) "4"
      }
    }
    ["ContentType"]=>
    string(6) "Parcel"
    ["DeliveryStart"]=>
    string(19) "2014-04-21T08:30:00"
    ["DeliveryEnd"]=>
    string(19) "2014-04-21T20:10:00"
    ["DeliveryDate"]=>
    string(10) "2014-04-21"
    ["DeliveryStartTime"]=>
    string(22) "08:30:00.0000000+04:00"
    ["DeliveryEndTime"]=>
    string(22) "20:10:00.0000000+04:00"
    ["ServiceType"]=>
    int(69)
    ["PriceType"]=>
    string(3) "SEP"
    ["Price"]=>
    float(0)
    ["PriceShop"]=>
    float(4.5)
    ["PriceShopIsCashless"]=>
    bool(false)
    ["PriceShopValuta"]=>
    string(3) "RUB"
    ["PriceService"]=>
    float(0)
    ["PriceServiceIsCashless"]=>
    bool(false)
    ["PriceServiceValuta"]=>
    string(3) "RUB"
    ["InsurancePrice"]=>
    float(0)
    ["Description"]=>
    string(115) "Карта памяти (вес 100 гр.) - 1 шт; Композитный кабель (вес 200 гр.)- 2 шт"
    ["Comment"]=>
    string(71) "Просьба осуществить доставку после 12:00"
    ["CargoPlaces"]=>
    object(stdClass)#21 (1) {
      ["CargoPlaceInfo"]=>
      object(stdClass)#22 (5) {
        ["Number"]=>
        string(13) "P000000297977"
        ["Weight"]=>
        float(1.2)
        ["L"]=>
        float(0.2)
        ["H"]=>
        float(0.2)
        ["W"]=>
        float(0.05)
      }
    }
    ["CargoPlacesDescription"]=>
    object(stdClass)#23 (0) {
    }
  }
}




*/

?>
