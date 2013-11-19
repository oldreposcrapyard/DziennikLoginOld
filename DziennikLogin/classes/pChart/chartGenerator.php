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
        $myData->setSerieDescription("Labels", "oceny");
        $myData->setAxisName(0,"Waga/Ilość");
        /* Overlay with a gradient */

        /* Create a pChart object and associate your dataset */
        $myPicture = new pImage(700, 230, $myData);
        /* Add a border to the picture */
        $myPicture->drawRectangle(0, 0, 699, 229, array("R" => 0, "G" => 0, "B" => 0));
        /* Define the boundaries of the graph area */
        $myPicture->setGraphArea(60, 40, 670, 190);
        $Settings = array("StartR" => 219, "StartG" => 231, "StartB" => 139, "EndR" => 1, "EndG" => 138, "EndB" => 68, "Alpha" => 50);
        $myPicture->drawGradientArea(0, 0, 700, 230, DIRECTION_VERTICAL, $Settings);
        $myPicture->drawGradientArea(0, 0, 700, 20, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 0, "StartB" => 0, "EndR" => 50, "EndG" => 50, "EndB" => 50, "Alpha" => 80));
        $Settings2 = array("R" => 170, "G" => 183, "B" => 87, "Dash" => 1, "DashR" => 190, "DashG" => 203, "DashB" => 107);
        $myPicture->drawFilledRectangle(0, 0, 700, 230, $Settings2);
        /* Create the per bar palette 
        $Palette = array("1,5" => array("R" => 188, "G" => 224, "B" => 46, "Alpha" => 100),
            "1" => array("R" => 224, "G" => 100, "B" => 46, "Alpha" => 100),
            "2" => array("R" => 224, "G" => 214, "B" => 46, "Alpha" => 100),
            "3" => array("R" => 46, "G" => 151, "B" => 224, "Alpha" => 100),
            "4" => array("R" => 176, "G" => 46, "B" => 224, "Alpha" => 100),
            "5" => array("R" => 224, "G" => 46, "B" => 117, "Alpha" => 100),
            "6" => array("R" => 92, "G" => 224, "B" => 46, "Alpha" => 100),
            "2,5" => array("R" => 224, "G" => 176, "B" => 46, "Alpha" => 100));*/
        /* Choose a nice font */
        $myPicture->setFontProperties(array("FontName" => "fonts/pf_arma_five.ttf", "FontSize" => 11));
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
        /* Draw the scale, keep everything automatic */
        $myPicture->drawScale();


        /* Draw the scale, keep everything automatic */
        $myPicture->drawBarChart(array("DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"Rounded"=>TRUE,"Surrounding"=>30,"OverrideColors"=>$Palette));

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
