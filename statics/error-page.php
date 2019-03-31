<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>
            <?= $title; ?>
        </title>
        
        <style>
            body {
                width: 60%;
                background-color: #fefefe;
                font-family: Helvetica, Arial, sans-serif;
                color: #333;
                text-align: left;
                margin: auto;
                margin-top: 5em;
            }
            p {
                line-height: 1.5em;
                text-align: justify;
            }
            h1.error-code {
                font-weight: 800;
                font-size: 150px;
                color: #ccc;
            }
            
            .link-buttons {
                margin-top: 50px;
            }
            .link-buttons a {
                background-color: #0063dc;
                color: #fefefe;
                padding: 10px 40px;
                font-weight: 600;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <h1 class="error-code">
            <?= http_response_code(); ?>
        </h1>
        
        <h2>

            <?= $heading; ?>

        </h2>
        
        <div class="body-text">
            <?= $body; ?>
        </div>
        
        <?php if (!empty($helplink)) { ?>
        <div class="link-buttons">
            <?= $helplink; ?>
        </div>
        <?php } ?>
    </body>
</html>