<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: text/html; charset=windows-1251');

    $host = 'localhost';
    $user = 'root';
    $pass = '12431243';
    $db = 'tiberis';

    ini_set('mssql.charset', 'UTF-8');

    $db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

    $db->query('SET NAMES utf8;');

    $result = array();


    if (($_REQUEST['pmax']!=-1)&&(($_REQUEST['pmin']!=-1))) {
        $min = $_REQUEST['pmin'];
        $max = $_REQUEST['pmax'];

        $catId = $_REQUEST['c'];

        $brands = $_REQUEST['brend'];

        $pos = strpos($brands, '_');
        $brandFilter = '';

        if ($pos !== false) {
            $brands = explode('_',$brands);
            $st = '(';
            $i = 0;
            while ($i < count($brands)) {
                $temp3 = $db->query("SELECT value FROM eav_attribute_option_value WHERE store_id = '0' AND option_id='".$brands[$i]."' LIMIT 1;")->FETCH();
                if ($i == 0) {
                    $st .= "value='".mb_strtolower($temp3['value'], 'UTF-8')."' ";
                } else {
                    $st .= "OR value='".mb_strtolower($temp3['value'], 'UTF-8')."' ";
                }
                $i++;
            }
            $st .= ')';
        } else {
            $temp3 = $db->query("SELECT value FROM eav_attribute_option_value WHERE store_id = '0' AND option_id='".$brands."' LIMIT 1;")->FETCH();
            $st = "value='".mb_strtolower($temp3['value'], 'UTF-8')."'";
            if ($st == "value=''") {
                $st = 'nobrands';
            }
        }

        if ($st!='nobrands') {
            $brandFilter = "AND attribute_id='177' AND ".$st;
        } else {
            $brandFilter = '';
        }

        # Получил продукты категории
        $products = $db->query("SELECT product_id FROM catalog_category_product_index WHERE category_id = '.$catId.' ;");

        $i = 0;
        foreach ($products as $product) {
            $id = $product['product_id'];
            # получил цену
            $temp = $db->query("SELECT final_price FROM catalog_product_index_price WHERE entity_id = '.$id.' AND customer_group_id='0' LIMIT 1;")->fetch();
            $price = explode('.',$temp['final_price']);
            $price = (int)$price[0];


            if (($price >= $min)&&($price <= $max)) {
                if ($st!='nobrands') {
                    # если указан бренд фильтруем по брендам
                    # в st указаны бренды типа (value='ресанта' OR value='сварог') и так далее
                    # то есть в цену вошли и нужно проверить атрибут производителя для товара с известным ID (entity_id)
                    //echo json_encode("SELECT entity_id FROM catalog_product_entity_varchar WHERE attribute_id = '155' AND entity_id='".$id."' AND ".$st." LIMIT 1");
                    //die();
                    $temp2 = $db->query("SELECT * FROM catalog_product_entity_varchar WHERE attribute_id = '155' AND entity_id='".$id."' LIMIT 1;")->fetch();


                    if ($temp2['entity_id']) {

                        $pos = strpos($brands, '_');

                        if ($pos !== false) {
                            //$aaa = json_encode($brands);

                            //$brands = explode('_',$brands);
                            $j = 0;
                            while ($j < count($brands)) {
                                if ($brands[$j] == $temp2['value']) {
                                    $result[$i] = $price;
                                    $i++;
                                    break;
                                }
                                $j++;
                            }
                        } else {
                            if ($brands == $temp2['value']) {
                                $result[$i] = $price;
                                $i++;
                            }
                        }










                    }

                } else {
                    $result[$i] = $price;
                    $i++;
                }
            }
        }

        echo json_encode($result);
        //echo 'max '.$_REQUEST['pmax'].' min '.$_REQUEST['pmin'].' cat '.$catId;
    }

    die();
?>