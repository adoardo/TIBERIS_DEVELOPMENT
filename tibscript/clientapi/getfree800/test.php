<?php
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Europe/Moscow');
$host = 'localhost';
$user = 'root';
$pass = '12431243';
$db = 'tiberis_clients';

ini_set('mssql.charset', 'UTF-8');

$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

$db->query('SET NAMES utf8;');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="robots" content="noindex,nofollow"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Клик по "показать номер" за последние пол часа</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>
<body>
<section id="main">
    <div class="container">
        <div class="row">
            <h3>Клик по "показать номер" за последние пол часа</h3>
            <?php
            $data = date("d.m.y H:i:s");
            $format = "d.m.y H:i:s";
            $current = DateTime::createFromFormat($format, $data);
            $maxID = $db->query("SELECT MAX(id) as 'max' FROM customers_clickphone;")->fetch();
            $result = $db->query("SELECT * FROM customers_clickphone  WHERE id = ".$maxID['max'].";")->fetch();
            
            $past = DateTime::createFromFormat($format, $result['click_time']);
            $since_start = $past->diff($current);

            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            # echo $minutes.' minutes';
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                if ($minutes < 31) {
                    if ($result['client_id'] == 'undefined') {
                        echo '<div style="font-size:42px;text-align:center;">Error: undefined ClientID</div>';
                    } else {
                        echo '<div style="font-size:42px;text-align:center;">ClientID: '.$result['client_id'].'</div>';
                    }
                } else {
                    echo '<div style="font-size:42px;text-align:center;">Error: no click</div>';
                }
            } else {
                if ($minutes < 31) {
                    if ($result['client_id'] == 'undefined') {
                        echo 'Error: undefined ClientID';
                    } else {
                        echo 'ClientID: '.$result['client_id'];
                    }
                } else {
                    echo 'Error: no click';
                }
            }


            ?>
        </div>
    </div>
</section>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</body>
</html>
