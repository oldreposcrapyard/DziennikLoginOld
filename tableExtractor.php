<?php
$time_start = microtime(true); 
error_reporting(E_ALL);
//include library
require 'simple_html_dom.php';

//policz przedmioty
function countSubjects($file){//takes document as argument
$html = file_get_html($file);
$rowsEven = $html->find('table',4)->find('.data_even');
$countEven = count($rowsEven);
$rowsOdd = $html->find('table',4)->find('.data_odd');
$countOdd = count($rowsOdd);
$count = $countOdd + $countEven;
return $count;
$html->clear();
unset($html);
}
//nazwa przedmiotu
function getSubjectName($file,$rowNumber){//takes document as argument and row number (0 is title row)
$html = file_get_html($file);
$row = $html->find('table',4)->find('tr',$rowNumber)->find('td',0);//$html->find('ul', 0)->find('li', 0);
return $row;
$html->clear();
unset($html);
}

//Define encoding
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
//PLIK
$file='EXAMPLEDATA.html';
$subjectsCount = countSubjects($file);//policz przedmioty - 18
echo "ILOSC PRZEDMIOTOW:$subjectsCount<br>";//wyswietl

//list subjects
$i=0;
while ($i < $subjectsCount) {
    $i++;//(0 is title row)
    $subjectName=getSubjectName($file,$i);
    echo "SUBJECTNAME:$subjectName";
}

//TIME
$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Secs';

?>
