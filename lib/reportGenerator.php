<?php

//In this file we will generate a report to be sent over various hooks

function generateReport($userId, $reportType, $pdo) {

//---------------------------
// Database connection
//---------------------------
    switch ($reportType) {
        case 'FULL_TEXT_PARENT':
            try {
                $stmt = $pdo->prepare("SELECT *
                    FROM `grades`
                    INNER JOIN `subjects`
                    ON grades.subjectId = subjects.subjectId  
                    WHERE userId = :userId 
                    ORDER BY grades.gradeValue DESC");

                /*                 * * bind the paramaters ** */
                $stmt->bindParam(':userId', $userId);
                $stmt->execute();
                $reportArray = $stmt->fetchAll();
                $reportText = "Witaj, \r\nPoniżej znajduja się oceny, które Twoje dziecko otrzymało w tym roku szkolnym.\r\nOceny uszeregowane są od najwyższej do najniższej.\r\n\r\n";
                foreach ($reportArray as $i) {
                    $reportText .= 'Data: '.$i['gradeDate']."\r\n";
                    $reportText .= 'Ocena: ' . $i['gradeValue']."\r\n";
                    $reportText .= 'Waga: ' . $i['gradeWeight']."\r\n";
                    $reportText .= 'Przedmiot: ' . $i['subjectName']."\r\n";
                    $reportText .= 'Tytuł: ' . $i['gradeAbbrev'] . ' - ' . htmlspecialchars_decode($i['gradeTitle'])."\r\n\r\n";

                }
                $reportText .= "Z poważaniem,\r\nDziennikLogin";
                return $reportText;
            } catch (PDOException $e) {
                return
                        $e->getMessage();
            }
    }
}
?>