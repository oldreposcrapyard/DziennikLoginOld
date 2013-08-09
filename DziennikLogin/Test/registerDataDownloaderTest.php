<?php

namespace DziennikLogin\Test;

//require_once '../../classes/dataDownloader.php';
//use DziennikLogin;
use DziennikLogin\registerDataDownloader;

class registerDataDownloaderTest extends \PHPUnit_Framework_TestCase {

    public function testCreateCurlHandleWorks() {

        //$foo = true;
        //$this->assertTrue($foo); 
        $dataDownloaderObject = new registerDataDownloader();
        $classErrors = $dataDownloaderObject->getErrors();
        $this->assertFalse($classErrors);
    }
/*
    public function testTrueIsTrue() {

        $foo = true;
        $this->assertTrue($foo); 
        
    }
*/
}

?>
