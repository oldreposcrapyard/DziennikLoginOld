<?php
// simple app to access e-dziennik grades through curl
// Marcin Ławniczak
// marcin.safmb@gmail.com
// github.com/marcinlawnik

function downloadData($userUsername,$userPassword){
// Necessary urls
$urlLogin = 'https://92.55.225.11/dbviewer/login.php';
$urlGrades = 'https://92.55.225.11/dbviewer/view_data.php?view_name=uczen_uczen_arkusz_ocen_semestr.view';
//These are the post data
$postData = 'user_name='.$userUsername.'&user_passwd='.$userPassword.'&con=e-dziennik-szkola01.con';

//Create a curl object
$ch = curl_init();

//IGNORE SSL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
//Set the useragent (meh linux box!)
$agent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22';
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
 
//Set the URL
curl_setopt($ch, CURLOPT_URL, $urlLogin );
 
//This is a POST query
curl_setopt($ch, CURLOPT_POST, 1 );
 
//Set the post data
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
 
//We want the content after the query
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
//Follow Location redirects
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//set timeout
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
 
/*
Set the cookie storing files
Cookie files are necessary since we are logging and session data needs to be saved
*/
 
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
 
//Execute the action to login
$postResult = curl_exec($ch);

// Go to page with grades
curl_setopt($ch, CURLOPT_URL, $urlGrades);
//set referrer
curl_setopt ($ch, CURLOPT_REFERER, $urlLogin); 
//Execute
$queryResult = curl_exec($ch);

//Free up resources
curl_close($ch);

return $queryResult;
}

?>
