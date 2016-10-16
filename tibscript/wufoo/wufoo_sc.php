<?php

/*


API Key: 8A7R-0RBV-KCM6-E9U3
1. n-
API ID 	Field Title
1 	ФИО
2 	Контактный телефон(ы)
26 	Поле заказа
3 	Город или населенный пункт
4 	Адрес доставки или паспортные данные
20 	Ваша электронная почта (email)
23 	Я буду оплачивать:
13 	Прикрепите реквизиты для выставления счета
7 	Комментарии к заказу
17 	clientID
18 	site
Hash 	z11vazuh15appf9


[Entries] => Array (
	[0] => Array (
		[EntryId] => 3109
		[Field1] => Осипов Владимир
		[Field2] => 8 917 5271 490
		[Field26] => РЕСАНТА Однофазный цифровой стабилизатор Ресанта СПН-14000 1 шт Артикул: R63-6-29 17000 всего 17000
		[Field3] => Москва
		[Field4] => АДРЕС, ул. Октября XX-X_X_XX
		[Field20] =>
		[Field23] => От юридического лица по безналичному расчету
		[Field13] => __57.doc (https://tiberis.wufoo.eu/cabinet/z11vazuh15appf9/GmqN6zajbeE%3D/__57.doc)
		[Field7] =>
		[Field17] => 623417594.1395067968
		[Field18] => resanta.tiberis.ru
		[DateCreated] => 2014-03-17 07:57:24
		[CreatedBy] => public
		[DateUpdated] =>
		[UpdatedBy] =>
	)
)



-----------------------------------------------------------------------------------
Новая разметка поля заказа, отправляемого с сайтов tiberis.ru и resanta.tiberis.ru
-----------------------------------------------------------------------------------

˵  - разделитель строк, т.е. разделитель позиций заказа.
¦  - разделитель наименования и кол-ва, артикула и цен


ПРИМЕР:

Было:
Сварочный инвертор KEMPPI MinarcTIG EVO 200MLP 2 шт Артикул: 61009200MLP 87880 всего 175760
Сварочный инвертор Ресанта САИ 250 1 шт Артикул: R65-6 8650 всего 8650

Стало:
Сварочный инвертор KEMPPI MinarcTIG EVO 200MLP ¦ 2 шт Артикул: 61009200MLP ¦ 87880 всего 175760˵
Сварочный инвертор Ресанта САИ 250 ¦ 1 шт Артикул: R65-6 ¦ 8650 всего 8650˵


-----------------------------------------------------------------------------------



Request Format
This API accepts GET requests in the following formats:

https://{subdomain}.wufoo.com/api/v3/forms/{formIdentifier}/entries.{xml|json}[?pretty=true]

    {subdomain} - This placeholder must be replaced with your subdomain.
    {formIdentifier} - This placeholder must be replaced with your URL or hash.
    {reportIdentifier} - This placeholder must be replaced with your URL or hash.
    {xml|json} - You must choose between xml or json
    pretty=true - This optional get parameter formats your output as HTML for debugging through the browser.


*/


// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------




// -----------------------------------------------------------------------------
// CONSTANT
// -----------------------------------------------------------------------------
$wufoo_server =				'https://tiberis.wufoo.eu';
$wufoo_API_KEY = 			'8A7R-0RBV-KCM6-E9U3';
$wufoo_api_uri =			'/api/v3';
$wufoo_useragent = 			'PHP-script (tiberis.ru) for Wufoo API v1b';
$wufoo_Form_ID = 			'1';
$HandShake_PHP =			'GGFH_im_from_1c-tiberis-ru__675893Uy8F4AS2230';

$NEW_entries_json = 		'new_entries.json';


// -----------------------------------------------------------------------------
// Взятие параметров URL
// -----------------------------------------------------------------------------
$ACTION 			=	isset($_REQUEST['action']) 		? trim($_REQUEST['action']) 	: null;
$Request_Entry 		=	isset($_REQUEST['entry']) 		? trim($_REQUEST['entry']) 		: null;
$HandShake_1C 		=	isset($_POST['handshake']) 		? trim($_POST['handshake']) 	: null;

$get_from_ip 		=	isset($_SERVER['REMOTE_ADDR']) 	? trim($_SERVER['REMOTE_ADDR']) : '127.0.0.1';

// -----------------------------------------------------------------------------

$STATUS = 'status:[ERROR no status yet]';


// -----------------------------------------------------------------------------
// Проверка безопасности
if ($HandShake_PHP !== $HandShake_1C) {
	echo "<strong>Carrots are given around the corner, on a different domain!</strong>"; // сообщение для тех, кто не прошёл нашу проверку безопасности.
	$STATUS = 'status:[ERROR no HandShake]';
}





// -----------------------------------------------------------------------------
// Основное условие
// -----------------------------------------------------------------------------
// if ($ACTION !== null) { // без учета ключа безопасности
if (($ACTION !== null) && ($HandShake_PHP == $HandShake_1C)) { // с учетом ключа безопасности


	// --------------------------------------------------------------------------------------------------------------------------
	if ($ACTION == 'get_from_wufoo') {  // -----------------------------------------------------------------[ GET From WUFOO]----
	// --------------------------------------------------------------------------------------------------------------------------
	// $ACTION == 'get_from_wufoo'  FOR TEST or FOR other
		// составляем URL
		$JSON_Get_Reports_URL = $wufoo_server.$wufoo_api_uri.'/forms/'.$wufoo_Form_ID.'/entries.json?Filter1=EntryId+Is_equal_to+'.$entry;

		// WUFOO auth
		$curl = curl_init($JSON_Get_Reports_URL);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERPWD, $wufoo_API_KEY.':footastic'); // Auth
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_USERAGENT, $wufoo_useragent);

		$response = curl_exec($curl);
		$resultStatus = curl_getinfo($curl);

		if($resultStatus['http_code'] == 200) { // Получен хороший ответ от сервера WUFOO и есть данные. Продолжаем работу
			$STATUS = 'status:[GET from Wufoo OK]';
			$Entry_array = json_decode($response, true); // преобразование в массив
			// print_r($Entry_array); //echo htmlentities($response);
			////////////////////////////////////////////////////////////////////
			///	Данный блок был предназначен для проведения серии тестов	 ///
			///	и при необходимости может быть использован во благо :Р		 ///
			////////////////////////////////////////////////////////////////////
		} else { // От сервера WUFOO получен фиговый ответ, явно есть какие-то проблемы.
			$STATUS = 'status:[GET from Wufoo ERROR]';
			echo $response;
		}

	// --------------------------------------------------------------------------------------------------------------------------
	} elseif ($ACTION == 'read') {  // ----------------------------------------------------------------[ READ From local JSON]---
	// --------------------------------------------------------------------------------------------------------------------------
		// Чтение json файла содержащего свежие заказы с Wufoo
		$NEW_entries_json_file = fopen($NEW_entries_json, 'r');
		if ( $NEW_entries_json_file ) {

			// читаем json из файла и распаковываем в удобный для работы массив
			$ENTRIES_data = json_decode(file_get_contents($NEW_entries_json), true);
			fclose($NEW_entries_json_file);

			// Меняем дату последнего обращения
			$ENTRIES_data["last_get"]["datetime"] = date('Y-m-d H:i:s', time());
			$ENTRIES_data["last_get"]["fromip"] = $get_from_ip;

			$SEND_ARRAY = array(); // создаем массив, который в последствии будем отправлять просящему
			$parser_array = array(); // вспомогательный массив для парсинга строки, содержащей позиции заказанного товара
			// перебираем все новые вхождения и формируем из них новый массив, который будет удобен для переваривания в 1С
			foreach ( $ENTRIES_data['new_entries'] as $entry_item ) {
				// исключения
				if ($entry_item["Field17"] == 'undefined') {
					$entry_item["Field17"] = '';
				}
				if ($entry_item["Field17"] == 'not_determined') {
					$entry_item["Field17"] = '';
				}
				// тафтология, знаю. но так wufoo-id отдельного заказа будет виднее. После того как 1С отчитается что заказ внесен, по этому EntryId будет сделан запрос, который уберет его из json-базы новых заказов, а при ошибке внесения в 1С отправит данные заказа на почту отделу продаж.
				$SEND_ARRAY[$entry_item["EntryId"]]["EntryId"] = $entry_item["EntryId"];	// id заказа по версии wufoo
				$SEND_ARRAY[$entry_item["EntryId"]]["clientID"] = $entry_item["Field17"];	// clientID
				$SEND_ARRAY[$entry_item["EntryId"]]["email"] = $entry_item["Field20"];		// мыльцо
				$SEND_ARRAY[$entry_item["EntryId"]]["fio"] = $entry_item["Field1"];			// ФИО
				$SEND_ARRAY[$entry_item["EntryId"]]["site"] = $entry_item["Field18"];		// с какого сайта заказ
				$SEND_ARRAY[$entry_item["EntryId"]]["tel"] = $entry_item["Field2"];			// телефон
				$SEND_ARRAY[$entry_item["EntryId"]]["adress"] = $entry_item["Field3"] . " " . $entry_item["Field4"]; // Адрес доставки
				$SEND_ARRAY[$entry_item["EntryId"]]["zakaz_text"] = $entry_item["Field26"];			// сырые позиции заказа
				$SEND_ARRAY[$entry_item["EntryId"]]["usercoment"] = $entry_item["Field7"];			// Комментарий покупателя

				$SEND_ARRAY[$entry_item["EntryId"]]["zakaz"] = array();						// подмассив для позиций заказа

				$parser_array[$entry_item["EntryId"]]["items"] = split('˵', $entry_item["Field26"]); // поле заказа, отправляем на парсинг
				$num = 0;
				foreach ( $parser_array[$entry_item["EntryId"]]["items"] as $zakaz_item ) {
					// echo $zakaz_item . "<br>***[".strlen($zakaz_item)."]***<br>"; // test
					$num = $num + 1; // считаем порядковые номера позиций
					if (strlen($zakaz_item) > 1) { // проверяем что тут действительно есть текст, который может представлять товар
						$SEND_ARRAY[$entry_item["EntryId"]]["zakaz"]["".$num.""]["kol"]   = zakaz_get_kol_from_string($zakaz_item);
						$SEND_ARRAY[$entry_item["EntryId"]]["zakaz"]["".$num.""]["sku"]   = zakaz_get_sku_from_string($zakaz_item);
						$SEND_ARRAY[$entry_item["EntryId"]]["zakaz"]["".$num.""]["price"] = zakaz_get_price_from_string($zakaz_item);
						$SEND_ARRAY[$entry_item["EntryId"]]["zakaz"]["".$num.""]["text"]  = $zakaz_item;
					}
				}
			}

			$SEND_JSON = json_encode($SEND_ARRAY); // формируем из полученного выше массива json
			echo $SEND_JSON; // выдаем на выход данные в формате JSON

			$ENTRIES_data_json = json_encode($ENTRIES_data); // запаковываем исходный массив обратно в json
			$NEW_entries_json_file = fopen($NEW_entries_json, 'w+');
			if ( $NEW_entries_json_file ) {
				fwrite($NEW_entries_json_file, $ENTRIES_data_json);
				fclose($NEW_entries_json_file);
				$STATUS = 'status:[Read]';
			} else { $STATUS = 'status:[Write json file ERROR]'; }



		} else { $STATUS = 'status:[Read json file ERROR]'; }


	// --------------------------------------------------------------------------------------------------------------------------
	} elseif ($ACTION == 'clear') {  // --------------------------------------------------------------[ CLEAR From local JSON]---
	// --------------------------------------------------------------------------------------------------------------------------
		// Чтение json файла содержащего свежие заказы с Wufoo
		$NEW_entries_json_file = fopen($NEW_entries_json, 'r');
		if ( $NEW_entries_json_file ) {

			// читаем json из файла
			$ENTRIES_data = json_decode(file_get_contents($NEW_entries_json), true);
			fclose($NEW_entries_json_file);

			// Меняем дату последнего обращения
			$ENTRIES_data["last_get"]["datetime"] = date('Y-m-d H:i:s', time());
			$ENTRIES_data["last_get"]["fromip"] = $get_from_ip;

			// Удаляем запись заказа из нашего JSON файла
			if( isset( $ENTRIES_data['new_entries'][$Request_Entry] ) ) {
				unset( $ENTRIES_data['new_entries'][$Request_Entry] );
			}

			$ENTRIES_data_json = json_encode($ENTRIES_data); // запаковываем исходный массив обратно в json
			$NEW_entries_json_file = fopen($NEW_entries_json, 'w+');
			if ( $NEW_entries_json_file ) {
				fwrite($NEW_entries_json_file, $ENTRIES_data_json);
				fclose($NEW_entries_json_file);
				$STATUS = 'status:[Clear]';
			} else { $STATUS = 'status:[Write json file ERROR]'; }



		} else { $STATUS = 'status:[Read json file ERROR]'; }

	} else $STATUS = 'status:[URL-param action ERROR]';

// --------------------------------------------------------------------------------------------------------------------------
} // endif ($ACTION !== null)
// --------------------------------------------------------------------------------------------------------------------------




// txt log
$fp = fopen('log_action_wufoo.txt', 'a');
fwrite($fp, "[".date('Y-m-d H:i:s', time())."] wufoo_sc.php | ".$STATUS." * post_from_ip:[".$get_from_ip."] Entry#".$Request_Entry."\n");
fclose($fp);




// -----------------------------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------------
// пример строки
// Сварочный инвертор KEMPPI MinarcTIG EVO 200MLP ¦ 2 шт Артикул: 61009200MLP ¦ 87880 всего 175760˵
// -----------------------------------------------------------------------------------------------------------

# Вычленение Количества единиц товара из строки
# @str - строка содержащая Артикул
function zakaz_get_kol_from_string($str) {
	$pars_a 	= split(' ¦ ', $str); // здесь нам нужна средняя часть, индекс 1
	$pars_aR 	= split(' шт ', $pars_a[1]);
	$item_kol 	= (int) $pars_aR[0]; // насильственно пробуем привести то что у нас получилось к целому числу.
	return $item_kol;
}

# Вычленение Артикула из строки
# @str - строка содержащая Артикул
function zakaz_get_sku_from_string($str) {
	$pars_a 	= split(' ¦ ', $str); // здесь нам нужна средняя часть, индекс 1
	$pars_aR 	= split('Артикул: ', $pars_a[1]);
	$item_sku 	= (string) $pars_aR[1]; // на всякий пожарный приводим насильственно к строке
	return $item_sku;
}

# Вычленение Стоимости товарной позиции из строки
# @str - строка содержащая Артикул
function zakaz_get_price_from_string($str) {
	$pars_a 	= split(' ¦ ', $str); // здесь нам нужна последняя часть, индекс 2
	$pars_aR 	= split(' всего ', $pars_a[2]);
	$item_price = (int) $pars_aR[0]; // насильственно пробуем привести то что у нас получилось к целому числу.
	return $item_price;
}






?>
