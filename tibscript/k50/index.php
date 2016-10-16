<?php
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Europe/Moscow');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="robots" content="noindex,nofollow"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Звонки за последние 24 часа</title>
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
            <h3>Звонки за последние 24 часа</h3>
            <div class="col-md-12">
                <div class="table-responsive">
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>start_time</th>
                                <th>caller_phone</th>
                                <th>called_phone</th>
                                <th>duration</th>
                                <th>is_matching</th>
                                <th>analytics_client_id</th>
                                <th>uuid</th>
                            </tr>
                        </thead>
                        <tbody class="callersdata">
                        
                        </tbody>
                    </table>
                    <div class="loader" data-from="<?php $date = date('Y-m-d');$date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) )); echo $date.' '. date('H:i:s') ?>" data-to="<?php echo date('Y-m-d H:i:s');?>">
                        <img src="img/loading.gif"/>
                    </div>
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
