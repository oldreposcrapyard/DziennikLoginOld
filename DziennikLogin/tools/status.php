<?php

require '../classes/autoload.php';
require '../config.local.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$statuses=  array();
// Create the logger
$logger = new Logger('DziennikLoginLogger');
// Now add some handlers
$logger->pushHandler(new StreamHandler('error.log', Logger::DEBUG));


// You can now use your logger
$logger->addInfo('My logger is now ready');

//check for database connection
try {
    $pdoHandle = new \PDO("mysql:host=$CONF[databaseHost];dbname=$CONF[databaseName];charset=utf8", $CONF['databaseUsername'], $CONF['databasePassword'], array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ));
    $pdoHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $logger->addInfo('The database is working');
    $statuses['baza'] = 'ok';
} catch (PDOException $e) {
    $logger->addInfo('The database is NOT working');
    $logger->addInfo($e->getMessage());
    $statuses['baza'] = 'remove';
}

//check e-dziennik availability
//returns true, if domain is availible, false if not
function isDomainAvailible($domain) {
    //check, if a valid url is provided
    if (!filter_var($domain, FILTER_VALIDATE_URL)) {
        return false;
    }

    //initialize curl
    $curlInit = curl_init($domain);
    //Ignore the SSL communication, because the certificate is outdated
    curl_setopt($curlInit, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlInit, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curlInit, CURLOPT_HEADER, true);
    curl_setopt($curlInit, CURLOPT_NOBODY, true);
    curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

    //get answer
    $response = curl_exec($curlInit);

    curl_close($curlInit);

    if ($response)
        return true;

    return false;
}

if (isDomainAvailible('https://92.55.225.11')) {
    $logger->addInfo('The e-dziennik is working');
    $statuses['dziennik'] = 'ok';
} else {
    $logger->addInfo('The e-dziennik is NOT working');
    $statuses['dziennik'] = 'remove';
}
?>


<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title>DziennikLogin - Panel statusu</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Le styles -->
        <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet"  media="screen">
        <style type="text/css">
            body {
                padding-top: 20px;
                padding-bottom: 40px;
            }

            /* Custom container */
            .container-narrow {
                margin: 0 auto;
                max-width: 700px;
            }
            .container-narrow > hr {
                margin: 30px 0;
            }
        </style>
        <link href="../classes/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container-narrow">

            <div class="masthead">
                <!--<ul class="nav nav-pills pull-right">-->
                    <!--<li><a href="../index.php">Strona główna</a></li>-->
                    <!--<li><a href="../userPanel">Panel użytkownika</a></li>-->
                    <!--<li><a href="../contact.php">Kontakt</a></li>-->
                <!--</ul>-->
                <img src="logo_small.png" style="float: left; margin-right: 20px;"></img><h3 class="muted">DziennikLogin</h3>
            </div>

            <hr>
            <table class="table table-hover">
                <tr><td>Baza danych</td><td><span class="glyphicon glyphicon-<?php echo $statuses['baza']; ?>"></span></td></tr>
                <tr><td>Dziennik elektroniczny</td><td><span class="glyphicon glyphicon-<?php echo $statuses['dziennik']; ?>"></span></td></tr>
            </table>
            <hr>
            <div class="footer">
                <p>&copy; Marcin Ławniczak 2013</p>
            </div>

        </div> <!-- /container -->

    </body>
</html>
