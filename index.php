<?php
//An index file will be here
include 'lib/dataDownloader.php';
include 'lib/dataExtractorToDatabase.php';
//include 'lib/reportGenerator.php';

if (isset($_GET['username']) && isset($_GET['password'])) {
	$dataDownloaded = downloadData($_GET['username'],$_GET['password']);
} else {
    $dataDownloaded = FALSE;	
}


if($dataDownloaded != FALSE){

$pureXML= extractDataToDatabase(1, $dataDownloaded, 'localhost', 'DziennikLogin', 'marcin', 'some_pass');

}
else {
	echo 'Failed to download data!';
}

//$report = generateReport($pureXML,'FULL_TEXT');
//echo '<pre>';
echo $pureXML;
//echo "</pre>";
?>