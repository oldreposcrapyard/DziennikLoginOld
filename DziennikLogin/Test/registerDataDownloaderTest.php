<?php

namespace DziennikLogin\Test;

//require_once '../../classes/dataDownloader.php';
//use DziennikLogin;
use DziennikLogin\registerDataDownloader;

class registerDataDownloaderTest extends \PHPUnit_Framework_TestCase
{

    public $dataDownloaderObject = '';
    $this->dataDownloaderObject = new registerDataDownloader();

    public function testSetCurlPropertiesThrowsExceptionWhenNoCookiePathSet()
    {
        $this->dataDownloaderObject->executeDownload();
        $this->assertExpectedException(Exception);
    }
}

?>
