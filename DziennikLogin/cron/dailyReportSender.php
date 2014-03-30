<?php

require dirname(__FILE__).'/../classes/textReportGenerator.php';
require dirname(__FILE__).'/../config.local.php';
require dirname(__FILE__).'/../hooks/hookEmail.php';

$db_host = $CONF['databaseHost'];
$db_name = $CONF['databaseName'];
$db_username = $CONF['databaseUsername'];
$db_password = $CONF['databasePassword'];

//header('Content-Type: text/html; charset=utf-8');
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_username, $db_password, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    return 'Błąd bazy danych:' . $e->getMessage();
}

try {
    $stmt = $pdo->query('SELECT * FROM reportjobs WHERE reportType = \'DAILY\' AND isActive=1');
    $stmt->execute();
    $jobsArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

$reportGenerator = new textReportGenerator($CONF['databaseHost'], $CONF['databaseName'], $CONF['databaseUsername'], $CONF['databasePassword']);
$mailer = new emailSendHook($CONF['smtpUsername'], $CONF['smtpPassword']);

foreach ($jobsArray as $i) {
//    var_dump($i);
    $reportGenerator->setUserId($i['userId']);
    $reportGenerator->setReportTo($i['reportTo']);
    $reportGenerator->setReportType('DAILY');
    $reportGenerator->generateReport();
    $content = $reportGenerator->getReportContent();
    $emailArray[0] = $i['reportEmail'];
//    var_dump($reportGenerator->reportData);
//    echo $reportGenerator->reportContent;
    if($content != 'EMPTY'){
    $mailer->setData($emailArray[0], $reportGenerator->getReportContent());
    $mailer->sendEmail();
    }
    $reportGenerator->reset();
}
//echo $reportGenerator->getReportContent();
var_dump($jobsArray);
unset($pdo);
?>
