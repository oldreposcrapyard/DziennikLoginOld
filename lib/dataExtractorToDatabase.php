<?php

// simple app to access e-dziennik grades through curl
// Marcin Ławniczak
// marcin.safmb@gmail.com
// github.com/marcinlawnik
//include library
require 'simple_html_dom.php';

function extractDataToDatabase($userId, $downloadedData, $db_host, $db_name, $db_username, $db_password) {
//$startTime = microtime(TRUE);
//---------------------------
// Database connection
//---------------------------
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_username, $db_password, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ));
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
    } catch (PDOException $e) {
        return 'Błąd bazy danych:' . $e->getMessage();
    }

//PARSING HTML:
//Lets get something to eat!
    $html = str_get_html($downloadedData);
//Count the subjects:
    $rowsEven = $html->find('table', 4)->find('.data_even');
    $countEven = count($rowsEven);
    $rowsOdd = $html->find('table', 4)->find('.data_odd');
    $countOdd = count($rowsOdd);
    $subjectsCount = $countOdd + $countEven; //18 for me
    $gradeTrimester = filter_var($html->find('b', 0)->plaintext, FILTER_SANITIZE_NUMBER_INT);;

//Here We Go!
    $i = 0;
    $x = 2; //cell offset, 0 is subject name, 1 is average
    while ($i < $subjectsCount) {
        $i++; //(0 is title row)
        //Hey, the subjectname:
        $subjectName = trim(str_replace('&nbsp;', '', $html->find('table', 4)->find('tr', $i)->find('td', 0)->plaintext));
        try {
            //put into database
            $queryHandleInsert = $pdo->prepare('INSERT INTO subjects SET subjectName=:subjectName');
            $queryHandleInsert->bindParam(':subjectName', $subjectName);
            $queryHandleInsert->execute();
            $subjectId = $pdo->lastInsertId('subjectId');
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                // duplicate entry, fetch the subject id
                $queryHandleSelect = $pdo->prepare('SELECT subjectId FROM subjects WHERE subjectName = :subjectName');
                $queryHandleSelect->bindParam(':subjectName', $subjectName);
                $queryHandleSelect->execute();
                $subjectIdQuery = $queryHandleSelect->fetch(PDO::FETCH_ASSOC);
                $subjectId = $subjectIdQuery['subjectId'];
            } else {
                // an error other than duplicate entry occurred
                return $e->getMessage();
            }
        }
        // The average ones get to eat too:
        if (!empty($html->find('table', 4)->find('tr', $i)->find('.cell-style-srednia', 0)->plaintext)) {
            $subjectAverage = trim($html->find('table', 4)->find('tr', $i)->find('.cell-style-srednia', 0)->plaintext);
        } else {
            //No food for ya!
            $subjectAverage = 'BRAK';
        }
        //Grades
        //Count the cells in the row:
        $gradesCount = count($html->find('table', 4)->find('tr', $i)->find('td'));
        //process each cell(grade)
        while ($x < $gradesCount) {
            $gradeCell = $html->find('table', 4)->find('tr', $i)->find('td', $x)->plaintext; //get the cell value
            $gradeAbbrev = strtoupper(trim(substr($gradeCell, 0, 3))); //grade abbreviation (the text from cell)
            $gradeValue = filter_var($gradeCell, FILTER_SANITIZE_NUMBER_INT); //grade numerical value (the number from cell)
            //See if it has +, add a 0,5 then
            $gradeValueHasPlus = strpos($gradeCell, '+');
            if ($gradeValueHasPlus !== FALSE) {
                $gradeValue = $gradeValue[0] + 0.5;
            }
            //Check if it contains numbers
            if (strcspn($gradeCell, '0123456789') != strlen($gradeCell)) {
                $gradeValueHasNumbers = TRUE;
            } else {
                $gradeValueHasNumbers = FALSE;
            }

            if (!empty($gradeCell) && $gradeValueHasNumbers) {
                $onmouseover = $html->find('table', 4)->find('tr', $i)->find('td', $x)->onmouseover;
                $mouseDom = str_get_html($onmouseover);
                //$gradeDate = ; //date of grade, needs to be converted
                $gradeDate=date('Y-m-d',strtotime($mouseDom->find('td', '1')->plaintext));
                $gradeTitle = $mouseDom->find('i', 1)->plaintext; //title of grade
                $gradeGroup = $mouseDom->find('p', '1')->plaintext; //group of grade
                $gradeWeight = trim($mouseDom->find('td', '3')->plaintext); //weight of grade
                $mouseDom->clear();
                unset($mouseDom); //free up resources
                if (strcspn($gradeWeight, '0123456789') == strlen($gradeWeight)) {//check if its really number, if not, then its 1
                    $gradeWeight = '1';
                } else {
                    $gradeWeight = round($gradeWeight);
                }
                //write data to database!
                /* * * prepare the SQL statement * * */
                try {
                    $stmt = $pdo->prepare("INSERT INTO `grades` (`userId`, `subjectId`,`gradeValue`, `gradeWeight`, `gradeGroup`, `gradeTitle`, `gradeDate`, `gradeAbbrev`, `gradeTrimester`, `gradeDownloadDate`, `gradeShown`) VALUES 
	                                                (:userId, :subjectId, :gradeValue, :gradeWeight, :gradeGroup, :gradeTitle, :gradeDate, :gradeAbbrev, :gradeTrimester, CURRENT_TIMESTAMP, :gradeShown);");

                    /*                     * * bind the paramaters ** */
                    $stmt->bindParam(':userId', $userId);
                    $stmt->bindParam(':subjectId', $subjectId);
                    $stmt->bindParam(':gradeValue', $gradeValue);
                    $stmt->bindParam(':gradeWeight', $gradeWeight);
                    $stmt->bindParam(':gradeGroup', $gradeGroup);
                    $stmt->bindParam(':gradeTitle', $gradeTitle);
                    $stmt->bindParam(':gradeDate', $gradeDate);
                    $stmt->bindParam(':gradeAbbrev', $gradeAbbrev);
                    $stmt->bindParam(':gradeTrimester', $gradeTrimester);
                    $stmt->bindParam(':gradeShown', $gradeShown = 0); //We haven't shown the grade yet
                    $stmt->execute();
                } catch (PDOException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        // duplicate entry, do nothing
                    } else {
                        // an error other than duplicate entry occurred
                        return
                        $e->getMessage();
                    }
                }
            }
            $x++;
        }
        $x = 2; //cell offset, 0 is subject name, 1 is average
    }
    //commit to database
    try {
            $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        return $e->getMessage();
    }


//Clean up after ourselves ;)
    $html->clear();
    unset($html);

//Close database connention
    try {
        $pdo = null;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
//Return output
    return 'Success!';
}

?>
