<?php
//An index file will be here
include 'lib/dataDownloader.php';
include 'lib/dataExtractor.php';
include 'lib/reportGenerator.php';

if (isset($_GET['username']) && isset($_GET['password'])) {
	$dataDownloaded = downloadData($_GET['username'],$_GET['password']);
} else {
    $dataDownloaded = FALSE;	
}


if($dataDownloaded != FALSE){

$pureXML=extractData($dataDownloaded);

}
else {
	echo 'Failed to download data!';
}

$report = generateReport($pureXML,'FULL_TEXT');
echo '<pre>';
echo $report;
echo "</pre>";
?>