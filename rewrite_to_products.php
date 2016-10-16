<?php
# Данные для подключения к БД
$dbhost="localhost";
$dbname="tiberis";
$dbuser="tiberis";
$dbpass="12431243";

# Подключаемся к SQL серверу
$db = @mysqli_connect($dbhost,$dbuser,$dbpass);
if (!$db)
{
    die('Ошибка соединения с базой данных, повторите попытку позже!');
}

# Выбираем базу данных
mysqli_select_db ($db,$dbname);
# Устанавливаем кодировку выбранной базы данных
mysqli_set_charset($db,"utf8");

# Делаем выборку по продуктам, которые нужно обновить
$sel = "UPDATE url_rewrite SET request_path = CONCAT('products/', request_path) WHERE entity_type = 'product'";
$q = mysqli_query($GLOBALS["db"],$sel);

//entity_type = product
//request_path не содержит product

?>