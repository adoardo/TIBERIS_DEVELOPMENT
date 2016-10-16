<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);

	$host = 'localhost';
	$user = 'root';
	$pass = '12431243';
	$db = 'tiberis';

	ini_set('mssql.charset', 'UTF-8');

	$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

	$db->query('SET NAMES utf8;'); 

	# Избавляемся от старых скидок
	$result = $db->query("SELECT entity_id FROM catalog_product_index_price WHERE price=final_price ;");
	foreach ($result as $row) {
		$db->query("UPDATE catalog_product_entity_varchar SET value=NULL WHERE attribute_id=190 OR attribute_id=189 AND entity_id=".$row['entity_id']." ;");
	}

	# Обновляем скидки
	$result = $db->query("SELECT customer_group_id,entity_id,price,final_price FROM catalog_product_index_price WHERE price!=final_price ;");
	foreach ($result as $row) {
		if ($row['customer_group_id']==0) {
			$saleAmount = intval($row['price']) - intval($row['final_price']);
			$checkSaleAmount = $db->query("SELECT * FROM catalog_product_entity_varchar WHERE attribute_id=190 AND entity_id=".$row['entity_id']." ;");
			if ($checkSaleAmount->fetchColumn()) {
				# Если запись уже имеется
				$saleAmountRec = $db->query("UPDATE catalog_product_entity_varchar SET value=".$saleAmount." WHERE attribute_id=190 AND entity_id=".$row['entity_id']." ;");
			} else {
				$saleAmountRec = $db->query("INSERT INTO catalog_product_entity_varchar(attribute_id, store_id, entity_id, value) VALUES (190, 0, ".$row['entity_id'].", ".$saleAmount.") ;");
			}
			

			$salePercent = round((100-((intval($row['final_price'])/intval($row['price']))*100)), 4);
			$checkSaleAmount = $db->query("SELECT * FROM catalog_product_entity_varchar WHERE attribute_id=189 AND entity_id=".$row['entity_id']." ;");
			if ($checkSaleAmount->fetchColumn()) {
				# Если запись уже имеется
				$saleAmountRec = $db->query("UPDATE catalog_product_entity_varchar SET value=".$salePercent." WHERE attribute_id=189 AND entity_id=".$row['entity_id']." ;");
			} else {
				$saleAmountRec = $db->query("INSERT INTO catalog_product_entity_varchar(attribute_id, store_id, entity_id, value) VALUES (189, 0, ".$row['entity_id'].", ".$salePercent.") ;");
			}
		}
		
	}
?>