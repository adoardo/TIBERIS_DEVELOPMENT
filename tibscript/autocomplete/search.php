<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: text/html; charset=UTF-8');
    


    $host = 'localhost';
    $user = 'root';
    $pass = '12431243';
    $db = 'tiberis';

    #ini_set('mssql.charset', 'UTF-8');
    $db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass);
    $db->query('SET NAMES utf8 COLLATE utf8_general_ci;');



/*
    $dsn = 'mysql:host=localhost;dbname=magento_tiberis';
    $username = 'root';
    $password = '12431243';
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    ); 

    $db = new PDO($dsn, $username, $password, $options);

*/














    $result = array();

    if(isset($_REQUEST['q']) && trim($_REQUEST['q']))
    {
        $search = rawurldecode(trim($_REQUEST['q']));
    } else {
        $search = "";
    }

    $search = ucfirst($search);

    $products = $db->query("SELECT value, entity_id FROM catalog_product_entity_varchar WHERE attribute_id = '73' AND value like '%".$search."%'
     ORDER BY CASE WHEN value like '".$search."%' THEN 0
                   WHEN value like '% %".$search."% %' THEN 1
                   WHEN value like '%".$search."' THEN 2
                   ELSE 3
              END;");

    $i = 0;

//echo $products->rowCount();

    foreach($products as $item) {
        $result[$i] = array();

        # Достаём название продукта
        $result[$i]['name'] = $item['value'];
        # Достаём URL
        $url = $db->query("SELECT request_path FROM url_rewrite WHERE entity_id = '".$item['entity_id']."';")->fetch();
        $result[$i]['url'] = 'products/'.$url['request_path'];

        # Достаём описание
        $desc = $db->query("SELECT value FROM catalog_product_entity_text WHERE attribute_id = '75' AND entity_id = '".$item['entity_id']."';")->fetch();
        # $tempz = strip_tags($desc['value']);
        # $tempz = mb_convert_encoding($tempz, "UTF-8");
        # $tempz = utf8_encode($tempz);
        # $result[$i]['desc'] = substr($tempz , 0, 160).'...';
$result[$i]['desc'] = $desc['value'];


        # Достаём картинку
        $image = $db->query("SELECT value FROM catalog_product_entity_varchar WHERE attribute_id = '87' AND entity_id = '".$item['entity_id']."';")->fetch();
        $result[$i]['img'] = 'http://51.254.132.135/pub/media/catalog/product'.$image['value'];

        # Достаём цену
        $price = $db->query("SELECT price FROM catalog_product_index_price_idx WHERE entity_id = '".$item['entity_id']."' AND customer_group_id = '0';")->fetch();
        $temp = explode('.',$price['price']);
        $result[$i]['price'] = $temp[0].' руб.';

        $i++;

        if ($i == 4 ) {
        	$result[$i] = array();
			$result[$i]['name'] = 'found';
			$result[$i]['num'] = $products->rowCount();
        	break;
        }
    }
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}
//var_dump($result);
    //echo json_encode($result);
    echo json_encode(utf8ize($result));
?>