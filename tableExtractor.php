<?php
$time_start = microtime(true);
error_reporting(E_ALL);
//include library
require 'simple_html_dom.php';

//Define encoding
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
//INPUT!
$file          = '25aee6451cf293f85f56e806815a0f15_grades_1367928713.html';//EXAMPLEDATA.html
//Lets get something to eat!
$html          = file_get_html($file);
//Count the subjects:
$rowsEven      = $html->find('table', 4)->find('.data_even');
$countEven     = count($rowsEven);
$rowsOdd       = $html->find('table', 4)->find('.data_odd');
$countOdd      = count($rowsOdd);
$subjectsCount = $countOdd + $countEven; //18

echo "ILOSC PRZEDMIOTOW:$subjectsCount<br>"; //Echo, echo :p

//Here We Go!
$i = 0;
$x = 2; //cell offset, 0 is subject name, 1 is average
while ($i < $subjectsCount) {
    $i++; //(0 is title row)   
    //The number:
    echo 'NUMBER:'.$i;
    //Hey, the subjectname:
    $subjectName = $html->find('table', 4)->find('tr', $i)->find('td', 0)->plaintext; //$html->find('ul', 0)->find('li', 0);
    $subjectName = trim(str_replace('&nbsp;','',$subjectName));//Get rid of that nasty NBSP and whitespace
    echo 'SUBJECTNAME:'.'"'.$subjectName.'"';
    // The average ones get to eat too:
    if (!empty($html->find('table', 4)->find('tr', $i)->find('.cell-style-srednia', 0)->plaintext)) {
        $subjectAverage = trim($html->find('table', 4)->find('tr', $i)->find('.cell-style-srednia', 0)->plaintext);
    } else {
        //No food for ya!
        $subjectAverage = 'BRAK';
    }
    echo 'AVERAGE:"'.$subjectAverage.'"' . '<br>';
    //Grades
    //Count the cells in the row:
    $gradesNumber = $html->find('table', 4)->find('tr', $i)->find('td');
    $gradesCount  = count($gradesNumber);
    //process each cell
    while ($x < $gradesCount) {
        $found = $html->find('table', 4)->find('tr', $i)->find('td', $x)->plaintext; //get the pure text 
        $abbrev = trim(substr ($found ,0 ,3));//grade abbreviation
        $found = filter_var($found, FILTER_SANITIZE_NUMBER_INT);//get the number
        //Check if it contains numbers
        if (strcspn($found, '0123456789') != strlen($found)) {
            $hasNumbers = TRUE;
        } else {
            $hasNumbers = FALSE;
        }
        
        if (!empty($found)) {
            $onmouseover = $html->find('table', 4)->find('tr', $i)->find('td', $x)->onmouseover;
            $mouseDom    = str_get_html($onmouseover);
            //If it has numbers
            if ($hasNumbers == TRUE) {
                $title  = $mouseDom->find('i', 1)->plaintext; //title of grade
                $group  = $mouseDom->find('p', '1')->plaintext; //group of grade
                $weight = trim($mouseDom->find('td', '3')->plaintext); //weight of grade
                if(strcspn($weight, '0123456789') != strlen($weight)){//check if its really number that we want
                }
                else{
                $weight = '1,00';
                }
                $date   = $mouseDom->find('td', '1')->plaintext; //date of grade
                
                echo '<br>GRADE:"'.$found.'"'.'<br>TITLE:"'.$title.'"'.'<br>GROUP:"'.$group.'"'.'<br>WEIGHT:"'.$weight.'"'.'<br>DATE:"'.$date.'"<br>'.'ABBREV:'.'"'.$abbrev.'"'.'<br><br>';
            } else { //Then if it doesn't:
                //$hasNumbers = FALSE;
            }
            
        } else { //do nothing,empty cell
        }
        $x++;
    }
    $x = 2; //cell offset, 0 is subject name, 1 is average
}
//Clean up after ourselves ;)
$html->clear();
unset($html);

//TIME
$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<b>Total Execution Time:</b> ' . $execution_time . ' Secs';

?>
