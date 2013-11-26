<?php

//begin work on api for mobile and desktop apps
//end of class
require 'config.local.php';

$db_host = $CONF['databaseHost'];
$db_name = $CONF['databaseName'];
$db_username = $CONF['databaseUsername'];
$db_password = $CONF['databasePassword'];

$chartGenerator = new graphReportGenerator();

$chartGenerator->setDatabaseConnectionData($db_host, $db_name, $db_username, $db_password);
$chartGenerator->setUserId(2);
$chartGenerator->executeProcessing();
?>
