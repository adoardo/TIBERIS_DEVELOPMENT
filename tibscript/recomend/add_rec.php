<?php

/*
Покупка
BUY
http://demo.easyrec.org:8080/api/1.0/buy?apikey=7a1666aa05df0a3e2736f4383a05760f&tenantid=EASYREC_DEMO&itemid=42&itemdescription=resanta-sai-140&itemurl=http://www.tiberis.ru//collections/svarochnyj-invertor/products/resanta-sai-140&itemimageurl=http://cdn.shopify.com/s/files/1/0048/1572/products/140_medium.jpg&sessionid=F3D4E3BE31EE3FA069F5434DB7EC2E34&itemtype=ITEM


modify
http://demo.easyrec.org:8080/api/1.0/profile/store?apikey=7a1666aa05df0a3e2736f4383a05760f&tenantid=EASYREC_DEMO&itemid=42&profile=%3Cprofile%3E%3Cfield%3Evalue%3C/field%3E%3C/profile%3E&


load price
http://demo.easyrec.org:8080/api/1.0/profile/field/load?apikey=7a1666aa05df0a3e2736f4383a05760f&tenantid=EASYREC_DEMO&itemid=42&field=/moremore/price


JSON GET
http://demo.easyrec.org:8080/api/1.0/json/otherusersalsoviewed?tenantid=EASYREC_DEMO&apikey=7a1666aa05df0a3e2736f4383a05760f&userid=321451034.1392040627&itemid=42&itemtype=ITEM&requesteditemtype=ITEM&actiontype=VIEW&callback=drawRec_toTiberis&numberOfResults=3


http://demo.easyrec.org:8080/api/1.0/json/otherusersalsoviewed?tenantid=www_tiberis_ru&apikey=7a1666aa05df0a3e2736f4383a05760f&userid=&itemid=14046&itemtype=ITEM&requesteditemtype=ITEM&actiontype=VIEW&callback=phpget&numberOfResults=1
http://demo.easyrec.org:8080/api/1.0/json/otherusersalsoviewed?tenantid=TIBERIS_RU_V22&apikey=7a1666aa05df0a3e2736f4383a05760f&userid=321451034.1392040627&itemid=090-005135-00502&itemtype=ITEM&requesteditemtype=ITEM&actiontype=VIEW&callback=drawRec_toTiberis&numberOfResults=3






Shopify GET Product
https://svarka.myshopify.com/admin/products/16992862.json?fields=id,title,handle,variants

*/

// взятие параметров URL
$ACTION 	= isset($_REQUEST['action']) ? trim($_REQUEST['action']) : null;
$itemid 	= isset($_REQUEST['itemid']) ? trim($_REQUEST['itemid']) : null;
$userid 	= isset($_REQUEST['userid']) ? trim($_REQUEST['userid']) : 'null';

$ITEM_description 	= isset($_REQUEST['description']) 	? trim($_REQUEST['description']) 	: null;
$ITEM_url 			= isset($_REQUEST['url']) 			? trim($_REQUEST['url']) 			: null;
$ITEM_imageurl 		= isset($_REQUEST['imageurl']) 		? trim($_REQUEST['imageurl']) 		: null;
$ITEM_itemtype 		= isset($_REQUEST['itemtype']) 		? trim($_REQUEST['itemtype']) 		: 'ITEM';
$ITEM_price 		= isset($_REQUEST['price']) 		? trim($_REQUEST['price']) 			: null;


$client_ip = isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '127.0.0.1';

	$rec_server =			'http://51.254.132.135:8080';
	$rec_rest_api_uri =		'/api/1.0/buy';
	$rec_json_api_uri =		'/api/1.0/json/otherusersalsoviewed';

	$rec_rest_update_api_uri =		'/api/1.0/profile/store';

	$apikey =				'7a1666aa05df0a3e2736f4383a05760f';
	$tenantid = 			'TIBERIS_RU_BUY';
	$itemtype = 			'ITEM';
	$requesteditemtype = 	'ITEM';
	$actiontype = 			'VIEW';
	$callback = 			'phpget';
	$numberOfResults = 		'1';

$STATUS = 'status:nostatus';


if ($ACTION !== null) { // --------------------------------------------------------------------------------------------------------------------------


	$JSONP_URL = 		$rec_server.$rec_json_api_uri.'?tenantid='.$tenantid.'&apikey='.$apikey.'&userid='.$userid.'&itemid='.$itemid.'&itemtype='.$itemtype.'&requesteditemtype='.$requesteditemtype.'&actiontype='.$actiontype.'&callback='.$callback.'&numberOfResults='.$numberOfResults;

	// ---[ CURL] --- get jsonp with ITEM_ID for itemurl & itemdescription
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

	curl_setopt($ch, CURLOPT_URL, $JSONP_URL);
	$result = substr(curl_exec($ch), 7, -1); // GET
	curl_close($ch);



	$JSONP_URL_array = json_decode($result, true); // преобразование в массив


	$JSONP_URL_array_GOOD = false;
	// Продолжаем, если нет ошибки (JSON распарсился)
	if (is_array($JSONP_URL_array) && $itemid != null) {
		// Перебираем ассоциативный масив для того, что бы убедится что там нет ключа 'error'. Который, в нашем случае скорее всего будет говорить о том, что запрашиваемого товара нет в базе, в этом случае нам не от куда взять его описание и URL, потому ничего и не заносим.
		while (list($key, $val) = each($JSONP_URL_array))
			if ($key == 'error') {
				$JSONP_URL_array_GOOD = false;
			} else {
				$JSONP_URL_array_GOOD = true;
			}
	}

	// --------------------------------------------------------------------------------------------------------------------------
	if ($JSONP_URL_array_GOOD == true && $ACTION == 'buy') {  // -----------------------------------------------------[ BUY ]----
	// --------------------------------------------------------------------------------------------------------------------------
		// Подбираем недостающие данные для оформления покупки
		$cur_ITEM_desc = 	 urlencode($JSONP_URL_array['baseitem']['description']);
		$cur_ITEM_url = 	 urlencode($JSONP_URL_array['baseitem']['url']);
		$cur_ITEM_img = 	 urlencode($JSONP_URL_array['baseitem']['imageUrl']);
		$cur_ITEM_type = 	 urlencode($JSONP_URL_array['baseitem']['itemType']);
		$cur_sessionid = 	 'F3D4E3BE31EE3FA069F5434DB7EC2E34';

		// составляем URL для обращения к базе
		$REST_BUY_URL = 		$rec_server.$rec_rest_api_uri.'?tenantid='.$tenantid.'&apikey='.$apikey.'&userid='.$userid.'&itemid='.$itemid.'&itemtype='.$itemtype.'&itemurl='.$cur_ITEM_url.'&itemimageurl='.$cur_ITEM_img.'&itemdescription='.$cur_ITEM_desc.'&sessionid='.$cur_sessionid;

		//echo "\n".$REST_URL."\n";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

		curl_setopt($ch, CURLOPT_URL, $REST_BUY_URL);
		$result = curl_exec($ch); // GET
		curl_close($ch);

		//echo "\n".$result."\n";
		$STATUS = 'status:buy-ok';
	// --------------------------------------------------------------------------------------------------------------------------
	} elseif ($ACTION == 'update') {  // ---------------------------------------------------------------------------[ UPDATE ]---
	// --------------------------------------------------------------------------------------------------------------------------
		/*
		// Подбираем недостающие данные
		$cur_ITEM_desc = 	 urlencode($ITEM_description);
		$cur_ITEM_url = 	 urlencode($ITEM_url);
		$cur_ITEM_img = 	 urlencode($ITEM_imageurl);
		$cur_ITEM_type = 	 urlencode($ITEM_itemtype);

		$profile = '<moremore><price>'.$ITEM_price.'</price></moremore>';

		$tenantid = 'EASYREC_DEMO'; // ************************************************** TODO TEST *********


		// составляем URL для обращения к базе
		$REST_UPDATE_URL = 		$rec_server.$rec_rest_update_api_uri.'?tenantid='.$tenantid.'&apikey='.$apikey.'&itemid='.$itemid.'&profile='.$profile;

		//echo "\n".$REST_URL."\n";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

		curl_setopt($ch, CURLOPT_URL, $REST_UPDATE_URL);
		$result = curl_exec($ch); // GET
		curl_close($ch);

		$STATUS = 'status:update-ok';
		*/
	} else $STATUS = 'status:error';

// --------------------------------------------------------------------------------------------------------------------------
} // endif ($ACTION !== null)
// --------------------------------------------------------------------------------------------------------------------------




// txt log
$fp = fopen('log_add.txt', 'a');
fwrite($fp, "[".date('Y-m-d H:i:s', time())."] ACTION:".$ACTION." ".$STATUS." * request from_ip:[".$client_ip."] SKU(itemid): ".$itemid." Client_ID: ".$userid."\n");
fclose($fp);











/*

-------------------------------------------
NORMAL OUTPUT
-------------------------------------------
array(5) {
  ["action"]=>
  string(20) "otherusersalsoviewed"
  ["baseitem"]=>
  array(7) {
    ["creationDate"]=>
    string(21) "2014-02-27 13:50:13.0"
    ["description"]=>
    string(65) "Сварочный инвертор Ресанта САИ 250ПН"
    ["imageUrl"]=>
    string(66) "http://cdn.shopify.com/s/files/1/0048/1572/products/253_medium.jpg"
    ["id"]=>
    string(9) "rsai250pn"
    ["itemType"]=>
    string(4) "ITEM"
    ["profileData"]=>
    array(1) {
      ["@nil"]=>
      string(4) "true"
    }
    ["url"]=>
    string(68) "http://www.tiberis.ru/collections/resanta/products/resanta-sai-250pn"
  }
  ["recommendeditems"]=>
  array(1) {
    ["item"]=>
    array(8) {
      ["creationDate"]=>
      string(21) "2014-02-27 13:04:05.0"
      ["description"]=>
      string(61) "Сварочный инвертор Ресанта САИ 250"
      ["imageUrl"]=>
      string(66) "http://cdn.shopify.com/s/files/1/0048/1572/products/251_medium.jpg"
      ["id"]=>
      string(7) "rsai250"
      ["itemType"]=>
      string(4) "ITEM"
      ["profileData"]=>
      array(1) {
        ["@nil"]=>
        string(4) "true"
      }
      ["url"]=>
      string(133) "http://demo.easyrec.org:8080/t?r=70906099&t=2303&f=70744875&i=70743293&a=1&u=http%3A%2F%2Fwww.tiberis.ru%2Fproducts%2Fresanta-sai-250"
      ["value"]=>
      string(16) "13.4146341463415"
    }
  }
  ["tenantid"]=>
  string(14) "www_tiberis_ru"
  ["userid"]=>
  string(21) "1131537058.1393783045"
}




-------------------------------------------
ERROR OUTPUT
-------------------------------------------
array(1) {
  ["error"]=>
  array(2) {
    ["@code"]=>
    string(3) "300"
    ["@message"]=>
    string(20) "Item does not exist!"
  }
}



*/





?>
