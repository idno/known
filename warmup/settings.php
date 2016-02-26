<?php

    $ok       = true;
    $messages = '';

    function getInput($name)
    {
        if (!empty($_POST[$name])) {
            return $_POST[$name];
        }

        return '';
    }

    $site_title  = getInput('site_title');
    $mysql_user  = getInput('mysql_user');
    $mysql_host  = getInput('mysql_host');
    $mysql_pass  = getInput('mysql_pass');
    $mysql_name  = getInput('mysql_name');
    $upload_path = getInput('upload_path');

    if (!empty($_SERVER['PHP_SELF'])) {
        if ($subdir = dirname(dirname($_SERVER['PHP_SELF']))) {
            if ($subdir != DIRECTORY_SEPARATOR) {
                if(substr($subdir, -1) == DIRECTORY_SEPARATOR) {
                    $subdir = substr($subdir, 0, -1);
                }
                if (substr($subdir, 0, 1) == DIRECTORY_SEPARATOR) {
                    $subdir = substr($subdir, 1);
                }
                $subdir = str_replace(DIRECTORY_SEPARATOR, '/', $subdir);
            }
        }
    }
    if (empty($subdir)) {
        $subdir = '';
    } else {
        $subdir = '/' . $subdir;
    }

    if (!empty($mysql_name) && !empty($mysql_host)) {
        try {
            $database_string = 'mysql:';
            if (!empty($mysql_host)) {
                $database_string .= 'host=' . $mysql_host . ';';
            }
            $database_string .= 'dbname=' . $mysql_name;
            $dbh = new PDO($database_string, $mysql_user, $mysql_pass);
            if ($schema = @file_get_contents(dirname(dirname(__FILE__)) . '/schemas/mysql/mysql.sql')) {
                $dbh->exec('use `' . $mysql_name . '`');
                if (!$dbh->exec($schema)) {
                    $err = $dbh->errorInfo();
                    if ($err[0] === '00000') {
                        // exec() might return false (no rows affected) and still have been successful
                        // http://php.net/manual/en/pdo.exec.php#118156
                    } else if ($err[0] === '01000') {
                        $messages .= '<p>Installed database schema with warnings: '.$err[2].'.</p>';
                    } else {
                        $messages .= '<p>We couldn\'t automatically install the database schema: '.$err[2].'</p>';
                        $ok = false;
                    }
                }
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

    if (function_exists('apache_get_version')) {
        $host = strtolower($_SERVER['HTTP_HOST']);
        if (!empty($_SERVER['HTTPS'])) {
            $schema = 'https://';
        } else {
            $schema = 'http://';
        }

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $schema . $host . $subdir . '/js/canary.js');
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_HEADER, 1);

        $curl_result = curl_exec($curl_handle);
        $curl_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        if ($curl_status < 200 || $curl_status > 299) {
            $messages .= '<p>Rewriting appears to be disabled. Usually this means "AllowOverride None" is set in apache2.conf ';
            $messages .= 'which prevents Known\'s .htaccess from doing its thing. We tried to fetch a URL that should redirect ';
            $messages .= 'to default.js, but got this response instead:</p>';
            $messages .= '<code><pre>' . htmlspecialchars($curl_result) . '</pre></code>';
            $messages .= '<p>You can usually fix this by setting <code>AllowOverride All</code> in your Apache configuration.</p>';
            $ok = false;
        }

        curl_close($curl_handle);
    }

    if ($ok) {
        if (file_exists('../config.ini')) {
            if ($config = @parse_ini_file('../config.ini')) {
                header('Location: ../begin/register?set_name=' . urlencode($site_title));
                exit;
            }
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

    if ($ok && !empty($upload_path) && !empty($mysql_name) && !empty($mysql_host)) {
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
            if (file_exists(dirname(dirname(__FILE__)) . '/.htaccess')) {
                if ($fp = @fopen(dirname(dirname(__FILE__)) . '/.htaccess', 'a')) {
                    fwrite($fp, "\n\n\n" . file_get_contents(dirname(dirname(__FILE__)) . '/htaccess.dist'));
                }
            } else {
                @rename(dirname(dirname(__FILE__)) . '/htaccess.dist', dirname(dirname(__FILE__)) . '/.htaccess');
            }
            if ($fp = @fopen('../config.ini', 'w')) {
                fwrite($fp, $ini_file);
                fclose($fp);
                header('Location: ../begin/register?set_name='.urlencode($site_title));
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

    if (empty($upload_path)) {
        // New default upload path
        $upload_path = dirname(dirname(__FILE__)) . '/Uploads/';
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
                    uploaded files like user photos, pictures and audio.
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
                    <br><br>
                    You should create your database before entering the details here. If you're using a shared host,
                    you may have an option called "MySQL Database Wizard" that will speed you through the process.
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
                        The full path to a folder that Known can upload to. By default this is the "Uploads" folder -
                        you should leave this as-is unless you're an experienced system administrator. You need to make
                        sure the web server can save data to it. In your file manager, you should be able
                        to select the folder, and click to enable "group" write access.
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
