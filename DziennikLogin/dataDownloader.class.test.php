<?php

include 'registerDataDownloader.php';
try{
$dataDownloader = new \DziennikLogin\registerDataDownloader('cookie.txt');
//$dataDownloader->setCookieFilePath('cookie.txt');
$dataDownloader->setRegisterUsername('marcin.lawniczak');
$dataDownloader->setRegisterPassword('ekosiarz');
echo $dataDownloader->executeDownload();
echo '<hr>';
$dataDownloader->setRegisterUsername('jan.sliwinski');
$dataDownloader->setRegisterPassword('gryfindor');
echo $dataDownloader->executeDownload();
}
 catch (Exception $e){
    echo $e->getMessage();
 }
?>
