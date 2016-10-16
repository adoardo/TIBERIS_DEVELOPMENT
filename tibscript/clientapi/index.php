<?php
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$user = 'root';
$pass = '12431243';
$db = 'tiberis_clients';

ini_set('mssql.charset', 'UTF-8');

$db = new PDO ('mysql:host=' . $host . ';dbname=' . $db, $user, $pass );

$db->query('SET NAMES utf8;');

if ($phone = $_REQUEST['phone']) {

    $date = date('Y-m-d');
    $date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
    $from = $date.' '. date('H:i:s');
    $to = date('Y-m-d H:i:s');

    $ch = curl_init();
    $url = "http://api.tracker.k50.ru/api/call/ext/77212604978137/".$from."/".$to."?apiKey=e072665d-438d-409a-858f-9f827ee7c9da";
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    $obj = json_decode($result);
    $resultPhone = false;

    foreach($obj as $key=>$value) {
        foreach($value as $key2=>$item) {
            if ($key2 == 'caller_phone') {
                if ($item == $phone) {
                    $resultPhone = $value->analytics_client_id;
                    break;
                }
            }
        }
    }

    if (isset($_SERVER['HTTP_USER_AGENT'])) { ?>
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
                <h3>Телефон: <?php echo $phone; ?></h3>
                <?php
                    if ($resultPhone === false) {
                        echo '<div style="font-size:42px;text-align:center;">No call found</div>';
                    } else {
                        if ($resultPhone == '') {
                            echo '<div style="font-size:42px;text-align:center;">No client id specified</div>';
                        } else {
                            echo '<div style="font-size:42px;text-align:center;">ClientID: '.$resultPhone.'</div>';
                        }
                    }
                ?>
            </div>
            </section>
        </body>
        </html>
    <?php } else {
        if ($resultPhone === false) {
            echo 'No call found';
        } else {
            if ($resultPhone == '') {
                echo 'No client id specified';
            } else {
                echo 'ClientID: '.$resultPhone;
            }
        }
    }
} else {
    if ($email = $_REQUEST['email']) {

        $temp = explode('@', $email);
        $email = preg_replace("/[^0-9,.]/", "", $temp[0]);
        $result = $db->query("SELECT client_id FROM customers_id WHERE customer_id = '".$email."';")->fetch();

        if (isset($_SERVER['HTTP_USER_AGENT'])) { ?>
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
                    <h3>Email: <?php echo $email; ?></h3>
                    <?php
                        if ($result['client_id']) {
                            if (($result['client_id'] == '')||($result['client_id'] == 'null')) {
                                echo '<div style="font-size:42px;text-align:center;">No client id specified</div>';
                            } else {
                                echo '<div style="font-size:42px;text-align:center;">ClientID: '.$result['client_id'].'</div>';
                            }
                        } else {
                            echo '<div style="font-size:42px;text-align:center;">Cliend id not found</div>';
                        }
                    ?>
                </div>
            </section>
            </body>
            </html>
        <?php } else {
            if ($result['client_id']) {
                if (($result['client_id'] == '')||($result['client_id'] == 'null')) {
                    echo 'No client id specified';
                } else {
                    echo 'ClientID: '.$result['client_id'];
                }
            } else {
                echo 'Cliend id not found';
            }
        }
    } else {
        include_once('askldjlkajsd.php');
    }
}
?>
