<?php
require '../classes/graphReportGenerator.php';
require '../config.misc.php';

$chart=new graphReportGenerator($CONF['databaseHost'], $CONF['databaseName'], $CONF['databaseUsername'], $CONF['databasePassword']);
$chart->setUserId(15);
$chart->generateReport();

?>
