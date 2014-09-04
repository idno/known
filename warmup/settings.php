<?php

    $ok = true;
    $messages = '';

    function getInput($name)
    {
        if (!empty($_POST[$name])) {
            return $_POST[$name];
        }

        return '';
    }

    $site_title = getInput('site_title');
    $mysql_user = getInput('mysql_user');
    $mysql_host = getInput('mysql_host');
    $mysql_pass = getInput('mysql_pass');
    $mysql_name = getInput('mysql_name');
    $upload_path = getInput('upload_path');
    if (empty($mysql_host)) {
        $mysql_host = 'localhost';
    }

    if (file_exists('../config.ini')) {
        header('Location: ../begin/register?set_name=' . urlencode($site_title));
        exit;
    }

    if (!empty($mysql_name) && !empty($mysql_host)) {
        try {
            $dbh = new PDO('mysql:host=' . $mysql_host . ';dbname=' . $mysql_name, $mysql_user, $mysql_pass);
            if ($schema = @file_get_contents('../schemas/mysql/mysql.sql')) {
                $dbh->exec('use `'.$mysql_name.'`');
                $dbh->exec($schema);
            } else {
                $messages .= '<p>We couldn\'t find the schema doc.</p>';
                $ok = false;
            }
        } catch (Exception $e) {
            $messages .= '<p>We couldn\'t connect to your database. Please check your settings and try again. Here\'s the error we got:</p>';
            $messages .= '<blockquote><p>' . $e->getMessage() . '</p></blockquote>';
            $ok = false;
        }
    }

    if (!empty($upload_path)) {
        if ($upload_path = realpath($upload_path)) {
            if (substr($upload_path, -1) != '/' && substr($upload_path, -1) != '\\') {
                $upload_path .= '/';
            }
            if (file_exists($upload_path) && is_dir($upload_path)) {
                if (!is_readable($upload_path)) {
                    $ok = false;
                    $messages .= '<p>We can\'t read data from ' . htmlspecialchars($upload_path) . ' - please check permissions and try again.</p>';
                }
                if (!is_writable($upload_path)) {
                    $ok = false;
                    $messages .= '<p>We can\'t write data to ' . htmlspecialchars($upload_path) . ' - please check permissions and try again.</p>';
                }
            } else {
                $ok = false;
                $messages .= '<p>The upload path ' . htmlspecialchars($upload_path) . ' either doesn\'t exist or isn\'t a directory.</p>';
            }
        } else {
            $messages .= '<p>The upload path ' . htmlspecialchars($upload_path) . ' either doesn\'t exist or isn\'t a directory.</p>';
        }
    } else {
        $ok = false;
        if (!empty($mysql_user)) {
            $messages .= '<p>You need to specify an upload path.</p>';
        }
    }

    if ($ok = true && !empty($upload_path) && !empty($mysql_name) && !empty($mysql_host)) {
        $ini_file = <<< END

# This configuration file was created by Known's installer.

database = 'MySQL'
dbname = '{$mysql_name}'
dbpass = '{$mysql_pass}'
dbuser = '{$mysql_user}'
dbhost = '{$mysql_host}'

filesystem = 'local'
uploadpath = '{$upload_path}'

END;

        try {
            if ($fp = @fopen('../config.ini', 'w')) {
                fwrite($fp, $ini_file);
                fclose($fp);
                header('Location: ../begin/register');
                exit;
            } else {
                include 'writeconfig.php';
                exit;
            }
        } catch (Exception $e) {
            include 'writeconfig.php';
            exit;
        }

    }

    $title = 'Settings';
    include 'top.php';

?>

    <div id="form-div">


        <h2>
            Technical settings
        </h2>

        <?php

            if (!empty($messages)) {
                include 'messages.php';
            } else {

                ?>
                <p>
                    Great! You have everything you need to get started.
                </p>
                <p>
                    On this screen, we'll ask you how we should connect to your database, and where we should save
                    uploaded files
                    like user photos, pictures and audio.
                </p>
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
                    <input type="text" name="site_title" placeholder="" value="<?= htmlspecialchars($site_title) ?>"
                           class="profile-input" id="site_title">
                </p>
            </div>
            <div class="settings-group">
                <h3>
                    2. Your MySQL settings
                </h3>

                <p class="control-label">
                    Known needs a single MySQL database, with a user that can connect to it. We recommend that this
                    is a user you have created just for Known, rather than one you share with other applications.
                </p>
                <p>
                    <label class="control-label">
                        MySQL database name<br>
                        <input type="text" name="mysql_name" placeholder="" value="<?= htmlspecialchars($mysql_name) ?>"
                               class="profile-input" required>
                    </label>
                </p>

                <p>
                    <label class="control-label">
                        MySQL username<br>
                        <input type="text" name="mysql_user" placeholder="" value="<?= htmlspecialchars($mysql_user) ?>"
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
                        <input type="text" name="mysql_host" placeholder="<?= htmlspecialchars($mysql_host) ?>"
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
                        The full path to a folder that Known can upload to. This shouldn't be in your web server's
                        document root. Your web server does need to be able to read and write to it.
                    </label>
                </p>

                <p>
                    <input type="text" name="upload_path" id="upload_path" placeholder=""
                           value="<?= htmlspecialchars($upload_path) ?>" class="profile-input" required>
                </p>
            </div>
            <div class="submit settings-group page-bottom">
                <input type="submit" class="btn btn-primary btn-lg btn-responsive" value="Onwards!">
            </div>
        </form>

    </div>

<?php

    include 'bottom.php';
