<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title>DziennikLogin - Strona Główna</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Le styles -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
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

            /* Main marketing message and sign up button */
            .jumbotron {
                margin: 60px 0;
                text-align: center;
            }
            .jumbotron h1 {
                font-size: 72px;
                line-height: 1;
            }
            .jumbotron .btn {
                font-size: 21px;
                padding: 14px 24px;
            }

            /* Supporting marketing content */
            .marketing {
                margin: 60px 0;
            }
            .marketing p + h4 {
                margin-top: 28px;
            }
            .or{
                margin: 2em;
            }
        </style>
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container-narrow">

            <div class="masthead">
                <ul class="nav nav-pills pull-right">
                    <li class="active"><a href="index.php">Strona główna</a></li>
                    <li><a href="userPanel">Panel użytkownika</a></li>
                    <li><a href="contact.php">Kontakt</a></li>
                </ul>
                <h3 class="muted">DziennikLogin</h3>
            </div>

            <hr>

            <div class="jumbotron">
                <h1>Nie masz czasu na sprawdzanie ocen?</h1>
                <p class="lead">DziennikLogin ułatwi Tobie życie. Nasz system wyśle do Ciebie oceny, które zostały uzyskane w danym okresie czasu. Rozpocznij już teraz.</p>
                <a class="btn btn-large btn-success" href="userPanel/userRegister.php">Załóż konto</a> <small class="or">lub</small>
                <a class="btn btn-large btn-primary" href="userPanel/userLogin.php">Zaloguj się</a>
            </div>

            <hr>

            <div class="row-fluid marketing">
                <div class="span6">
                    <h4>Subheading</h4>
                    <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

                    <h4>Subheading</h4>
                    <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

                    <h4>Subheading</h4>
                    <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
                </div>

                <div class="span6">
                    <h4>Subheading</h4>
                    <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

                    <h4>Subheading</h4>
                    <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

                    <h4>Subheading</h4>
                    <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
                </div>
            </div>

            <hr>

            <div class="footer">
                <p>&copy; Marcin Ławniczak 2013</p>
            </div>

        </div> <!-- /container -->

    </body>
</html>
