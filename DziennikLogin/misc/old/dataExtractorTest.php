<?php
require '../lib/dataDownloader.php';
//require '../lib/dataExtractorToDatabase.php';
require '../config.local.php';
require '../lib/simple_html_dom.php';
$db_host = $CONF['databaseHost'];
$db_name = $CONF['databaseName'];
$db_username = $CONF['databaseUsername'];
$db_password = $CONF['databasePassword'];


    $downloadedData = downloadData('marcin.lawniczak', 'ekosiarz');
    //extractDataToDatabaseNew($i['userId'], $dataDownloaded, $db_host, $db_name, $db_username, $db_password);
    
    $html = str_get_html($downloadedData);
    $table = $html->find('table', 4);
    
    var_dump($table->children());
?>
