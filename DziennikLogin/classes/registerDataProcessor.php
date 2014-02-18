<?php

namespace DziennikLogin\classes\registerDataProcessor;

use \PDO;

use \Exception;

use \PDOException;

require 'simple_html_dom.php';

/**
 * Parse the data from register
 *
 * This class parses the data from register to arrays,
 * so it can be written to database.
 *
 * @author Marcin Ławniczak <marcin.safmb@gmail.com>
 * @package DziennikLogin
 * @version 0.1
 * @return string|false
 *
 * 
 */
class registerDataProcessor {

    /**
     * The content of the grade page to parse
     * @var string
     */
    private $registerGradePageContent = null;

    /**
     * The array with all subjects
     * @var array
     */
    public $registerSubjectsArray;

    /**
     * The handle to PDO object
     * @var resource
     */
    private $pdoHandle;

    /**
     * The object containing the grade table cells
     * @var object
     */
    private $registerGradeTableCells;

    /**
     * The DOM object with grade page
     * @var object
     */
    private $registerGradePageDomObject;

    /**
     * The trimester of the grades
     * @var string
     */
    public $gradeTrimester;

    /**
     * The ID of currently processed user
     * @var string
     */
    private $currentUserId;

    /**
     * Self-explanatory
     * @var string
     */
    private $databaseHost;

    /**
     * Self-explanatory
     * @var string
     */
    private $databaseName;

    /**
     * Self-explanatory
     * @var string
     */
    private $databaseUsername;

    /**
     * Self-explanatory
     * @var string
     */
    private $databasePassword;
    private $gradeInsertQuery;

    public function __construct($gradePageContent = null) {
        if (!is_null($gradePageContent) && $gradePageContent != '') {
            $this->registerGradePageContent = $gradePageContent;
        }
    }

    public function setUserId($userId) {
        $this->currentUserId = $userId;
    }

    public function setGradePageContent($gradePageContent) {
        $this->registerGradePageContent = $gradePageContent;
    }

    public function setDatabaseConnectionData($databaseHost, $databaseName, $databaseUsername, $databasePassword) {
        $this->databaseHost = $databaseHost;
        $this->databaseName = $databaseName;
        $this->databaseUsername = $databaseUsername;
        $this->databasePassword = $databasePassword;
    }

    private function connectToDatabase() {
        try {
            $this->pdoHandle = new \PDO("mysql:host=$this->databaseHost;dbname=$this->databaseName;charset=utf8", $this->databaseUsername, $this->databasePassword, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ));
            $this->pdoHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdoHandle->beginTransaction();
        } catch (PDOException $e) {
            throw new Exception('Błąd bazy danych:' . $e->getMessage());
        }
    }

    private function createSubjectArray() {
        //2. Powinieneś najpierw spróbować pobrać za jednym zamachem wszystkie ID przedmiotów na podstawie ich nazw.
        //Dopiero w następnym kroku utworzyć nowe rekordy w bazie dla nieistniejących jeszcze przedmiotów.
        //get all subjects from db
        try {
            $queryHandleSelect = $this->pdoHandle->prepare('SELECT subjectId,subjectName FROM subjects');
            $queryHandleSelect->execute();
            $subjectIds = $queryHandleSelect->fetchAll(PDO::FETCH_ASSOC);
            if (!is_array($subjectIds)) {
                $subjectIds = array();
            }
        } catch (PDOException $e) {
            throw new Exception('Błąd bazy danych:' . $e->getMessage());
        }
        //Convert array from database
        $this->registerSubjectsArray = array();
        foreach ($subjectIds as $val) {
            $this->registerSubjectsArray[$val['subjectId']] = $val['subjectName'];
        }
    }

    private function createSimpleHtmlDomObject() {
        //Lets get something to eat!
        $this->registerGradePageDomObject = str_get_html($this->registerGradePageContent);
    }

    private function getGradeCellsFromGradePage() {
        $table = $this->registerGradePageDomObject->find('table', 4);
        $this->registerGradeTableCells = $table->find('tr td');
    }

    public function cleanDom() {
        //Clean up after ourselves ;)
        //$this->registerGradePageDomObject->clear();
        unset($this->registerGradePageDomObject);
    }

    private function commitToDatabase() {
        //commit to database
        try {
            $this->pdoHandle->commit();
        } catch (PDOException $e) {
            $this->pdoHandle->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    private function getGradeTrimester() {
        //trimester:
        $gradeTrimester = $this->registerGradePageDomObject->find('b', 1)->plaintext;
        if (strstr($gradeTrimester,'I ')){
            $this->gradeTrimester = 1;
        }elseif(strstr($gradeTrimester,'II ')){
            $this->gradeTrimester = 2;
        }elseif(strstr($gradeTrimester,'III ')){
            $this->gradeTrimester = 3;
        }else{
            $this->gradeTrimester = 9;
        }
        //$this->gradeTrimester = filter_var($gradeTrimester, FILTER_SANITIZE_NUMBER_INT);
    }

    private function processGrades() {
        //1. Zapytania możesz przygotować ($pdo->prepare) raz, przed pętlą, i używać ich później wielokrotnie w pętli.
        /*         * * prepare the SQL statement * * */
        $this->gradeInsertQuery = $this->pdoHandle->prepare("INSERT INTO `grades` (`userId`, `subjectId`,`gradeValue`, `gradeWeight`, `gradeGroup`, `gradeTitle`, `gradeDate`, `gradeAbbrev`, `gradeTrimester`, `gradeDownloadDate`, `gradeShown`) VALUES 
	                                                (:userId, :subjectId, :gradeValue, :gradeWeight, :gradeGroup, :gradeTitle, :gradeDate, :gradeAbbrev, :gradeTrimester, CURRENT_TIMESTAMP, :gradeShown);");

        $subjectId = '';
        //the processing itself
        foreach ($this->registerGradeTableCells as $cell) {
            //switch depending on the type of the cell: subjectname, average or grade cell
            //in that order, or else average is detected as grade
            if (!isset($cell->class) && !isset($cell->onmouseover) && $cell->plaintext != '') {//subjectname: has text inside and no attributes
                //echo 'subjectname:' . ' ' . $cell->plaintext;
                $subjectName = trim(str_replace('&nbsp;', '', $cell->plaintext));
                //search for subject
                //Check if subject exists in $subjectIdsNew.
                $subjectSearch = in_array($subjectName, $this->registerSubjectsArray);
                //}
                if ($subjectSearch === FALSE) { //No subject found, must insert
                    try {
                        $queryHandleInsert = $this->pdoHandle->prepare('INSERT INTO subjects SET subjectName=:subjectName');
                        $queryHandleInsert->bindParam(':subjectName', $subjectName);
                        $queryHandleInsert->execute();
                        //then, set the subjectId according to the subject processed now
                        $subjectId = $this->pdoHandle->lastInsertId('subjectId');
                        // and  add to the array
                        $this->registerSubjectsArray[$subjectId] = $subjectName;
                    } catch (PDOException $e) {
                        if ($e->errorInfo[1] == 1062) {
                            // duplicate entry, do nothing
                        } else {
                            echo $e->getMessage();
                        }
                    }
                } else {
                    $subjectId = array_search($subjectName, $this->registerSubjectsArray);
                }
            } elseif ($cell->class == 'cell-style-srednia') {//average: class: cell-style-srednia
                //echo'srednia:' . ' ' . $cell->plaintext;
                //do nothing, we dont yet have an use for it
            } elseif (strcspn($cell->plaintext, '0123456789') != strlen($cell->plaintext)) {//grade: has number inside and isnt empty
                try {
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
                        $gradeTitle = 'BRAK OPISU OCENY'; //dirty hack around those lazy teachers that don't set the title
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

                    /* bind the paramaters */
                    $this->gradeInsertQuery->bindParam(':userId', $this->currentUserId);
                    $this->gradeInsertQuery->bindParam(':subjectId', $subjectId);
                    $this->gradeInsertQuery->bindParam(':gradeValue', $gradeValue);
                    $this->gradeInsertQuery->bindParam(':gradeWeight', $gradeWeight);
                    $this->gradeInsertQuery->bindParam(':gradeGroup', $gradeGroup);
                    $this->gradeInsertQuery->bindParam(':gradeTitle', $gradeTitle);
                    $this->gradeInsertQuery->bindParam(':gradeDate', $gradeDate);
                    $this->gradeInsertQuery->bindParam(':gradeAbbrev', $gradeAbbrev);
                    $this->gradeInsertQuery->bindParam(':gradeTrimester', $this->gradeTrimester);
                    $this->gradeInsertQuery->bindParam(':gradeShown', $gradeShown = 0); //We haven't shown the grade yet


                    $this->gradeInsertQuery->execute(); //execute
                } catch (PDOException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        // duplicate entry, do nothing
                    } else {
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
    }

    public function executeProcessing() {
        if ($this->registerGradePageContent == '') {
            throw new Exception('No grade page content set!');
        }
        $this->connectToDatabase();
        $this->createSimpleHtmlDomObject();
        $this->getGradeTrimester();
        $this->createSubjectArray();
        $this->getGradeCellsFromGradePage();
        $this->processGrades();
        $this->commitToDatabase();
        $this->cleanDom();
    }

}

?>
