<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>
            <?php echo $title; ?>
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
                margin-bottom: 5em;
            }
            p {
                line-height: 1.5em;
                text-align: justify;
            }
            h1.error-code {
                font-weight: 800;
                font-size: 150px;
                color: #ccc;
                margin-bottom: 50px;
            }
            
            .link-buttons {
                margin-top: 70px;
                background-color: #357ebd;
                border-radius: 4px;
                padding: 10px 40px;
                font-weight: 600;
                max-width: 40%;
            }
            .link-buttons a {
                color: #fefefe;
                text-decoration: none;
            }
            pre {
                overflow: auto;
                margin: 15px;
                margin-top: 30px;
                color: #777;
            }
        </style>
    </head>
    <body>
        <h1 class="error-code">
            <?php echo http_response_code(); ?>
        </h1>
        
        <h2>

            <?php echo $heading; ?>

        </h2>
        
        <div class="body-text">
            <?php echo $body; ?>
        </div>
        
        <?php if (!empty($helplink)) { ?>
        <div class="link-buttons">
            <?php echo $helplink; ?>
        </div>
        <?php } ?>
    </body>
</html>
