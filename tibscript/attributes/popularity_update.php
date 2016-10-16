<?php
//catalog_category_product
	$currentDate = date("Y-m-d");

	$host = 'localhost';
	$user = 'root';
	$pass = '12431243';
	$db = 'magento_tiberis';
	$dbAttr = 'custom_attributes';

	ini_set('mssql.charset', 'UTF-8');

	$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );
	$db->query('SET NAMES utf8;');

	$dbAttr = new PDO ('mysql:host=' . $host . ';dbname=' . $dbAttr, $user, $pass );
	$dbAttr->query('SET NAMES utf8;');




	$entries = $db->query("SELECT category_id, product_id, position FROM catalog_category_product_index;");

	foreach ($entries as $entry) {
		$sel = $dbAttr->query("SELECT cat_id, prod_id, popularity FROM tiberis_popularity_data WHERE cat_id='".$entry['category_id']."' AND prod_id='".$entry['product_id']."';");
		if ($temp = $sel->fetch()) {
			$temp = 0;
			foreach ($sel as $item) {
				$temp = $temp + intval($item['popularity']);
			}
			$db->query("UPDATE catalog_category_product_index SET position='".$temp."' WHERE category_id='".$entry['category_id']."' AND product_id='".$entry['product_id']."' ;");
		}
	}


?>