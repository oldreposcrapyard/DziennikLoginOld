<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title>DziennikLogin - Kontakt</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- Le styles -->
        <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
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
        <link href="lib/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container-narrow">
            <div class="masthead">
                <ul class="nav nav-pills pull-right">
                    <li><a href="index.php">Strona główna</a></li>
                    <li><a href="userPanel">Panel użytkownika</a></li>
                    <li class="active"><a href="contact.php">Kontakt</a></li>
                </ul>
                <h3 class="muted">DziennikLogin</h3>
            </div>
            <hr>

                        <form class="well span8">
                    <div class="controls controls-row">
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