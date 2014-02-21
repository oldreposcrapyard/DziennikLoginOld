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
//check e-dziennik availability
       //returns true, if domain is availible, false if not
       function isDomainAvailible($domain)
       {
               //check, if a valid url is provided
               if(!filter_var($domain, FILTER_VALIDATE_URL))
               {
                       return false;
               }

               //initialize curl
               $curlInit = curl_init($domain);
                           //Ignore the SSL communication, because the certificate is outdated
            curl_setopt($curlInit, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curlInit, CURLOPT_SSL_VERIFYHOST, FALSE);
               curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
               curl_setopt($curlInit,CURLOPT_HEADER,true);
               curl_setopt($curlInit,CURLOPT_NOBODY,true);
               curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

               //get answer
               $response = curl_exec($curlInit);

               curl_close($curlInit);

               if ($response) return true;

               return false;
       }
       if (isDomainAvailible('https://92.55.225.11'))
       {
               $logger->addInfo('The e-dziennik is working');
       }
       else
       {
               $logger->addInfo('The e-dziennik is NOT working');
       }



?>