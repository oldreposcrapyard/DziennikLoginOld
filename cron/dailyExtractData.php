<?php

require '../lib/dataDownloader.php';
require '../lib/dataExtractorToDatabase.php';
require '../config.local.php';

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
} catch (PDOException $e) {
    echo $e->getMessage();
}

foreach ($usersArray as $i) {
    $dataDownloaded = downloadData($i['registerUsername'], $i['registerPassword']);
    extractDataToDatabase($i['userId'], $dataDownloaded, $db_host, $db_name, $db_username, $db_password);
}

?>
