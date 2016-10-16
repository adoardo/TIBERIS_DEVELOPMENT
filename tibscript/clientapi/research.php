<?php
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$user = 'root';
$pass = '12431243';
$db = 'tiberis_clients';

ini_set('mssql.charset', 'UTF-8');

$search = $_REQUEST['what'];

$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

$db->query('SET NAMES utf8;');

$result = array();

$result2 = $db->query("SELECT client_id, customer_id, true_last_enter FROM customers_id WHERE customer_id = '".$search."';");

$i = 0;
                        foreach ($result2 as $row) {

                        	$result[$i] = '<tr cid="'.$row['client_id'].'" cud="'.$row['customer_id'].'"><td>'.$row['client_id'].'</td><td>f'.$row['customer_id'].'</td><td>'.$row['true_last_enter'].'</td></tr>';
                            #echo '<tr cid="'.$row['client_id'].'" cud="'.$row['customer_id'].'"><td>'.$row['client_id'].'</td><td>f'.$row['customer_id'].'</td><td>'.$row['true_last_enter'].'</td></tr>';

                            $i++;
                        }

                        echo json_encode($result);

?>