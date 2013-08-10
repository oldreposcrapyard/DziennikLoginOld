<?php

namespace DziennikLogin\Test;

use DziennikLogin\classes\registerDataDownloader;

class registerDataDownloaderTest extends \PHPUnit_Framework_TestCase
{

    public $dataDownloaderObject = '';
    
    public function testSetExecuteDownloadThrowsExceptionWhenNoUsernameSet(){
        $this->dataDownloaderObject = new registerDataDownloader();
        $this->setExpectedException('Exception', 'No username or password set!');
        $this->dataDownloaderObject->executeDownload();   
    }
    public function testSetExecuteDownloadThrowsExceptionWhenNoCookiePathSet()
    {
        $this->dataDownloaderObject = new registerDataDownloader();
        $this->dataDownloaderObject->setRegisterUsername('jan.kowalski');
        $this->dataDownloaderObject->setRegisterPassword('haslo12345');
        $this->setExpectedException('Exception', 'Cookie file path not set!');
        $this->dataDownloaderObject->executeDownload();
 
    }

}

?>
