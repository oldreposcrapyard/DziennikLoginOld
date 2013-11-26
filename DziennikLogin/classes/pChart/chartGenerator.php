<?php

/* Include all the classes */
include("class/pDraw.class.php");
include("class/pImage.class.php");
include("class/pData.class.php");

use \PDO;
use \Exception;
use \PDOException;

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
    public $chartData;
    public $chartDataConverted;

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

    public function connectToDatabase() {
        try {
            $this->pdoHandle = new \PDO("mysql:host=$this->databaseHost;dbname=$this->databaseName;charset=utf8", $this->databaseUsername, $this->databasePassword, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ));
            $this->pdoHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception('Błąd bazy danych:' . $e->getMessage());
        }
    }

    public function generateChart() {
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
        $myPicture->setFontProperties(array("FontName" => "fonts/pf_arma_five.ttf", "FontSize" => 11));
        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
        /* Draw the scale, keep everything automatic */
        $myPicture->drawScale();
        /* Create the per bar palette */
        $Palette = array("0" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "1" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "2" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "3" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "4" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "5" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "6" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "7" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "8" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "9" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "10" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100)
            );

        /* Draw the scale, keep everything automatic */
        $myPicture->drawBarChart(array("DisplayValues" => TRUE, "DisplayShadow"=>TRUE,"Rounded" => TRUE, "Surrounding" => 30,"OverrideColors"=>$Palette,"Draw0Line"=>TRUE));

        /* Build the PNG file and send it to the web browser */
        $myPicture->Stroke();
    }

    public function getDataForChart() {
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

//end of class
require 'config.local.php';

$db_host = $CONF['databaseHost'];
$db_name = $CONF['databaseName'];
$db_username = $CONF['databaseUsername'];
$db_password = $CONF['databasePassword'];

$chartGenerator = new graphReportGenerator();

$chartGenerator->setDatabaseConnectionData($db_host, $db_name, $db_username, $db_password);
$chartGenerator->connectToDatabase();
$chartGenerator->setUserId(2);
$chartGenerator->getDataForChart();
$chartGenerator->generateChart();

var_dump($chartGenerator->chartDataConverted);
?>
