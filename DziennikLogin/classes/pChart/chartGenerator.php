<?php

/* Include all the classes */
include("class/pDraw.class.php");
include("class/pImage.class.php");
include("class/pData.class.php");

use \PDO;
use \Exception;
use \PDOException;

/**
 * Generate graphs with required data
 *
 * This class generates charts for specified users.
 *
 * @author Marcin Ławniczak <marcin.safmb@gmail.com>
 * @package DziennikLogin
 * @version 0.1
 * @return image
 *
 * 
 */
class graphReportGenerator {

    /**
     * The handle to PDO object
     * @var resource
     */
    private $pdoHandle;

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
    private $chartData;
    private $chartDataConverted;

    public function __construct() {    
    }

    public function setUserId($userId) {
        $this->currentUserId = $userId;
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
        } catch (PDOException $e) {
            throw new Exception('Błąd bazy danych:' . $e->getMessage());
        }
    }

    private function generateChart() {
        /* Create your dataset object */
        $myData = new pData();

        /* Add data in your dataset */
        $myData->addPoints($this->chartDataConverted);
        $myData->addPoints(array(1, "1.5", 2, "2.5", 3, "3.5", 4, "4.5", 5, "5.5", 6), "Labels");
        $myData->setSerieDescription("Labels", "Oceny");
        $myData->setAbscissa("Labels");
        //$myData->setAxisName(0,"Waga/Ilość");
        /* Overlay with a gradient */

        /* Create a pChart object and associate your dataset */
        $myPicture = new pImage(700, 230, $myData);
        $myPicture->drawGradientArea(0, 0, 699, 299, DIRECTION_VERTICAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
        $myPicture->drawGradientArea(0, 0, 699, 299, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
        /* Add a border to the picture */
        $myPicture->drawRectangle(0, 0, 699, 229, array("R" => 0, "G" => 0, "B" => 0));
        /* Define the boundaries of the graph area */
        $myPicture->setGraphArea(60, 40, 670, 190);
        /* Choose a nice font */
        $myPicture->setFontProperties(array("FontName" => "/fonts/pf_arma_five.ttf", "FontSize" => 11));
        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
        /* Draw the scale, keep everything automatic */
        $myPicture->drawScale();
        /* Create the per bar palette */ //as per http://ideone.com/vWSh6o nope
         $Palette = array("0" => array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 100),//FF0000 don
              "1" => array("R" => 255, "G" => 51, "B" => 0, "Alpha" => 100),//FF3300 don
              "2" => array("R" => 255, "G" => 102, "B" => 0, "Alpha" => 100),//FF6600 don
              "3" => array("R" => 255, "G" => 153, "B" => 0, "Alpha" => 100),//FF9900 don
              "4" => array("R" => 255, "G" => 204, "B" => 0, "Alpha" => 100),//FFCC00 don
              "5" => array("R" => 255, "G" => 255, "B" => 0, "Alpha" => 100),//FFFF00 don
              "6" => array("R" => 204, "G" => 255, "B" => 0, "Alpha" => 100),//CCFF00 don
              "7" => array("R" => 153, "G" => 255, "B" => 0, "Alpha" => 100),//99FF00 don
              "8" => array("R" => 102, "G" => 255, "B" => 0, "Alpha" => 100),//66FF00 don
              "9" => array("R" => 51, "G" => 255, "B" => 0, "Alpha" => 100),//33FF00 don
              "10" => array("R" => 0, "G" =>255, "B" => 0, "Alpha" => 100)//00FF00 don
            );

        /* Draw the scale, keep everything automatic */
        $myPicture->drawBarChart(array("DisplayValues" => TRUE, "DisplayShadow"=>TRUE,"Rounded" => TRUE, "Surrounding" => 30,"OverrideColors"=>$Palette,"Draw0Line"=>TRUE));

        /* Build the PNG file and send it to the web browser */
        $myPicture->Stroke();
    }

    private function getDataForChart() {
        try {
            $queryHandleSelect = $this->pdoHandle->prepare('SELECT gradeValue,gradeWeight FROM grades WHERE userId=:userId');
            $queryHandleSelect->bindParam(':userId', $this->currentUserId);
            $queryHandleSelect->execute();
            $this->chartData = $queryHandleSelect->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Błąd bazy danych:' . $e->getMessage());
        }
        //Convert array from database
        $this->chartDataConverted = array(1 => 0, "1.5" => 0, 2 => 0, "2.5" => 0, 3 => 0, "3.5" => 0, 4 => 0, "4.5" => 0, 5 => 0, "5.5" => 0, 6 => 0);
        foreach ($this->chartData as $val) {
            $this->chartDataConverted[$val['gradeValue']] = $this->chartDataConverted[$val['gradeValue']] + $val['gradeWeight'];
        }
    }

    public function executeProcessing() {
        $this->connectToDatabase();
        $this->getDataForChart();
        $this->generateChart();
    }

}
?>
