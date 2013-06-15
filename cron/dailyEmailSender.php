<?php

require '../hooks/hookEmail.php';
require '../config.local.php';
require '../lib/reportGenerator.php';

$db_host = $CONF['databaseHost'];
$db_name = $CONF['databaseName'];
$db_username = $CONF['databaseUsername'];
$db_password = $CONF['databasePassword'];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_username, $db_password, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    return 'Błąd bazy danych:' . $e->getMessage();
}

try {
    $stmt = $pdo->query('SELECT * FROM users');
    $stmt->execute();
    $usersArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($usersArray);
} catch (PDOException $e) {
    echo $e->getMessage();
}

foreach ($usersArray as $i) {
    $report = generateReport($i['userId'], 'FULL_TEXT_PARENT', $pdo);
    sendEmailWithGrades('marcin.safmb@gmail.com', $report,FALSE,'');//Get the email from database
    echo 'SUCCESS';
}
?>
