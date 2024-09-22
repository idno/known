<?php

    $ok       = true;
    $messages = $vars['messages'];


    $site_title  = $vars['site_title'];
    $mysql_user  = $vars['mysql_user'];
    $mysql_host  = $vars['mysql_host'];
    $mysql_pass  = $vars['mysql_pass'];
    $mysql_name  = $vars['mysql_name'];
    $upload_path = $vars['upload_path'];

?>

<div id="form-div">


    <h2>
        Technical settings
    </h2>

    <?php

    if (!empty($messages)) {
        echo $this->__(
            [
            'messages' => $messages
            ]
        )->draw('pages/elements/messages');

    } else {

        $canary = '<p>Rewriting appears to be disabled. Usually this means "AllowOverride None" is set in apache2.conf ';
        $canary .= 'which prevents Known\'s .htaccess from doing its thing. We tried to fetch a URL that should redirect ';
        $canary .= 'to known.min.js</p>';
        $canary .= '<p>You can usually fix this by setting <code>AllowOverride All</code> in your Apache configuration.</p>';
        $canary .= '<p>If you think this is an error, you can continue, but you might have problems later on.</p>';

        echo $this->__(
            [
            'id' => 'canary',
            'messages' => $canary,
            'style' => 'display: none;'
            ]
        )->draw('pages/elements/messages');

        ?>
        <div id="success-block" class="alerts success message-success-block" style="display:none;">
            <p>
                Great! You have everything you need to get started.
            </p>
            <p>
                On this screen, we'll ask you how we should connect to your database, and where we should save
                uploaded files like user photos, pictures and audio.
            </p>
        </div>
        <?php

    }

    ?>

    <form action="" method="post">
        <div class="settings-group">
            <h3>1. What are you going to name your site?</h3>

            <p>
                <label class="control-label" for="site_title">
                    Don't worry: you can change this at any time. You can even leave it blank for now if
                    you need more time.
                </label>
            </p>

            <p>
                <input type="text" name="site_title" placeholder=""  value="<?php echo !is_null($site_title) ?  htmlspecialchars($site_title) : '' ?>"
                       class="profile-input" id="site_title">
            </p>
        </div>
        <div class="settings-group">
            <h3>
                2. Your MySQL settings
            </h3>

            <p class="control-label">
                Known needs a single MySQL database, with a MySQL user that can connect to it. We recommend that this
                is a user you have created just for Known, rather than one you share with other applications.
                <br><br>
                You should create your database before entering the details here. If you're using a shared host,
                you may have an option called "MySQL Database Wizard" that will speed you through the process.
            </p>

            <p>
                <label class="control-label">
                    MySQL database name<br>
                    <input type="text" name="mysql_name" placeholder="" value="<?php echo !is_null($mysql_name) ?  htmlspecialchars($mysql_name):'' ?>"
                           class="profile-input" required>
                </label>
            </p>

            <p>
                <label class="control-label">
                    MySQL username<br>
                    <input type="text" name="mysql_user" placeholder="" value="<?php echo !is_null($mysql_user) ?  htmlspecialchars($mysql_user):'' ?>"
                           class="profile-input" required>
                </label>
            </p>

            <p>
                <label class="control-label">
                    MySQL password<br>
                    <input type="password" name="mysql_pass" placeholder="" value="" class="profile-input" required>
                </label>
            </p>

            <p>
                <label class="control-label">
                    MySQL server name<br>
                    <input type="text" name="mysql_host" placeholder="<?php echo !is_null($mysql_host) ? htmlspecialchars($mysql_host) :'' ?>"
                           value="localhost" class="profile-input" required>
                </label>
            </p>
        </div>
        <div class="settings-group">
            <h3>
                3. Your upload directory
            </h3>

            <p>
                <label class="control-label" for="upload_path">
                    The full path to a folder that Known can upload to. By default this is the "Uploads" folder -
                    you should leave this as-is unless you're an experienced system administrator. You need to make
                    sure the web server can save data to it. In your file manager, you should be able
                    to select the folder, and click to enable "group" write access.
                </label>
            </p>

            <p>
                <input type="text" name="upload_path" id="upload_path" placeholder=""
                       value="<?php echo !is_null($upload_path) ? htmlspecialchars($upload_path) :'' ?>" class="profile-input" required>
            </p>
        </div>
        <div class="submit settings-group page-bottom">
            <input type="submit" class="btn btn-primary btn-lg btn-responsive" value="Onwards!">
        </div>
    </form>
    <script>
        var request = new XMLHttpRequest();  
        request.open('GET', '../js/canary.js', true);
        request.onreadystatechange = function(){
            if (request.readyState !== 2){
                if (request.status !== 200) {  
                    document.getElementById('canary').style.display = 'block';
                    document.getElementById('success-block').style.display = 'none';
                }  
            } else {
                document.getElementById('success-block').style.display = 'block';
            }
        };
        request.send();
    </script>

</div>
