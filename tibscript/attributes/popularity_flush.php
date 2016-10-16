<?php
	$currentDate = date("Y-m-d");

	$host = 'localhost';
	$user = 'root';
	$pass = '12431243';
	$db = 'custom_attributes';

	ini_set('mssql.charset', 'UTF-8');

	$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

	$db->query('SET NAMES utf8;');

	$result = $db->query("SELECT * FROM tiberis_popularity_bufer;");
	foreach ($result as $item) {
		$flush = $db->query("SELECT * FROM tiberis_popularity_bufer;");

		$db->query("INSERT INTO tiberis_popularity_data(cat_id, prod_id, popularity, date) VALUES('".$item['cat_id']."', '".$item['prod_id']."', '".$item['popularity']."', '".$currentDate."') ;");
	}

	$db->query("DELETE FROM `tiberis_popularity_bufer` WHERE 1 ;");
?>