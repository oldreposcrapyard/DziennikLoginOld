<?php
// simple app to access e-dziennik grades through curl
// Marcin Ławniczak
// marcin.safmb@gmail.com
// github.com/marcinlawnik

// TODO:
// Supply the password in other way (encrypted post?)
// Split the project into downloader -> parser -> output file
// Write teh parser (damn you, awful table layout!)
// Get this into nice graphical form
// Write an android app :) (It is good to have faith and dreams...)

/*
Data Gathered:
login field name: 
user_name
password field name:
user_passwd
additional imput:
<input type="hidden" name="con" value="e-dziennik-szkola01.con">
login page address: 
https://92.55.225.11/dbviewer/login.php
page with grades:
https://92.55.225.11/dbviewer/view_data.php?view_name=uczen_uczen_arkusz_ocen_semestr.view
method:
POST
*/
require ('config.local.php');
// Necessary urls
$login_url = 'https://92.55.225.11/dbviewer/login.php';
$grades_url = 'https://92.55.225.11/dbviewer/view_data.php?view_name=uczen_uczen_arkusz_ocen_semestr.view';
//These are the post data
$post_data = 'user_name='.$CONF['dziennikUsername'].'&user_passwd='.$CONF['dziennikPassword'].'&con=e-dziennik-szkola01.con';

//Create a curl object
$ch = curl_init();

//IGNORE SSL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
//Set the useragent
$agent = $_SERVER["HTTP_USER_AGENT"];
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
 
//Set the URL
curl_setopt($ch, CURLOPT_URL, $login_url );
 
//This is a POST query
curl_setopt($ch, CURLOPT_POST, 1 );
 
//Set the post data
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
 
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
curl_setopt($ch, CURLOPT_URL, $grades_url);
//set referrer
curl_setopt ($ch, CURLOPT_REFERER, $login_url); 
//Execute
$gradesResult = curl_exec($ch);

//Free up resources
curl_close($ch);

echo htmlentities($gradesResult);
?>
