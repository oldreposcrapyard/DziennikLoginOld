<?php
require '../classes/graphReportGenerator.php';
require '../config.local.php';

$chart=new graphReportGenerator($CONF['databaseHost'], $CONF['databaseName'], $CONF['databaseUsername'], $CONF['databasePassword']);
$chart->setUserId(15);
$chart->generateReport();

?>
