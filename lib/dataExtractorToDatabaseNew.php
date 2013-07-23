<?php

// simple app to access e-dziennik grades through curl
// Marcin Ławniczak
// marcin.safmb@gmail.com
// github.com/marcinlawnik
//include library
require 'simple_html_dom.php';

function extractDataToDatabaseNew($userId, $downloadedData, $db_host, $db_name, $db_username, $db_password) {
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
    //Lets get something to eat!
    $html = str_get_html($downloadedData);
    $table = $html->find('table', 4);
    $cells = $table->find('tr td');
    //trimester:
    $gradeTrimester = filter_var($html->find('b', 0)->plaintext, FILTER_SANITIZE_NUMBER_INT);

    //2. Powinieneś najpierw spróbować pobrać za jednym zamachem wszystkie ID przedmiotów na podstawie ich nazw.
    //Dopiero w następnym kroku utworzyć nowe rekordy w bazie dla nieistniejących jeszcze przedmiotów.
    //get all subjects from db
    try {
        $queryHandleSelect = $pdo->prepare('SELECT subjectId,subjectName FROM subjects');
        $queryHandleSelect->execute();
        $subjectIds = $queryHandleSelect->fetchAll(PDO::FETCH_ASSOC);
        if (!is_array($subjectIds)) {
            $subjectIds = array();
        }
    } catch (PDOException $e) {
        return $e->getMessage();
    }
    //Convert array from database
    $subjectIdsNew = array();
    foreach ($subjectIds as $val) {
        $subjectIdsNew[$val['subjectId']] = $val['subjectName'];
    }
    //1. Zapytania możesz przygotować ($pdo->prepare) raz, przed pętlą, i używać ich później wielokrotnie w pętli.
    /*     * * prepare the SQL statement * * */
    $stmt = $pdo->prepare("INSERT INTO `grades` (`userId`, `subjectId`,`gradeValue`, `gradeWeight`, `gradeGroup`, `gradeTitle`, `gradeDate`, `gradeAbbrev`, `gradeTrimester`, `gradeDownloadDate`, `gradeShown`) VALUES 
	                                                (:userId, :subjectId, :gradeValue, :gradeWeight, :gradeGroup, :gradeTitle, :gradeDate, :gradeAbbrev, :gradeTrimester, CURRENT_TIMESTAMP, :gradeShown);");

    $subjectId = '';
    //the processing itself
    foreach ($cells as $cell) {
        //switch depending on the type of the cell: subjectname, average or grade cell
        //in that order, or else average is detected as grade
        if (!isset($cell->class) && !isset($cell->onmouseover) && $cell->plaintext != '') {//subjectname: has text inside and no attributes
            //echo 'subjectname:' . ' ' . $cell->plaintext;
            $subjectName = trim(str_replace('&nbsp;', '', $cell->plaintext));
            //search for subject
            //Check if subject exists in $subjectIdsNew.
            $subjectSearch = in_array($subjectName, $subjectIdsNew);
            //}
            if ($subjectSearch === FALSE) { //No subject found, must insert
                try {
                    $queryHandleInsert = $pdo->prepare('INSERT INTO subjects SET subjectName=:subjectName');
                    $queryHandleInsert->bindParam(':subjectName', $subjectName);
                    $queryHandleInsert->execute();
                    //then, set the subjectId according to the subject processed now
                    $subjectId = $pdo->lastInsertId('subjectId');
                    // and  add to the array
                    $subjectIdsNew[$subjectId] = $subjectName;
                } catch (PDOException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        // duplicate entry, do nothing
                    } else {
                        echo $e->getMessage();
                    }
                }
            } else {
                $subjectFoundSearch = array_search($subjectName, $subjectIdsNew);
                $subjectId = $subjectFoundSearch;
            }
        } elseif ($cell->class == 'cell-style-srednia') {//average: class: cell-style-srednia
            //echo'srednia:' . ' ' . $cell->plaintext;
            //do nothing, we dont yet have an use for it
        } elseif (strcspn($cell->plaintext, '0123456789') != strlen($cell->plaintext)) {//grade: has number inside and isnt empty
            //get all the needed values
            $gradeAbbrev = strtoupper(trim(substr($cell->plaintext, 0, 3))); //grade abbreviation (the text from cell)
            $gradeValue = filter_var($cell->plaintext, FILTER_SANITIZE_NUMBER_INT); //grade numerical value (the number from cell)
            //See if it has +, add a 0,5 then
            if (strpos($cell->plaintext, '+') !== FALSE) {
                $gradeValue = $gradeValue[0] + 0.5;
            }
            //now we need to dive into the onmouseover attribute
            $mouseDom = str_get_html($cell->onmouseover);
            $gradeDate = date('Y-m-d', strtotime($mouseDom->find('td', '1')->plaintext)); //date of grade
            @$gradeTitle = $mouseDom->find('i', '1')->plaintext; //title of grade
            if (is_null($gradeTitle)) {
                $gradeTitle = 'BRAK'; //dirty hack around those lazy teachers that don't set the title
            }
            $gradeGroup = $mouseDom->find('p', '1')->plaintext; //group of grade
            $gradeWeight = trim($mouseDom->find('td', '3')->plaintext); //weight of grade
            //free up resources
            $mouseDom->clear();
            unset($mouseDom);
            if (strcspn($gradeWeight, '0123456789') == strlen($gradeWeight)) {//check if gradeWeight is really a number, if not, then its 1
                $gradeWeight = '1';
            } else {
                $gradeWeight = round($gradeWeight);
            }
            //insert into database
            try {
                /*                 * * bind the paramaters * * */
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
                $stmt->execute(); //execute
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    // duplicate entry, do nothing
                } else {
                    echo $e->getMessage();
                }
            }
        }
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
}
?>