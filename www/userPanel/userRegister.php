<?php
require '../../lib/registrationValidation.php';
require '../../lib/bcryptWrapper.php';
require '../../config.local.php';

$db_host = $CONF['databaseHost'];
$db_name = $CONF['databaseName'];
$db_username = $CONF['databaseUsername'];
$db_password = $CONF['databasePassword'];


if (isset($_POST['isSent']) && $_POST['isSent'] == 'yes') {//check if form has been sent
    $registrationErrors = '';
    //begin checks
    //username
    if (preg_match('/^[A-Za-z][A-Za-z0-9]{7,31}$/', $_POST['username'])) {//check username
        $usernameOkay = TRUE;
    } else {
        $usernameOkay = FALSE;
        $registrationErrors .= "Nieprawidłowa nazwa uzytkownika!\r\n";
    }

    //email
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $emailOkay = TRUE;
    } else {
        $emailOkay = FALSE;
        $registrationErrors .= "Nieprawidłowy adres e-mail!\r\n";
    }
    //passwords
    if ($_POST['password'] != '' && $_POST['passwordConfirm'] != '') {//if passwords are empty
        if ($_POST['password'] === $_POST['passwordConfirm']) {//passwords match
            $passwordOkay = TRUE;
            if (checkPassword($_POST['password'], $_POST['username'])) {//password final check
                $passwordOkay = TRUE;
            } else {
                $passwordOkay = FALSE;
                $registrationErrors .= "Hasło nie spełnia wymagań!\r\n";
            }
        } else {
            $passwordOkay = FALSE;
            $registrationErrors .= "Hasła nie zgadzają się!\r\n";
        }
    } else {
        $passwordOkay = FALSE;
        $registrationErrors .= "Hasła są puste!\r\n";
    }

    //register username
    if ($_POST['registerUsername'] != '') {//if register username is empty
        $registerUsernameOkay = TRUE;
    } else {
        $passwordOkay = FALSE;
        $registrationErrors .= "Nazwa użytkownika Dziennika jest pusta!\r\n";
    }

    //register passwords
    if ($_POST['registerPassword'] != '' && $_POST['registerPasswordConfirm'] != '') {//if register passwords are empty
        if ($_POST['registerPassword'] === $_POST['registerPasswordConfirm']) {// register passwords match
            $registerPasswordOkay = TRUE;
        } else {
            $registerPasswordOkay = FALSE;
            $registrationErrors .= "Hasła do dziennika nie zgadzają się!\r\n";
        }
    } else {
        $registerPasswordOkay = FALSE;
        $registrationErrors .= "Hasła do Dziennika są puste!\r\n";
    }



    if ($usernameOkay && $passwordOkay && $emailOkay && $registerUsernameOkay && $registerPasswordOkay) {//can write to database && $registerUsernameOkay
        //validated all data, can now insert
        //connect to database
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_username, $db_password, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            return 'Błąd bazy danych:' . $e->getMessage();
        }
        //hash password
        $crypter = new Bcrypt(12);  // correct
        $hashedPassword = $crypter->hash($_POST['password']);
        //crypt register password
        $fileContents = file_get_contents('public.key');
        $publicKey = openssl_pkey_get_public($fileContents);
        $registerPasswordEncrypted = '';
        if (!openssl_public_encrypt($_POST['registerPassword'], $registerPasswordEncrypted, $publicKey))
            die('Failed to encrypt data');
        openssl_free_key($publicKey);
        //check e-mail
        try {
            $stmt = $pdo->prepare('INSERT INTO users VALUES (NULL,:userName,:userPassword,:registerUsername,:registerPassword)');
            $stmt->bindParam(':userName', $_POST['username']);
            $stmt->bindParam(':userPassword', $hashedPassword);
            $stmt->bindParam(':registerUsername', $_POST['registerUsername']);
            $stmt->bindParam(':registerPassword', $registerPasswordEncrypted);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title>DziennikLogin - Rejestracja</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Le styles -->
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"  media="screen">
        <style type="text/css">
            body {
                padding-top: 20px;
                padding-bottom: 40px;
            }

            /* Custom container */
            .container-narrow {
                margin: 0 auto;
                max-width: 700px;
            }
            .container-narrow > hr {
                margin: 30px 0;
            }
        </style>
        <link href="../bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container-narrow">

            <div class="masthead">
                <ul class="nav nav-pills pull-right">
                    <li><a href="../index.php">Strona główna</a></li>
                    <li><a href="../userPanel">Panel użytkownika</a></li>
                    <li><a href="../contact.php">Kontakt</a></li>
                </ul>
                <h3 class="muted">DziennikLogin</h3>
            </div>

            <hr>
            <form class="form-horizontal" action='userRegister.php' method="POST">
                <fieldset>
                    <div id="legend">
                        <legend class="">Rejestracja</legend>
                    </div>
                    <?php if (isSet($registrationErrors)) {
                        echo $registrationErrors;
                    } ?>
                    <div class="control-group">
                        <!-- Username -->
                        <label class="control-label" for="username">Nazwa użytkownika</label>
                        <div class="controls">
                            <input type="text" id="username" name="username" placeholder="" class="input-xlarge">
                            <p class="help-block">Nazwa użytkownika może zawierać małe i wielkie litery oraz cyfry.</p>
                        </div>
                    </div>

                    <div class="control-group">
                        <!-- E-mail -->
                        <label class="control-label" for="email">E-mail</label>
                        <div class="controls">
                            <input type="text" id="email" name="email" placeholder="" class="input-xlarge">
                            <p class="help-block">Podaj swój E-mail (Na niego będą wysyłane oceny)</p>
                        </div>
                    </div>

                    <div class="control-group">
                        <!-- Password-->
                        <label class="control-label" for="password">Hasło</label>
                        <div class="controls">
                            <input type="password" id="password" name="password" placeholder="" class="input-xlarge">
                            <p class="help-block">Hasło powinno mieć co najmniej 8 znaków.</p>
                        </div>
                    </div>

                    <div class="control-group">
                        <!-- Password -->
                        <label class="control-label"  for="passwordConfirm">Potwierdź Hasło</label>
                        <div class="controls">
                            <input type="password" id="passwordConfirm" name="passwordConfirm" placeholder="" class="input-xlarge">
                            <p class="help-block">Proszę potwierdź hasło</p>
                        </div>
                    </div>
                    <div class="control-group">
                        <!-- Username -->
                        <label class="control-label" for="registerUsername">Nazwa użytkownika Dziennika</label>
                        <div class="controls">
                            <input type="text" id="registerUsername" name="registerUsername" placeholder="" class="input-xlarge">
                            <p class="help-block">Używana do logowania w Dzienniku Elektronicznym szkoły.</p>
                        </div>
                    </div>
                    <div class="control-group">
                        <!-- Password-->
                        <label class="control-label" for="registerPassword">Hasło do Dziennika</label>
                        <div class="controls">
                            <input type="password" id="registerPassword" name="registerPassword" placeholder="" class="input-xlarge">
                            <p class="help-block">Używane do logowania w Dzienniku Elektronicznym szkoły.</p>
                        </div>
                    </div>

                    <div class="control-group">
                        <!-- Password -->
                        <label class="control-label"  for="registerPasswordConfirm">Potwierdź Hasło do Dziennika</label>
                        <div class="controls">
                            <input type="password" id="registerPasswordConfirm" name="registerPasswordConfirm" placeholder="" class="input-xlarge">
                            <p class="help-block">Proszę potwierdź hasło do logowania w Dzienniku Elektronicznym szkoły.</p>
                        </div>
                    </div>
                    <input type="hidden" id="isSent" name ="isSent" value="yes">
                    <div class="control-group">
                        <!-- Button -->
                        <div class="controls">
                            <button class="btn btn-success">Zarejestruj</button>
                        </div>
                    </div>
                </fieldset>
            </form>
            <hr>
            <div class="footer">
                <p>&copy; Marcin Ławniczak 2013</p>
            </div>

        </div> <!-- /container -->

    </body>
</html>