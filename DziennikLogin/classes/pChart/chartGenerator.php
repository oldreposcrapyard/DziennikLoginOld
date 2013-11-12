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

    private function generateChart() {
        /* Create your dataset object */
        $myData = new pData();

        /* Add data in your dataset */
        $myData->addPoints(array(VOID, 3, 4, 3, 5));

        /* Create a pChart object and associate your dataset */
        $myPicture = new pImage(700, 230, $myData);

        /* Define the boundaries of the graph area */
        $myPicture->setGraphArea(60, 40, 670, 190);

        /* Choose a nice font */
        $myPicture->setFontProperties(array("FontName" => "fonts/Forgotte.ttf", "FontSize" => 11));

        /* Draw the scale, keep everything automatic */
        $myPicture->drawScale();

        /* Draw the scale, keep everything automatic */
        $myPicture->drawBarChart();

        /* Build the PNG file and send it to the web browser */
        //$myPicture->Stroke();
    }

    public function getDataForChart() {
        try {
            $queryHandleSelect = $this->pdoHandle->prepare('SELECT gradeValue,gradeWeight FROM grades WHERE userId=:userId');
            $queryHandleSelect->bindParam(':userId',$this->currentUserId);
            $queryHandleSelect->execute();
            $this->chartData = $queryHandleSelect->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Błąd bazy danych:' . $e->getMessage());
        }
        //Convert array from database
        $this->chartDataConverted = array(1=>0,"1.5"=>0,2=>0,"2.5"=>0,3=>0,"3.5"=>0,4=>0,"4.5"=>0,5=>0,"5.5"=>0,6=>0);
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

var_dump($chartGenerator->chartDataConverted);

?>
