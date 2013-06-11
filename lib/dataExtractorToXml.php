<?php
// simple app to access e-dziennik grades through curl
// Marcin Ławniczak
// marcin.safmb@gmail.com
// github.com/marcinlawnik

//include library
require 'simple_html_dom.php';

function extractDataToXml($downloadedData){
$startTime = microtime(TRUE);
//XML
//create the xml document
$xmlDoc = new DOMDocument('1.0', 'UTF-8');

//create the root element
$root = $xmlDoc->appendChild(
          $xmlDoc->createElement("registerGrades"));
//create the subjects element
$subjectsElement = $root->appendChild(
          $xmlDoc->createElement("registerSubjects"));
       
//make the output pretty
$xmlDoc->formatOutput = true;
//END XML

//PARSING HTML:
//Lets get something to eat!
$html          = str_get_html($downloadedData);
//Count the subjects:
$rowsEven      = $html->find('table', 4)->find('.data_even');
$countEven     = count($rowsEven);
$rowsOdd       = $html->find('table', 4)->find('.data_odd');
$countOdd      = count($rowsOdd);
$subjectsCount = $countOdd + $countEven; //18 for me

//Here We Go!
$i = 0;
$x = 2; //cell offset, 0 is subject name, 1 is average
while ($i < $subjectsCount) {
    $i++; //(0 is title row)
    // XML:create a subject element
    $subjectElement = $subjectsElement->appendChild($xmlDoc->createElement("subject"));
    //Hey, the subjectname:
    $subjectName = $html->find('table', 4)->find('tr', $i)->find('td', 0)->plaintext; //$html->find('ul', 0)->find('li', 0);
    $subjectName = trim(str_replace('&nbsp;','',$subjectName));//Get rid of that nasty NBSP and whitespace
    // XML:create a subjectName attribute
    $subjectElement->appendChild($xmlDoc->createAttribute("subjectName"))->appendChild($xmlDoc->createTextNode($subjectName));
    // The average ones get to eat too:
    if (!empty($html->find('table', 4)->find('tr', $i)->find('.cell-style-srednia', 0)->plaintext)) {
        $subjectAverage = trim($html->find('table', 4)->find('tr', $i)->find('.cell-style-srednia', 0)->plaintext);
    } else {
        //No food for ya!
        $subjectAverage = 'BRAK';
    }
    // XML:create a subjectAverage element
    $subjectAverageElement = $subjectElement->appendChild($xmlDoc->createElement("average",$subjectAverage));
    //Grades
    //Count the cells in the row:
    $gradesCount  = count($html->find('table', 4)->find('tr', $i)->find('td'));
    //process each cell(grade)
    while ($x < $gradesCount) {
        $gradeCell = $html->find('table', 4)->find('tr', $i)->find('td', $x)->plaintext; //get the cell value
        $gradeAbbrev = strtoupper(trim(substr ($gradeCell ,0 ,3)));//grade abbreviation (the text from cell)
        $gradeValue = filter_var($gradeCell, FILTER_SANITIZE_NUMBER_INT);//grade numerical value (the number from cell)
        //Check if it contains numbers
        if (strcspn($gradeCell, '0123456789') != strlen($gradeCell)) {
            $gradeValueHasNumbers = TRUE;
        } else {
            $gradeValueHasNumbers = FALSE;
        }
        
        if (!empty($gradeCell) && $gradeValueHasNumbers) {
            $onmouseover = $html->find('table', 4)->find('tr', $i)->find('td', $x)->onmouseover;
            $mouseDom    = str_get_html($onmouseover);
            $gradeDate   = $mouseDom->find('td', '1')->plaintext; //date of grade
            $gradeTitle  = $mouseDom->find('i', 1)->plaintext; //title of grade
            $gradeGroup  = $mouseDom->find('p', '1')->plaintext; //group of grade
            $gradeWeight = trim($mouseDom->find('td', '3')->plaintext); //weight of grade
            $mouseDom->clear();
            unset($mouseDom);//free up resources
            if(strcspn($gradeWeight, '0123456789') == strlen($gradeWeight)){//check if its really number, if not, then its 1
            $gradeWeight = '1,00';
            }

            //write data to XML file!
            // XML:create a grade element
            $grade = $subjectElement->appendChild($xmlDoc->createElement("grade"));
            // XML:create a gradeValue element/child
            $gradeValueElement = $grade->appendChild($xmlDoc->createElement("gradeValue",$gradeValue));

            // XML:create a gradeWeight element/child
            $gradeWeightElement = $grade->appendChild($xmlDoc->createElement("gradeWeight",$gradeWeight));

            // XML:create a gradeAbbrev element/child
            $gradeAbbrevElement = $grade->appendChild($xmlDoc->createElement("gradeAbbrev",$gradeAbbrev));

            // XML:create a gradeDate element/child
            $gradeDateElement = $grade->appendChild($xmlDoc->createElement("gradeDate",$gradeDate));

            // XML:create a gradeTitle element/child
            $gradeTitleElement = $grade->appendChild($xmlDoc->createElement("gradeTitle",$gradeTitle));

            // XML:create a gradeGroup element/child
            $gradeGroupElement = $grade->appendChild($xmlDoc->createElement("gradeGroup",$gradeGroup));
            
        }
        $x++;
    }
    $x = 2; //cell offset, 0 is subject name, 1 is average
}
//Clean up after ourselves ;)
$html->clear();
unset($html);

//TIME
$endTime = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$getExecutionTime = ($endTime - $startTime);

// XML:create a executionTime element
$executionTime = $root->appendChild($xmlDoc->createElement('executionTime',$getExecutionTime));
//Return output xml
return $xmlDoc->saveXML();
}
?>
