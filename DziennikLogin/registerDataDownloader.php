<?php

namespace DziennikLogin;

/**
 * Download the data from register
 * 
 * This class connects to the register using cURL and gets the source
 * of the grade page from it.
 *
 * @author Marcin Ławniczak <marcin.safmb@gmail.com>
 * @package DziennikLogin
 * @version 0.1
 * @return string|false
 * @todo Add a destructor that kills the curl object
 */
class registerDataDownloader {

    /**
     * The username of user used to access the register to download data
     * @var string 
     */
    private $registerUserUsername = null;

    /**
     * The password of user used to access the register to download data
     * @var string 
     */
    private $registerUserPassword = null;

    /**
     * The handle to created curl object
     * @var object 
     */
    private $curlHandle = null;

    /**
     * The path where the temporary cookie file is located
     * @var string 
     */
    private $cookieFilePath = '';

    /**
     * The POST data used to login to register
     * @var string 
     */
    private $postData = '';

    /**
     * The contents of grade page
     * @var string 
     */
    private $registerGradePageContent = '';

    /**
     * The result of query for the grade page
     * @var string
     */
    private $queryGetGradePageResult = '';

    /**
     * This array contains any errors that occur
     * @var array
     */
    private $errors = array();

    /**
     * In constructor we only create the cURL object and 
     * set the cookie filepath which can be changed later.
     */
    public function __construct($cookiePath = '') {
        if ($this->createCurlObject() === FALSE) {
            $this->errors[] .= 'Could not create cURL object';
        }
        if ($cookiePath != '') {
            $this->setCookieFilePath($cookiePath);
        }
    }

    /**
     * This method creates the cURL object
     * @return true|false
     */
    private function createCurlObject() {
        //Create a curl object
        if (!$this->curlHandle = curl_init()) {
            return FALSE;
        } else {
            return TRUE;
        }
        //return $curlHandle;
    }

    /**
     * This method sets the properties necessary to connect to register.
     * 
     * Those properties include, but are not limited to:
     * -useragent
     * -ssl settings
     * -return transfer settings
     * -timeout
     * Returns true on success, false otherwise
     * @return true|false
     */
    private function setCurlProperties() {
        if (!isset($this->cookieFilePath)) {
            return FALSE;
        } else {
            //Ignore the SSL communication, because the certificate is outdated
            curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
            //Set the useragent (This one is my laptop)
            curl_setopt($this->curlHandle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22');
            //Specify that we want the content after the query
            curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, 1);
            //Follow Location redirects
            curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            //Set timeout
            curl_setopt($this->curlHandle, CURLOPT_TIMEOUT, 60);
            //Set the cookie storing files
            //Cookie files are necessary since we are logging and session data needs to be saved
            curl_setopt($this->curlHandle, CURLOPT_COOKIEJAR, $this->cookieFilePath);
            curl_setopt($this->curlHandle, CURLOPT_COOKIEFILE, $this->cookieFilePath);
            return TRUE;
        }
    }

    /**
     * This method logins us to the register used supplied login data
     * @return type Description
     */
    private function doRegisterLogin() {
        //Set the URL
        curl_setopt($this->curlHandle, CURLOPT_URL, 'https://92.55.225.11/dbviewer/login.php');
        //Define that this is a POST query
        curl_setopt($this->curlHandle, CURLOPT_POST, 1);
        //Set the post data
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $this->postData);
        //Execute
        curl_exec($this->curlHandle);
    }

    /**
     * This method logs out the currently logged in user
     * @return true|false
     */
    private function doRegisterLogout() {
        //Set the URL
        curl_setopt($this->curlHandle, CURLOPT_URL, 'https://92.55.225.11/dbviewer/logout.php?con=e-dziennik-szkola01.con&location=..');
        //Execute
        curl_exec($this->curlHandle);
        return TRUE;
    }

    /**
     * This method gets the register grade page after login
     * @return true|false
     */
    private function getGradePageContent() {
        //Go to page with grades
        curl_setopt($this->curlHandle, CURLOPT_URL, 'https://92.55.225.11/dbviewer/view_data.php?view_name=uczen_uczen_arkusz_ocen_semestr.view');
        //Set referrer
        curl_setopt($this->curlHandle, CURLOPT_REFERER, 'https://92.55.225.11/dbviewer/login.php');
        //Execute
        $this->queryGetGradePageResult = curl_exec($this->curlHandle);
        //Write the grade page content and return response
        if ($this->queryGetGradePageResult != '') {
            $this->registerGradePageContent = $this->queryGetGradePageResult;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This method generates the post data from the user username and password
     * @return void
     */
    private function generatePostData() {
        $this->postData = 'user_name=' . $this->registerUserUsername . '&user_passwd=' . $this->registerUserPassword . '&con=e-dziennik-szkola01.con';
    }

    /**
     * This method sets the username used to access the register
     * @param string $registerUsername The user username
     */
    public function setRegisterUsername($registerUsername) {
        //Make sure the variable is empty
        $this->registerUserUsername = null;
        //Then fill it with the username
        $this->registerUserUsername = $registerUsername;
    }

    /**
     * This method sets the passsword used to access the register
     * @param string $registerPassword
     */
    public function setRegisterPassword($registerPassword) {
        //Make sure the variable is empty
        $this->registerUserPassword = null;
        //Then fill it with the password
        $this->registerUserPassword = $registerPassword;
    }

    /**
     * This method sets the path to the cookie file
     * @param string $cookiePath The path to cookie file
     */
    public function setCookieFilePath($cookiePath) {
        //Set the path of the cookie file as supplied
        $this->cookieFilePath = $cookiePath;
    }

    /**
     * This method actually downloads the data and writes it into variable
     * Returns false on failure, true otherwise
     * @return true|false
     */
    private function downloadData() {
        //Check for the necessary login data
        if ($this->registerUserUsername == '' || $this->registerUserUsername == '') {
            return FALSE;
        }
        //Check for cookie file path
        if ($this->cookieFilePath == '') {
            return FALSE;
        }
        //Set the curl properties
        if (!$this->setCurlProperties()) {
            return FALSE;
        }
        //Generate the POST data
        $this->generatePostData();
        //Login to the register
        $this->doRegisterLogin();
        //Get content of the grade page
        if (!$this->getGradePageContent()) {
            return FALSE;
        }
        //logout from the register so that the next user could be processed
        if (!$this->doRegisterLogout()) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * This method returns the downloaded data
     * @return string|false
     */
    public function getDownloadedData() {
        if (!$this->downloadData()) {
            return FALSE;
        }
        if ($this->registerGradePageContent !== '') {
            return $this->registerGradePageContent;
        } else {
            return FALSE;
        }
    }

    /**
     * This method returns any errors if they occur, false otherwise
     * @return array|false
     */
    public function getErrors() {
        if (array_key_exists(0, $this->errors)) {
            return $this->errors;
        } else {
            return FALSE;
        }
    }

}

?>