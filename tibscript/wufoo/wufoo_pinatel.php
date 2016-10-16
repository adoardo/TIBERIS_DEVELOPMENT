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


Данные поля приходят при срабатывании WEB-HOOKS из Wufoo
--------------------------------------------------------------------------------
Field1			=>		DCH Тестович Дмитрий
Field2			=>		1234 1234 1234
Field26			=>		Сварочный инвертор KEMPPI MinarcTIG EVO 200MLP 1 шт Артикул: 61009200MLP 87880 всего 87880
Сварочный инвертор Ресанта САИ 250 1 шт Артикул: R65-6 8650 всего 8650

Field3			=>		Гомель
Field4			=>		Заугольная, 25Б 3-й этаж
Field20			=>		1@zaugolnaya.da
Field23			=>		Как физическое лицо (наличными, карточкой, переводом и др.)
Field7			=>		ТЕСТ, заказ тестовый
Field17			=>		321451034.1392040627
Field18			=>		tiberis.ru
Field13			=>
Field13-url		=>
Entsource		=>
CreatedBy		=>		public
DateCreated		=>		2014-03-18 09:59:01
EntryId			=>		3120
IP				=>		93.125.126.31
HandshakeKey	=>		ASTRALOPITEK_diswashere_Gha67G019yBAQQ9
--------------------------------------------------------------------------------

WEBHOOK setup
http://1c.tiberis.ru/tibscript/wufoo/wufoo_pinatel.php
ASTRALOPITEK_diswashere_Gha67G019yBAQQ9


*/









// CONSTANT
$wufoo_WebHook_Handshake_Key 	= 'ASTRALOPITEK_diswashere_Gha67G019yBAQQ9';
$NEW_entries_json 				= 'new_entries.json';

/* // Так, структурно, этот JSON должен выглядеть
--------------------------------------------------
{
"last_post":
    {
    "datetime":"2014-01-05 22:36:41",
    "fromip":"192.168.56.101"
    },
"last_get":
    {
    "datetime":"2014-01-05 22:36:41",
    "fromip":"192.168.56.101"
    },
"new_entries":
    {
    "1234":
        {
        "Field1":"FIO bla-bla-bla",
        "Field18":"tiberis.ru",
        "Field17":"123456789.987654321",
        ...
        ...
        ...
        },
    "5678":
        {
        "Field1":"FIO bla-bla-bla",
        "Field18":"tiberis.ru",
        "Field17":"123456789.987654321",
        ...
        ...
        ...
        }
    ...
    ...
    ...
    }
}
----------------------------------------------- */


// Собираем POST данные в кучку
$NEWentry = array();
$NEWentry["Field1"] 		=	isset($_POST['Field1']) ? trim($_POST['Field1']) : null;
$NEWentry["Field2"] 		=	isset($_POST['Field2']) ? trim($_POST['Field2']) : null;
$NEWentry["Field26"] 		=	isset($_POST['Field26']) ? trim($_POST['Field26']) : null;
$NEWentry["Field3"] 		=	isset($_POST['Field3']) ? trim($_POST['Field3']) : null;
$NEWentry["Field4"] 		=	isset($_POST['Field4']) ? trim($_POST['Field4']) : null;
$NEWentry["Field20"] 		=	isset($_POST['Field20']) ? trim($_POST['Field20']) : null;
$NEWentry["Field23"] 		=	isset($_POST['Field23']) ? trim($_POST['Field23']) : null;
$NEWentry["Field7"] 		=	isset($_POST['Field7']) ? trim($_POST['Field7']) : null;
$NEWentry["Field17"] 		=	isset($_POST['Field17']) ? trim($_POST['Field17']) : null;
$NEWentry["Field18"] 		=	isset($_POST['Field18']) ? trim($_POST['Field18']) : null;
$NEWentry["Field13"] 		=	isset($_POST['Field13']) ? trim($_POST['Field13']) : null;
$NEWentry["Field13_url"] 	=	isset($_POST['Field13-url']) ? trim($_POST['Field13-url']) : null;
$NEWentry["Entsource"] 		=	isset($_POST['Entsource']) ? trim($_POST['Entsource']) : null;
$NEWentry["CreatedBy"] 		=	isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : null;
$NEWentry["DateCreated"] 	=	isset($_POST['DateCreated']) ? trim($_POST['DateCreated']) : null;
$NEWentry["EntryId"] 		=	isset($_POST['EntryId']) ? trim($_POST['EntryId']) : null;
$NEWentry["IP"] 			=	isset($_POST['IP']) ? trim($_POST['IP']) : null;


$HandshakeKey 				=	isset($_POST['HandshakeKey']) ? trim($_POST['HandshakeKey']) : null;
$post_from_ip 				=	isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '127.0.0.1';




if ($HandshakeKey == $wufoo_WebHook_Handshake_Key) { // мера предосторожности, этот параметр знает Wufoo и шлет его при обращении

	// Чтение и Запись json файла содержащего время последней генерации файлов
	$NEW_entries_json_file = fopen($NEW_entries_json, 'r');
	if ( $NEW_entries_json_file ) {

		// читаем json из файла и распаковываем в удобный для работы массив
		$ENTRIES_data = json_decode(file_get_contents($NEW_entries_json), true);
		fclose($NEW_entries_json_file);

		// Меняем дату последнего обращения
		$ENTRIES_data["last_post"]["datetime"] = date('Y-m-d H:i:s', time());
		$ENTRIES_data["last_post"]["fromip"] = $post_from_ip;
		// Добавляем к этому массиву пришедшие свежие данные
		$ENTRIES_data["new_entries"][$NEWentry["EntryId"]] = $NEWentry;

		$ENTRIES_data_json = json_encode($ENTRIES_data); // запаковываем масивв обратно в json
		$NEW_entries_json_file = fopen($NEW_entries_json, 'w+');
		if ( $NEW_entries_json_file ) {
			fwrite($NEW_entries_json_file, $ENTRIES_data_json);
			fclose($NEW_entries_json_file);
			$STATUS = 'status:[Save New Entry]';
		} else { $STATUS = 'status:[Write json file ERROR]'; }
	} else { $STATUS = 'status:[Read json file ERROR]'; }

} else { // обращающийся к скрипту не верно указал в POST запросе WebHook_Handshake_Key
	$STATUS = 'status:[AUTH ERROR]';
}



// txt log
$fp = fopen('log_action_wufoo.txt', 'a');
fwrite($fp, "[".date('Y-m-d H:i:s', time())."] wufoo_pinatel.php | ".$STATUS." * post_from_ip:[".$post_from_ip."] Entry#".$NEWentry["EntryId"]."\n");
fclose($fp);




?>
