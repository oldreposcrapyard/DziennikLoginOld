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
//get all users from database
try {
    $stmt = $pdo->query('SELECT * FROM users');
    $stmt->execute();
    $usersArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($usersArray);
} catch (PDOException $e) {
    echo $e->getMessage();
}
var_dump($usersArray);
//get the user emails (the user can have more than one email)
try {
    $stmt2 = $pdo->query('SELECT * FROM emails');
    $stmt2->execute();
    $emailsArray = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($usersArray);
} catch (PDOException $e) {
    echo $e->getMessage();
}
//convert the array
//    $emailsArrayNew = array();
//   foreach ($emailsArray as $val) {
//      $emailsArrayNew[$val['userId']] = $val['userEmail'];
//   }

var_dump($emailsArray);

foreach ($usersArray as $i) {
    $report = generateReport($i['user_id'], 'NEW_TEXT_PARENT', $pdo);
    //create per-user email array
    $userEmailArray = array();
    foreach ($emailsArray as $email) {
        if ($email['userId'] == $i['user_id']) {
            $userEmailArray[] .= $email['userEmail'];
        }
    } 
    var_dump($userEmailArray);
    //send emails
    foreach ($userEmailArray as $email) {
        sendEmailWithGrades($CONF['smtpUsername'], $CONF['smtpPassword'], $email, $report, FALSE, ''); //Get the email from array
        echo 'SUCCESS';
    }

    unset($userEmailArray);
}
?>
