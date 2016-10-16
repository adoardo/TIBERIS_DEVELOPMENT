<?php
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$user = 'root';
$pass = '12431243';
$db = 'tiberis_clients';

ini_set('mssql.charset', 'UTF-8');

$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

date_default_timezone_set('Europe/Moscow');
$data = date("Y-m-d");
$true_data = date("d.m.y H:i:s");

$db->query('SET NAMES utf8;');

$id = trim($_REQUEST['client_id']);

if ($id) {

    $result = $db->query("SELECT customer_id FROM customers_id WHERE client_id='".$id."' ORDER BY id DESC LIMIT 1;")->fetch();

    if ($result) {
        $result3 = $db->query("UPDATE customers_id SET last_enter='".$data."', true_last_enter='".$true_data."' WHERE customer_id='".$result['customer_id']."';");

        echo $result['customer_id'];
        die();
    } else {
        $flag = true;
        while($flag) {
            $newClientId = rand(0, 99999);

            if ($newClientId<10) {
                $newClientId = '0000'.$newClientId;
            } else {
                if ($newClientId<100) {
                    $newClientId = '000'.$newClientId;
                } else {
                    if ($newClientId<1000) {
                        $newClientId = '00'.$newClientId;
                    } else {
                        if ($newClientId<10000) {
                            $newClientId = '0'.$newClientId;
                        }
                    }
                }
            }
            $test = $db->query("SELECT * FROM customers_id WHERE customer_id='".$newClientId."' ORDER BY id ;")->fetch();
            if (!$test){
                $flag = false;
            }
        }




        $result = $db->query("INSERT INTO customers_id(client_id, customer_id, last_enter, true_last_enter) VALUES ('".$id."','".$newClientId."','".$data."','".$true_data."');");

        echo $newClientId;
        die();
    }
} else {
    echo 'h';
    die();
}

?>