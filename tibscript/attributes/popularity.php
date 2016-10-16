<?php
	$cat_id = trim($_REQUEST['c']);
	$prod_id = trim($_REQUEST['i']);

	$host = 'localhost';
	$user = 'root';
	$pass = '12431243';
	$db = 'custom_attributes';

	ini_set('mssql.charset', 'UTF-8');

	$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

	$db->query('SET NAMES utf8;');

	if ($result = $db->query("SELECT popularity FROM tiberis_popularity_bufer WHERE cat_id='".$cat_id."' AND prod_id='".$prod_id."';")->fetch()) {
	    $newp = intval($result['popularity']) + 1;
	    $db->query("UPDATE tiberis_popularity_bufer SET popularity='".$newp."' WHERE cat_id='".$cat_id."' AND prod_id='".$prod_id."';");
	} else {
	    $db->query("INSERT INTO tiberis_popularity_bufer(cat_id, prod_id, popularity) VALUES('".$cat_id."', '".$prod_id."', '1') ;");
	}

?>