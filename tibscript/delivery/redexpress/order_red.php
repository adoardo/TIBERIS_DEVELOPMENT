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

$redURL = "http://redexpressservice.ru:9081/rxwbm/";
//$redURL = "https://redexpressservice.ru:9082/rxwbm";
//$redURL = "http://redexpressservice.ru:9081/rxwbm";

// -----------------------------------------------------------------------------

$STATUS = 'status:[ERROR no status yet]';
$red_invoice = "n/a"; // for log
$num1C = "n/a"; // for log
$request_action = $ACTION; // for log


// -----------------------------------------------------------------------------
// Проверка безопасности
if ($HandShake_PHP !== $HandShake_1C) {
	echo "<strong>Carrots are given around the corner, on a different domain!</strong>"; // сообщение для тех, кто не прошёл нашу проверку безопасности.
	$STATUS = 'status:[ERROR no HandShake]';
}


$respond_data = array();
$respond_data["error"] = "Unknown error!";
$respond_data["invoice"] = "";


// 000000002 - Тиберис (логин\пас 100162)
// 000000005 - СК Тиберис (логин\пас 100152)
// 000000006 - ИП Зарецкая А.А. (логин\пас 100238)
if ($org == '000000002') {
	$loginname = "100162";
	$passwname = "aDDask3Aj5^";
} elseif ($org == '000000005') {
	$loginname = "100152";
	$passwname = "sKdjfuA7@23";
} elseif ($org == '000000006') {
	$loginname = "100238";
	$passwname = "a:kfiasdu53";
} else {
	$loginname = "noname";
	$respond_data["error"] = "ERROR! Данная Организация не учтена в скрипте. Сейчас поддерживаются [ООО Тиберис], [ООО СК Тиберис] и [ИП Зарецкая А.А.]";
	$STATUS = 'status:[ERROR wrong organization id '.$org.']';
}



// -----------------------------------------------------------------------------
// --------------- TEST  TEST  TEST  TEST  TEST  TEST  TEST  TEST  TEST  TEST
// -----------------------------------------------------------------------------
// Тест ... тестовая база

			//$loginname = "testClient";
			//$passwname = "P@ssw0rd!";
			//$redURL = "https://redexpressservice.ru:9082/rxwbm2";

// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------



// -----------------------------------------------------------------------------
// Основное условие
// -----------------------------------------------------------------------------
// if ($ACTION !== null) { // без учета ключа безопасности
if (($ACTION !== null) && ($loginname !== "noname") && ($HandShake_PHP == $HandShake_1C)) { // с учетом ключа безопасности


    $INVOICE_data = json_decode($json_data, true);


	// for debug write last send data
	$fp = fopen('dump_action_1c.txt', 'w+');
		fwrite($fp, json_encode($INVOICE_data));
	fclose($fp);


    if (!is_array($INVOICE_data)) {
        $ACTION = 'error!';
		$respond_data["error"] = "Data from 1C invalid!";
		$STATUS = 'status:[ERROR Data from 1C invalid!]';
    }

	// --------------------------------------------------------------------------------------------------------------------------
	if ($ACTION == 'get_invoice') {  // -----------------------------------------------------------------------[ GET Invoice]----
	// --------------------------------------------------------------------------------------------------------------------------

		$num1C = $INVOICE_data["NumberOrder"]; // for log

		if($INVOICE_data['BoxAmount'] < 1) {
			$respond_data["error"] = "Кол-во коробок не может быть меньше одной!";
			$STATUS = 'status:[1c ERROR: Пришедший BoxAmount (кол-во коробок): '.$INVOICE_data['BoxAmount'].' меньше 1 ]';
			write_text_log($request_action,$STATUS,$get_from_ip,$num1C,$red_invoice);
			echo json_encode($respond_data);
			exit;
		}


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

        // Получаем номер грузоместа
		// -------------------------------
		// -------------- Для нескольких мест
		$cargoNumber = array();
		for ($numerator = 0; $numerator < $INVOICE_data['BoxAmount']; $numerator++) {
			$args = new stdClass();
			$args->token = $token;
			$res = $client->GetCargoPlaceNumberTkn($args);
			if($res->GetCargoPlaceNumberTknResult != "Success")
			{
				//echo $res->GetCargoPlaceNumberTknResult;
				$respond_data["error"] = $res->GetCargoPlaceNumberTknResult;
				$STATUS = 'status:[red ERROR GetCargoPlaceNumberTkn['.$numerator.']: '.$respond_data["error"].']';
				write_text_log($request_action,$STATUS,$get_from_ip,$num1C,$red_invoice);
				echo json_encode($respond_data);
				exit;
			}

			$cargoNumber[$numerator] = $res->number;
		}

		//$cargoNumber = array();
		//$numerator = 0;
		//foreach ( $INVOICE_data['CargoPlaces'] as $entry_item ) {
		//	$args = new stdClass();
		//	$args->token = $token;
		//	$res = $client->GetCargoPlaceNumberTkn($args);
		//	if($res->GetCargoPlaceNumberTknResult != "Success")
		//	{
		//		//echo $res->GetCargoPlaceNumberTknResult;
		//		$respond_data["error"] = $res->GetCargoPlaceNumberTknResult;
		//		$STATUS = 'status:[red ERROR: '.$respond_data["error"].']';
		//		write_text_log($request_action,$STATUS,$get_from_ip,$num1C,$red_invoice);
		//		echo json_encode($respond_data);
		//		exit;
		//	}
		//
		//	$cargoNumber[$numerator] = $res->number;
		//	$numerator = $numerator + 1;
		//}



		// -------------------------------
		// -------------- Для одного места
		//$args = new stdClass();
		//$args->token = $token;
		//$res = $client->GetCargoPlaceNumberTkn($args);
		//if($res->GetCargoPlaceNumberTknResult != "Success")
		//{
		//	//echo $res->GetCargoPlaceNumberTknResult;
		//	$respond_data["error"] = $res->GetCargoPlaceNumberTknResult;
		//	$STATUS = 'status:[red GetCargoPlaceNumberTkn ERROR: '.$respond_data["error"].']';
		//	write_text_log($request_action,$STATUS,$get_from_ip,$num1C,$red_invoice);
		//	echo json_encode($respond_data);
		//	exit;
		//}
		//
		//$cargoNumber = $res->number;



        // Номер накладной
        $args->token = $token;
        $res = $client->GetInvoiceNumberTkn($args);
        if($res->GetInvoiceNumberTknResult != "Success")
        {
            //echo $res->GetInvoiceNumberTknResult;
			$respond_data["error"] = $res->GetInvoiceNumberTknResult;
			$STATUS = 'status:[red GetInvoiceNumberTknResult ERROR: '.$respond_data["error"].']';
			write_text_log($request_action,$STATUS,$get_from_ip,$num1C,$red_invoice);
			echo json_encode($respond_data);
            exit;
        }

        $invoiceNumber = $res->number;





        // Создаем новую накладную
        $invoice = new stdClass();
        $invoice->Number = $invoiceNumber;
        $invoice->ExistInvoice = false;
        $invoice->NumberOrder = $INVOICE_data["NumberOrder"]; // Номер заказа в вашей системе

        // ========= Грузоотправитель ===========================
        // Информация о отправителе
        $invoice->Consigner = new stdClass();
        //$invoice->Consigner->CompanyInfo = new stdClass();
        //$invoice->Consigner->CompanyInfo->IdClient =  "test"; // $loginname; // Ваш номер клиентского договора с компанией Red
		$invoice->Consigner->CompanyInfo->IdClient =  $loginname; // Ваш номер клиентского договора с компанией Red
        $invoice->Consigner->CompanyInfo->Organization = $INVOICE_data["Consigner"]["CompanyInfo"]["Organization"];
        $invoice->Consigner->CompanyInfo->ContactName = $INVOICE_data["Consigner"]["CompanyInfo"]["ContactName"];
        $invoice->Consigner->CompanyInfo->Phone = 		$INVOICE_data["Consigner"]["CompanyInfo"]["Phone"];

        // Адрес отправителя
        $invoice->Consigner->Address = new stdClass();
        $invoice->Consigner->Address->MailIndex = 		$INVOICE_data["Consigner"]["Address"]["MailIndex"];
        $invoice->Consigner->Address->Country = 		$INVOICE_data["Consigner"]["Address"]["Country"];
        $invoice->Consigner->Address->TypeRegion = 		$INVOICE_data["Consigner"]["Address"]["TypeRegion"];
        $invoice->Consigner->Address->Region = 			$INVOICE_data["Consigner"]["Address"]["Region"];
        $invoice->Consigner->Address->TypeStreet = 		$INVOICE_data["Consigner"]["Address"]["TypeStreet"];
        $invoice->Consigner->Address->Street = 			$INVOICE_data["Consigner"]["Address"]["Street"];
        $invoice->Consigner->Address->House = 			$INVOICE_data["Consigner"]["Address"]["House"];
		$invoice->Consigner->Address->Apartament = 		$INVOICE_data["Consigner"]["Address"]["Apartament"];

        // ========= Грузополучатель ==========================
        // Информация о получателе
        $invoice->Consignee = new stdClass();
        $invoice->Consignee->CompanyInfo = new stdClass();
		$invoice->Consignee->CompanyInfo->Organization = $INVOICE_data["Consignee"]["CompanyInfo"]["Organization"];
        $invoice->Consignee->CompanyInfo->ContactName = $INVOICE_data["Consignee"]["CompanyInfo"]["ContactName"];
        $invoice->Consignee->CompanyInfo->Phone = 		$INVOICE_data["Consignee"]["CompanyInfo"]["Phone"];

        // Адрес получателя
        $invoice->Consignee->Address = new stdClass();
        $invoice->Consignee->Address->Country = 		$INVOICE_data["Consignee"]["Address"]["Country"];
        $invoice->Consignee->Address->MailIndex = 		$INVOICE_data["Consignee"]["Address"]["MailIndex"];
		$invoice->Consignee->Address->TypeRegion = 		$INVOICE_data["Consignee"]["Address"]["TypeRegion"];
        $invoice->Consignee->Address->Region = 			$INVOICE_data["Consignee"]["Address"]["Region"];
		$invoice->Consignee->Address->TypeSubRegion = 	$INVOICE_data["Consignee"]["Address"]["TypeSubRegion"];
		$invoice->Consignee->Address->SubRegion = 		$INVOICE_data["Consignee"]["Address"]["SubRegion"];
		$invoice->Consignee->Address->TypeCity = 		$INVOICE_data["Consignee"]["Address"]["TypeCity"];
		$invoice->Consignee->Address->City = 			$INVOICE_data["Consignee"]["Address"]["City"];
		$invoice->Consignee->Address->TypePlace = 		$INVOICE_data["Consignee"]["Address"]["TypePlace"];
		$invoice->Consignee->Address->Place = 			$INVOICE_data["Consignee"]["Address"]["Place"];
        $invoice->Consignee->Address->TypeStreet = 		$INVOICE_data["Consignee"]["Address"]["TypeStreet"];
        $invoice->Consignee->Address->Street = 			$INVOICE_data["Consignee"]["Address"]["Street"];
        $invoice->Consignee->Address->House = 			$INVOICE_data["Consignee"]["Address"]["House"];
        $invoice->Consignee->Address->Apartament = 		$INVOICE_data["Consignee"]["Address"]["Apartament"];





        $invoice->ContentType = 						$INVOICE_data["ContentType"];
        $invoice->DeliveryDate = 						$INVOICE_data["DeliveryDate"];
        $invoice->DeliveryStartTime = 					$INVOICE_data["DeliveryStartTime"];
        $invoice->DeliveryEndTime = 					$INVOICE_data["DeliveryEndTime"];

        // Тип услуги указывается как код симовола

			// Тип услуги указывается как код симовола
			//D - Экспресс доставка документов за границу      		(код 68)
			//E - Экспресс-18 доставка до 18:00 (внутри России) 	(код 69)
			//F - Экспресс доставка флаеров за границу              (код 70)
			//M - Самовывоз-MultiFoto								(код 77)
			//P - Экспресс доставка посылок за границу              (код 80)
			//R - Возврат-18                                        (код 82)
			//S - Экспресс-00 в тот же день (внутри России)         (код 83)
			//T - Экспресс-12, доставка до 12:00 (внутри России)    (код 84)
			//U - Экспресс-RCV                                      (код 85)
			//V - Самовывоз  										(код 86)
			//W - Экспресс-ID, идентификация клиента                (код 87)
			//Z - Забор-18                                          (код 90)
			// ----------------------------------------------
			// ВАЖНО! должен быть передан именно код символа
			// к примеру коды описаны тут http://jquery.page2page.ru/index.php5/Коды_символов_и_клавиш
        $invoice->ServiceType = 						$INVOICE_data["ServiceType"]; // код услуги 'E', см справочник
		//$invoice->ServiceType = 						69; // код услуги 'E', см справочник

			//CARD	Оплата по кредитной карте отправителем при отправке груза
			//CASH	Оплата наличными отправителем при отправке груза
			//CODCARD Оплата по кредитной карте получателем при доставке груза
			//CODCASH Оплата наличными получателем при доставке груза
			//SEP		Оплата по счёту отправителем
			//COP		Оплата по счёту получателем
		$invoice->PriceType = 							$INVOICE_data["PriceType"];
        $invoice->PriceService = 						$INVOICE_data["PriceService"]; // Заполняется если у вас стоимость доставки отдельно от товара
        $invoice->PriceServiceIsCashless = 				$INVOICE_data["PriceServiceIsCashless"];
        $invoice->PriceServiceValuta = 					$INVOICE_data["PriceServiceValuta"];
        $invoice->PriceShop = 							$INVOICE_data["PriceShop"]; // Стоимость товара, может быть стоимость товара + стоимость доставки в вашем магазине, если стоимость 0.0 то был безналичный расчет
        $invoice->PriceShopIsCashless = 				$INVOICE_data["PriceShopIsCashless"];
        $invoice->PriceShopValuta = 					$INVOICE_data["PriceShopValuta"];


		//Стоимость доставки в интернет магазине.
		if ($INVOICE_data["PriceService"] > 0 ) {
			$invoice->PriceService = 						$INVOICE_data["PriceService"];
			// bool PriceServiceIsCashless [get, set]
			// true если оплата услуг доставки произошла безналично
			$invoice->PriceServiceIsCashless = 				$INVOICE_data["PriceServiceIsCashless"];
			$invoice->PriceServiceValuta = 					$INVOICE_data["PriceServiceValuta"];
		}


        // Не используйте, просто значения по умолчанию, почему то php не работает без заполнения
        $invoice->Price = 0.0;
        $invoice->InsurancePrice = 0.0;
        $invoice->ExtServicesDeliveryPersHands = false;
        $invoice->ExtServicesDeliverySaturday = false;
        $invoice->ExtServicesDeliveryAfterWorking = false;
        $invoice->ExtServicesDeliveryAddPackaging = false;
        // ----------------------------------------------------------

        // Описания и коментарий
        $invoice->Description = 						$INVOICE_data["Description"];
        $invoice->Comment = 							$INVOICE_data["Comment"];

        // Грузоместа
		// ----------------------------------------------------------------
		//<CargoPlaceInfo>
		//	<Number>string</Number>
		//	<Weight>double</Weight>
		//	<L>double</L>
		//	<H>double</H>
		//	<W>double</W>
		//</CargoPlaceInfo>
//// ----------------------------------------------- Для нескольких мест


		$invoice->CargoPlaces = array();
		for ($numerator = 0; $numerator < $INVOICE_data['BoxAmount']; $numerator++) {
			$invoice->CargoPlaces[$numerator] = new stdClass();
			$invoice->CargoPlaces[$numerator]->Number = 		$cargoNumber[$numerator];
			// Вес коробки, необязательно
			$invoice->CargoPlaces[$numerator]->Weight = 		0;
			// Размеры коробки, необзязательно
			$invoice->CargoPlaces[$numerator]->L = 				0;
			$invoice->CargoPlaces[$numerator]->H = 				0;
			$invoice->CargoPlaces[$numerator]->W = 				0;

		}


////        $invoice->CargoPlaces = array();
////		$numerator = 0;
////		foreach ( $INVOICE_data['CargoPlaces'] as $entry_item ) {
////			$invoice->CargoPlaces[$numerator] = new stdClass();
////			$invoice->CargoPlaces[$numerator]->Number = 		$cargoNumber[$numerator];
////			// Вес коробки, необязательно
////			// !!!!! Перемножать на кол-во товаров в заказе ---------------------------------------!!!!!!!!!!!!!!!!!! TODO:
////			$invoice->CargoPlaces[$numerator]->Weight = 		$entry_item["Weight"];
////			//$invoice->CargoPlaces[$numerator]->Weight = 		"";// $entry_item["Weight"];
////			// Размеры коробки, необзязательно
////			$invoice->CargoPlaces[$numerator]->L = 				$entry_item["L"];
////			$invoice->CargoPlaces[$numerator]->H = 				$entry_item["H"];
////			$invoice->CargoPlaces[$numerator]->W = 				$entry_item["W"];
////
////			$numerator = $numerator + 1;
////		}

//// ----------------------------------------------- Для одного места

//		$commonWeight = 0;
// 		foreach ( $INVOICE_data['CargoPlaces'] as $entry_item ) {
//			//$eachPositionWeight = $entry_item["Weight"] * $entry_item["Quantity"];
//			$eachPositionWeight = strtr($entry_item["Weight"], ',', '.') * strtr($entry_item["Quantity"], ',', '.');
//
//			$commonWeight = $commonWeight + $eachPositionWeight;
//		}
//
//
//        $invoice->CargoPlaces = array();
//		$invoice->CargoPlaces[0] = new stdClass();
//		$invoice->CargoPlaces[0]->Number = 		$cargoNumber;
//		// Вес коробки, необязательно
//		// !!!!! Перемножать на кол-во товаров в заказе ---------------------------------------!!!!!!!!!!!!!!!!!! TODO:
//		$invoice->CargoPlaces[0]->Weight = 		$commonWeight;
//		//$invoice->CargoPlaces[$numerator]->Weight = 		"";// $entry_item["Weight"];
//		// Размеры коробки, необзязательно
//		$invoice->CargoPlaces[0]->L = 				$entry_item["L"];
//		$invoice->CargoPlaces[0]->H = 				$entry_item["H"];
//		$invoice->CargoPlaces[0]->W = 				$entry_item["W"];




/*

		//<CargoPlaceDescription>
		//	<Description>string</Description>
		//	<Composition>string</Composition>
		//	<Quantity>int</Quantity>
		//	<Weight>double</Weight>
		//	<Manufacturer>string</Manufacturer>
		//	<Price>double</Price>
		//	<PriceValuta>string</PriceValuta>
		//	<IdNumNomenclature>string</IdNumNomenclature>
		//</CargoPlaceDescription>
		$invoice->CargoPlacesDescription = array();
		$numerator = 0;
		foreach ( $INVOICE_data['CargoPlaces'] as $entry_item ) {
			$invoice->CargoPlacesDescription[$numerator] = new stdClass();
			$invoice->CargoPlacesDescription[$numerator]->Description = 		$entry_item["Description"];
			$invoice->CargoPlacesDescription[$numerator]->Weight = 				$entry_item["Weight"];
			$invoice->CargoPlacesDescription[$numerator]->Quantity = 			$entry_item["Quantity"];
			$invoice->CargoPlacesDescription[$numerator]->Price = 				$entry_item["Price"];
			$invoice->CargoPlacesDescription[$numerator]->PriceValuta = 		$entry_item["PriceValuta"];
			$invoice->CargoPlacesDescription[$numerator]->IdNumNomenclature = 	$entry_item["IdNumNomenclature"];

			$numerator = $numerator + 1;
		}
*/



        $invoice->NeedBankCardTerminal = false;





		// for debug write last send data
		$fp = fopen('dump_action.txt', 'w+');
			fwrite($fp, json_encode($invoice));
		fclose($fp);




		// -------------------------------------------------------------------
        // Создаем накладную в системе
        $args = new stdClass();
        $args->token = $token;
        $args->invoice = $invoice;
        $res = $client->CreateInvoiceTkn($args);

        if($res->CreateInvoiceTknResult != "Success")
        {
            $error = GetLastError($client,$token);
            //echo $error->errorText;
			$respond_data["error"] = $error->errorText;
			$STATUS = 'status:[red CreateInvoiceTkn ERROR: '.$respond_data["error"].']';
        }
        else
        {
            $respond_data["error"] = "-"; // без ошибок
			$args = new stdClass();
            $args->token = $token;
            $args->number = $invoice->Number;
            $res = $client->GetInvoiceInfo($args);

            //echo $args->number; // test
			$respond_data["invoice"] = $res->invoiceInfo->Number;
			$red_invoice = $respond_data["invoice"]; // for log
			$STATUS = 'status:[OK]';

        }


        // Выход из системы
        $args->token = $token;
        $client->LogoutTkn($args);



	// --------------------------------------------------------------------------------------------------------------------------
	} elseif ($ACTION == 'delete_invoice') {  // -----------------------------------------------------------[ DELETE Invoice]----
	// --------------------------------------------------------------------------------------------------------------------------

		$num1C = 			$INVOICE_data["NumberOrder"]; // for log
		$invoiceNumber = 	$INVOICE_data["InvoiceNumber"];
		$red_invoice = 		$invoiceNumber;


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

		//ResultCode.Success 			успешно выполнено
		//ResultCode.InvalidId 			накладная с данным номером не найден
		//ResultCode.InvalidOperation 	накладная с данным номером находится в обработке, удаление не произведено
		//ResultCode.UserNotLogged 		клиент не выполнил вход в систему
		//ResultCode.InternalError 		внутренняя ошибка сервиса
		//ResultCode.NullParameter_0 	numberInvoice равен null или пустой строке.



        if($res->DeleteInvoiceTkn != "Success")
        {
            $error = GetLastError($client,$token);
            //echo $error->errorText;
			$respond_data["error"] = $error->errorText;
			if ($error->errorText = "Операция успешно выполнена.") {
				$respond_data["error"] = "-"; // без ошибок
				$STATUS = 'status:[OK]';
			} else {
				$STATUS = 'status:[red DeleteInvoiceTkn ERROR: '.$respond_data["error"].']';
			}
        }
        else
        {
            $respond_data["error"] = "-"; // без ошибок
			$STATUS = 'status:[OK]';

        }


        // Выход из системы
        $args->token = $token;
        $client->LogoutTkn($args);


	} else $STATUS = 'status:[URL-param action ERROR]';

// --------------------------------------------------------------------------------------------------------------------------
} // endif ($ACTION !== null)
// --------------------------------------------------------------------------------------------------------------------------



echo json_encode($respond_data);

write_text_log($request_action,$STATUS,$get_from_ip,$num1C,$red_invoice);









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
