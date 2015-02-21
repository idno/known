<!doctype html>
<html>
    <head>
        <title>
            Database error
        </title>
        <style>
            body {
                width: 70%;
                background-color: #fefefe;
                font-family: Helvetica, Arial, sans-serif;
                color: #333;
                text-align: left;
                margin: auto;
                margin-top: 5em;
            }
            p {
                line-height: 1.5em;
            }
        </style>
    </head>
    <body>
        <h1>

            Oh no! We couldn't connect to the database.

        </h1>
        <p>
            This probably means that the database settings changed, this Known site hasn't been set up yet, or
            there's a database problem.
        </p>
        <?php if (!empty($message)) echo $message; ?>
        <p>
            <a href="http://docs.withknown.com">See the Known documentation for help.</a>
        </p>
    </body>
</html>