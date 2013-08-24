<?php

namespace DziennikLogin\cron\dailyDataDownloader;

use \PDO;
use \Exception;
use \PDOException;

require '../classes/registerDataProcessor.php';
require '../classes/registerDataDownloader.php';
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
    $stmt = $pdo->query('SELECT * FROM users INNER JOIN registerPasswords on users.user_id=registerPasswords.userId');
    $stmt->execute();
    $usersArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

$keyContents = file_get_contents('private.key');
if (!$privateKey = openssl_pkey_get_private($keyContents))
    die('Private Key failed');

$downloader = new \DziennikLogin\classes\registerDataDownloader\registerDataDownloader;
$downloader->setCookieFilePath('cookie.txt');

$processor = new \DziennikLogin\classes\registerDataProcessor\registerDataProcessor;
$processor->setDatabaseConnectionData($db_host, $db_name, $db_username, $db_password);

foreach ($usersArray as $i) {
    $registerPassword = '';
    if (!openssl_private_decrypt($i['registerPassword'], $registerPassword, $privateKey))
        throw new \Exception('Failed to decrypt data');
    $downloader->setRegisterUsername($i['registerUsername']);
    $downloader->setRegisterPassword($registerPassword);
    $gradePageContent = $downloader->executeDownload();
    $processor->setGradePageContent($gradePageContent);
    $processor->setUserId($i['user_id']);
    $processor->executeProcessing();
    $processor->cleanDom();
    unset($registerPassword);
}
$downloader->__destruct();
unset($downloader);
unset($pdo);
openssl_free_key($privateKey);

?>

