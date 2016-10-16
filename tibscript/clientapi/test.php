<?php
header('Access-Control-Allow-Origin: *');

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
    <title>Новые посетители</title>
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
            <h3>Поиск новых посетителей</h3>
            <div class="col-md-12">
                <!--<div class="col-md-6"><label>По clientID</label><input type="text" class="form-control" id="uClientId"></div>-->
                <div class="col-md-8"><label>Почтовый адрес или идентификатор</label><input placeholder="Пример: f99999@tiberis.ru или 99999" type="text" class="form-control" id="uIdent"></div>
                <div class="col-md-4"><a href="#" class="search-button" id="research">Поиск</a></div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <div class="loader"><img src="img/loader.gif"/></div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ClientID</th>
                                <th>Идентификатор</th>
                                <th>Последний раз был на сайте</th>
                            </tr>
                        </thead>
                        <tbody id="tadaa">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="js/scripts.js" type="text/javascript"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</body>
</html>
