<?php

require('reportGenerator.php');
require('DB.php');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of textReportGenerator
 *
 * @author marcin
 */
class textReportGenerator extends \DziennikLogin\classes\reportGenerator\reportGenerator {

    private $dbHandle;

    public function __construct($databaseHost, $databaseName, $databaseUsername, $databasePassword) {
        $this->dbHandle = new DB("mysql:host=$databaseHost;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ));
        $this->dbHandle->beginTransaction();
        $this->dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->dbHandle->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setReportTo($reportTo) {
        $this->reportTo = $reportTo;
    }

    public function setReportType($reportType) {
        $this->reportType = $reportType;
    }

    public function getDataToReport() {
        if ($this->reportType == 'DAILY') {
            $selectQuery = $this->dbHandle->prepare('SELECT *
                    FROM `grades`
                    INNER JOIN `subjects`
                    ON grades.subjectId = subjects.subjectId  
                    WHERE userId = :userId AND gradeShown = 0
                    ORDER BY grades.gradeValue DESC');
            $selectQuery->bindParam(':userId', $this->userId);
            $selectQuery->execute();
            if ($selectQuery->rowCount() === 0) {
                throw new Exception('Za mało ocen!');
            }
            $this->reportData = $selectQuery->fetchAll();
            //todo Change the gradeShown value in db
        } elseif ($this->reportType == 'FULL') {
            $selectQuery = $this->dbHandle->prepare('SELECT *
                    FROM `grades`
                    INNER JOIN `subjects`
                    ON grades.subjectId = subjects.subjectId  
                    WHERE userId = :userId 
                    ORDER BY grades.gradeValue DESC');
            $selectQuery->bindParam(':userId', $this->userId);
            $selectQuery->execute();
            if ($selectQuery->rowCount() === 0) {
                throw new Exception('Za mało ocen!');
            }
            $this->reportData = $selectQuery->fetchAll();
        } else {
            throw new Exception('Nieznany typ raportu!');
        }
    }

    public function generateReport() {
        $this->getDataToReport();
        if ($this->reportTo == 'PARENT') {

            $this->reportContent = "Witaj, \r\nPoniżej znajduja się oceny, które otrzymało Twoje dziecko.\r\nOceny uszeregowane są od najwyższej do najniższej.\r\n\r\n";
            foreach ($this->reportData as $i) {
                $this->reportContent .= 'Data: ' . $i['gradeDate'] . "\r\n";
                $this->reportContent .= 'Ocena: ' . $i['gradeValue'] . "\r\n";
                $this->reportContent .= 'Waga: ' . $i['gradeWeight'] . "\r\n";
                $this->reportContent .= 'Przedmiot: ' . $i['subjectName'] . "\r\n";
                $this->reportContent .= 'Tytuł: ' . $i['gradeAbbrev'] . ' - ' . htmlspecialchars_decode($i['gradeTitle']) . "\r\n\r\n";
            }
            $this->reportContent .= "Z poważaniem,\r\nDziennikLogin";
        } elseif ($this->reportTo == 'CHILD') {
            $this->reportContent = "Witaj, \r\nPoniżej znajduja się twoje oceny.\r\nOceny uszeregowane są od najwyższej do najniższej.\r\n\r\n";
            foreach ($this->reportData as $i) {
                $this->reportContent .= 'Data: ' . $i['gradeDate'] . "\r\n";
                $this->reportContent .= 'Ocena: ' . $i['gradeValue'] . "\r\n";
                $this->reportContent .= 'Waga: ' . $i['gradeWeight'] . "\r\n";
                $this->reportContent .= 'Przedmiot: ' . $i['subjectName'] . "\r\n";
                $this->reportContent .= 'Tytuł: ' . $i['gradeAbbrev'] . ' - ' . htmlspecialchars_decode($i['gradeTitle']) . "\r\n\r\n";
            }
            $this->reportContent .= "Z poważaniem,\r\nDziennikLogin";
        } else {
            throw new Exception('Nieznany odbiorca raportu');
        }
    }

}

?>
