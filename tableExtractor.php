<?php
$time_start = microtime(true); 
error_reporting(E_ALL);
//include library
require 'simple_html_dom.php';

// policzyc ilosc komorek i sprawdzic ktore sa z ocenami, tylko te pokazac
/*function getGrades($file,$rowNumber){//takes document as argument and row number (0 is title row)
$html = file_get_html($file);
if(!empty($html->find('table',4)->find('tr',$rowNumber)->find('span',0)->plaintext)){//$html->find('ul', 0)->find('li', 0);
$row = $html->find('table',4)->find('tr',$rowNumber)->find('span',0)->plaintext;
return $row;
}
else{
return ' BRAK';
}


$html->clear();
unset($html);
}
*/


//Define encoding
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
//FILE!
$file='EXAMPLEDATA.html';
//Lets get something to eat!
$html = file_get_html($file);
//Count the subjects:
$rowsEven = $html->find('table',4)->find('.data_even');
$countEven = count($rowsEven);
$rowsOdd = $html->find('table',4)->find('.data_odd');
$countOdd = count($rowsOdd);
$subjectsCount = $countOdd + $countEven; //18

echo "ILOSC PRZEDMIOTOW:$subjectsCount<br>";//Echo, echo :p

//Here We Go!
$i=0;
while ($i < $subjectsCount) {
    $i++;//(0 is title row)   
    //The number:
    echo 'NUMBER:'.$i.' ';
    //Hey, the subjectname:
    $subjectName = $html->find('table',4)->find('tr',$i)->find('td',0)->plaintext;//$html->find('ul', 0)->find('li', 0);
    echo "SUBJECTNAME:$subjectName ";
    // The average ones get to eat too:
    if(!empty($html->find('table',4)->find('tr',$i)->find('.cell-style-srednia',0)->plaintext)){
    $average = $html->find('table',4)->find('tr',$i)->find('.cell-style-srednia',0)->plaintext;
    }
    else{
    //No food for ya!
    $average = ' BRAK';
    }
    echo "AVERAGE:$average".'<br>';
    //$subjectGrades=getGrades($file,$i);
    //echo "GRADES:$subjectGrades ";

}
//Clean up after ourselves ;)
$html->clear();
unset($html);

//TIME
$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Secs';

?>
