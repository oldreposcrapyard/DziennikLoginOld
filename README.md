DziennikLogin
=============

A simple app to directly access e-dziennik grades. (EKOS)

Usage:
=============

#Pobieranie danych
*/5 * * * * php /path/to/script/cron/dailyDataDownloader.php
#Wysylanie co 5 min
1,6,11,16,21,26,31,36,41,46,51,56 * * * * php /path/to/script/cron/dailyReportSender.php


TODO
=============
Get this into nice graphical form

Write an android app :) (It is good to have faith and dreams...)

DONE:
=============
Write teh parser (damn you, awful table layout!)

Split the project into downloader -> parser -> output file

Supply the password in other way (encrypted post?)



Data Gathered:
=============
login field name: user_name

password field name: user_passwd

additional imput: <input type="hidden" name="con" value="e-dziennik-szkola01.con">

login page address:  https://92.55.225.11/dbviewer/login.php

page with grades: https://92.55.225.11/dbviewer/view_data.php?view_name=uczen_uczen_arkusz_ocen_semestr.view

method: POST


