<?php
require '../classes/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

// Create the logger
$logger = new Logger('DziennikLoginLogger');
// Now add some handlers
$logger->pushHandler(new StreamHandler('../logs/error.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());

// You can now use your logger
$logger->addInfo('My logger is now ready');

?>