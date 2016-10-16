<?php
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$user = 'root';
$pass = '12431243';
$db = 'tiberis_clients';

ini_set('mssql.charset', 'UTF-8');

$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

$db->query('SET NAMES utf8;');

date_default_timezone_set('Europe/Moscow');
$data = date("Y-m-d");
/*
$removeparam = date("m")*date("d")*date("y")*date("H")*date("i")*date("s");
echo $removeparam.'<br>';
echo $a = date("m")*date("d")*date("y").'<br>';
$temp = date("d")-7;
echo $b = date("m")*$temp*date("y").'<br>';
echo $a-$b;
*/

# 1344 часов = неделя
/*
echo $data.'<br>';
$hours1 = date("d")*24;
$hours2 = date("m")*date('t')*24;
$hours3 = date("y")*date("m")*date("d")*24;
$current = $hours1+$hours2+$hours3;
echo $current.'<br>';
$hours1 = (date("d")-7)*24;
$hours2 = date("m")*(date("d")-7)*24;
$hours3 = date("y")*date("m")*(date("d")-7)*24;
$past = $hours1+$hours2+$hours3;
echo $past.'<br>';
echo $current-$past;
die();
*/


$result = $db->query("SELECT id, last_enter FROM customers_id;");
$datetime1 = new DateTime("now");

foreach($result as $entity) {
    $datetime2 = new DateTime($entity['last_enter']);
    $interval = $datetime1->diff($datetime2);
    $test = $interval->format('%R%a');
    if ($test<-7){
        $result2 = $db->query("DELETE FROM customers_id WHERE id='".$entity['id']."';");
    }
}