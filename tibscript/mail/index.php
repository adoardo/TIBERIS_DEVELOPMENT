<?php
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$user = 'root';
$pass = '12431243';
$db = 'tiberis_emails';

ini_set('mssql.charset', 'UTF-8');

$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

$db->query('SET NAMES utf8;');

$data = array();
$data['type'] = $_REQUEST['field'];
$data['url'] = $_REQUEST['field0'];
$data['name'] = $_REQUEST['field1'];
$data['email'] = $_REQUEST['field2'];
$data['phone'] = $_REQUEST['field3'];
$data['message'] = $_REQUEST['field4'];
$data['client'] = $_REQUEST['field5'];

date_default_timezone_set('Europe/Moscow');
$data['date'] = date("d.m.y H:i:s");

$to  = 'sales@tiberis.ru';
$toDEBUG  = 'adorealado@gmail.com';

/*
1 - задать вопрос (ТОВАР: убрать бордер топ у "товар" и у "ссылка на товар")
2 - заказ обратного звонка (добавить бордер вниз и бордер радиус вниз ПРИ условии что введён только телефон)
3 - быстрый заказ (ТОВАР: убрать бордер топ у "товар" и у "ссылка на товар" ПРИ условии наличия комментария)
4 - добавить свой вопрос
*/

if (($_REQUEST['requestfreecall']=='1')&&($_REQUEST['client_id'])) {
	/*
	if ($_REQUEST['from']=='1') {
		$subject = 'Клик по "показать номер" '.$data['date'];
	} else {
		$subject = 'Клик по "Бесплатный звонок из России" '.$data['date'];
	}
	*/


    $host2 = 'localhost';
    $user2 = 'root';
    $pass2 = '12431243';
    $db2 = 'tiberis_clients';

    ini_set('mssql.charset', 'UTF-8');

    $db2 = new PDO ('mysql:host=' . $host2 . ';dbname=' . $db2, $user2, $pass2 );

    $db2->query('SET NAMES utf8;');
    $result = $db2->query("INSERT INTO customers_clickphone(click_time, client_id) VALUES ('".$data['date']."','".$_REQUEST['client_id']."');");

	$subject = 'Клик по "показать номер" 8-800';

	$mailBODY = '
		<html>
		<head>
		  <title>'.$subject.'</title>
		</head>
		<body>
		  <table style="border-spacing: 0px;">';
		  		$mailBODY .= '
			  		<tr>
			  			<td style="border-bottom: 1px solid #bbb;width: 26%;border-radius:4px 0px 0px 0px;border-top:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;font-weight:bold;">Сайт</td>
						<td style="border-bottom: 1px solid #bbb;width: 74%;color:#555;border-radius:0px 4px 0px 0px;border-top:1px solid #bbb;border-right:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;">'.$_REQUEST['from_site'].'</td>
						</tr>	
						<tr>
						<td style="border-bottom: 1px solid #bbb;width: 26%;border-radius:0px 0px 0px 4px;border-top: none;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;font-weight:bold;">ClientID</td>
						<td style="border-bottom: 1px solid #bbb;width: 74%;color:#555;border-radius:0px 0px 4px 0px;border-top: none;border-right:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;">'.$_REQUEST['client_id'].'</td>
					</tr>
		  		';
		  	
			$mailBODY .= '
		  </body>
		</html>
	';

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: Бесплатный звонок <hello@fromtiberis.ru>' . "\r\n";

	if (!mail($to, $subject, $mailBODY, $headers)) {
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: Ошибка <hello@fromtiberis.ru>' . "\r\n";
		$mailBODY = 'Ошибка отправки почты бесплатного звонка';
		mail($toDEBUG, $subject, $mailBODY, $headers);
		echo 'email send error';
		die();
	} else {
		mail($toDEBUG, $subject, $mailBODY, $headers);
		echo 0;
		die();
	}

} else {
	$subject = '';
switch ($data['type']) {
    case 1:
        // тема письма
		$subject = 'Есть вопрос '.$data['date'];
        break;
    case 2:
        $subject = 'Заказ обратного звонка '.$data['date'];
        break;
    case 3:
        $subject = 'Быстрый заказ '.$data['date'];
        break;
    case 4:
        $subject = 'Добавить свой вопрос '.$data['date'];
        break;
    default:
    	break;
}

if ($subject == '') {
	echo 'userdata error';
	die(0);
} else {
	$mailBODY = '
		<html>
		<head>
		  <title>'.$subject.'</title>
		</head>
		<body>
		  <table style="border-spacing: 0px;">';
		  	if (($data['phone'])&&($data['phone']!='')) {
		  		$mailBODY .= '
			  		<tr>
						<td style="width: 26%;border-radius:4px 0px 0px 0px;border-top:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;font-weight:bold;">Телефон</td>
						<td style="width: 74%;color:#555;border-radius:0px 4px 0px 0px;border-top:1px solid #bbb;border-right:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;">'.$data['phone'].'</td>
					</tr>
		  		';
		  	}
			if (($data['name'])&&($data['name']!='')) {
				$mailBODY .= '
					<tr>
						<td style="width: 26%;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;font-weight:bold;">Контактное лицо</td>
						<td style="width: 74%;color:#555;border-right:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;">'.$data['name'].'</td>
					</tr>
				';
			}
			if (($data['email'])&&($data['email']!='')) {
				$mailBODY .= '
					<tr>
						<td style="width: 26%;border-radius:4px 0px 0px 0px;border-top:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;font-weight:bold;">Email</td>
						<td style="width: 74%;color:#555;border-radius:0px 4px 0px 0px;border-top:1px solid #bbb;border-right:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;">'.$data['email'].'</td>
					</tr>
				';
			}
			if (($data['message'])&&($data['message']!='')) {
				if ($data['url']!='') {
					$style1 = 'border-radius:0px 0px 0px 0px;';
					$style2 = 'border-radius:0px 0px 0px 0px;';
				} else {
					$style1 = 'border-radius:0px 0px 0px 4px;';
					$style2 = 'border-radius:0px 0px 4px 0px;';
				}
				$mailBODY .= '
					<tr>
						<td style="width: 26%;'.$style1.'border-bottom:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;font-weight:bold;">Комментарии</td>
						<td style="width: 74%;color:#555;'.$style2.'border-bottom:1px solid #bbb;border-right:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;">'.$data['message'].'</td>
					</tr>
				';
			}
			if (($data['url'])&&($data['url']!='')) {
				$mailBODY .= '
					<tr>
						<td style="width: 26%;border-radius:0px 0px 0px 4px;border-bottom:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;font-weight:bold;">Товар</td>
						<td style="width: 74%;color:#555;border-radius:0px 0px 4px 0px;border-bottom:1px solid #bbb;border-right:1px solid #bbb;border-left:1px solid #bbb;padding: 7px;border-top:1px solid #bbb;font-size:16px;">'.$data['url'].'</td>
					</tr>
				';
			}
			$mailBODY .= '
		  	</table>
		  	';
		  	if ($data['client']) {
				$mailBODY .= '
					<div style="color:#555;padding: 7px;margin-top: 14px;"><span style="color:#222;font-weight:bold;">ClientID:</span> '.$data['client'].'</div>
				';
			}
			$mailBODY .= '
		  </body>
		</html>
	';

	$result = $db->query("INSERT INTO customers_mail(mailtype,field1,field2,field3,field4,field5,mail_date,ga_id) VALUES ('".$data['type']."','".$data['name']."','".$data['email']."','".$data['phone']."','".$data['message']."','".$data['url']."','".$data['date']."','".$data['client']."');");
	$result2 = $db->query("SELECT id FROM customers_mail ORDER BY id DESC LIMIT 1;")->fetch();

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: Сообщение №'.$result2['id'].' <hello@fromtiberis.ru>' . "\r\n";

	if (!mail($to, $subject, $mailBODY, $headers)) {
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: Ошибка <hello@fromtiberis.ru>' . "\r\n";
		$mailBODY = 'Ошибка отправки почты';
		mail($toDEBUG, $subject, $mailBODY, $headers);
		echo 'email send error';
		die();
	} else {
		mail($toDEBUG, $subject, $mailBODY, $headers);
		echo 0;
		die();
	}
}

}

?>