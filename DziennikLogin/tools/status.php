<?php
require '../classes/autoload.php';
require '../config.local.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
        } catch (PDOException $e) {
            $logger->addInfo('The database is NOT working');
            $logger->addInfo($e->getMessage());
       }





?>