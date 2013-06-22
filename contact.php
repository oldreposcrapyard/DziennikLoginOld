<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title>DziennikLogin - Kontakt</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Le styles -->
        <link href="lib/bootstrap/css/bootstrap.css" rel="stylesheet">
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
        </style>
        <link href="lib/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    </head>

    <body>
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <div class="container-narrow">
            <div class="masthead">
                <ul class="nav nav-pills pull-right">
                    <li><a href="index.php">Strona główna</a></li>
                    <li><a href="userPanel">Panel użytkownika</a></li>
                    <li class="active"><a href="contact.php">Kontakt</a></li>
                </ul>
                <h3 class="muted">DziennikLogin</h3>
            </div>
            <form class="well span8">
                <div class="row">
                    <div class="span3">
                        <label>Imię</label>
                        <input type="text" class="span3" placeholder="Twoje imię">
                        <label>Nazwisko</label>
                        <input type="text" class="span3" placeholder="Twoje nazwisko">
                        <label>Adres e-mail</label>
                        <input type="text" class="span3" placeholder="Twój e-mail">
                        <label>Temat</label>
                        <select id="subject" name="subject" class="span3">
                            <option value="na" selected="">Wybierz:</option>
                            <option value="service">Opinia</option>
                            <option value="suggestions">Sugestia</option>
                            <option value="product">Problem</option>
                        </select>
                    </div>
                    <div class="span5">
                        <label>Wiadomość</label>
                        <textarea name="message" id="message" class="input-xlarge span5" rows="10"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary pull-right">Wyślij</button>
                </div>
            </form>

            <hr>

            <div class="footer">
                <p>&copy; Marcin Ławniczak 2013</p>
            </div>

        </div> <!-- /container -->

    </body>
</html>