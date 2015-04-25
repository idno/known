<?php

    $title = 'Requirements';

    include 'top.php';

    $ok = true;
    $sslrequired = false; // Only warn for now, later we might want to make SSL a requirement

?>

    <div id="form-div">
        <h2>Requirements</h2>
        <p>
            Known needs a few system components and PHP extensions in order to run properly. We'll check them now.
        </p>
        <p>
            (If you have everything you need, there's a "continue" button at the bottom of the page.)
        </p>

        <div class="components">

            <?php

                if (version_compare(phpversion(), '5.4') >= 0) {
                    $class = 'success';
                    $text = 'You are running PHP version ' . phpversion() . '.';
                } else {
                    $class = 'failure';
                    $text = 'You are running PHP version ' . phpversion() . ', which cannot run Known. You may need to ask your server administrator to upgrade PHP for you.';
                    $ok = false;
                }

            ?>
            <div class="component <?=$class?>">

                <h3>PHP 5.4 or above</h3>
                <p>
                    <?=$text?>
                </p>

            </div>
            
            
            <?php
                /* 
                 * Check whether you're installing on a secure connection (and presumably your site is secure).
                 * This is a warning for now, in future this might be a hard fail. 
                 */
            
                function isTLS() {
                    if (isset($_SERVER['HTTPS'])) {
                        if ($_SERVER['HTTPS'] == '1')
                            return true;
                        if (strtolower($_SERVER['HTTPS'] == 'on'))
                            return true;
                    } else if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443'))
                        return true;

                    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                        return true;
                    }

                    return false;
                }
                
                if (isTLS()) {
                    $class = 'success';
                    $text = 'You are running Known on a secure site.';
                } else {
                    if ($sslrequired) {
                        $class = 'failure';
                        $ok = false;
                    } else {
                        $class = 'warning';
                    }
                    $text = 'You appear to be running Known on an unsecured site. We strongly recommend obtaining a certificate to make your site secure, and protect the privacy of your users.';
                }
            ?>
            <div class="component <?= $class ?>">
                <h3>Secure site</h3>
                <p>
                    <?=$text?>
                </p>
            </div>

            <?php

                if (function_exists('apache_get_modules')) {
                    $modules = apache_get_modules();
                    if (in_array('mod_rewrite', apache_get_modules())) {
                        $class = 'success';
                        $text = 'mod_rewrite is installed and enabled.';
                    } else {
                        $class = 'failure';
                        $text = 'mod_rewrite is not installed. Known cannot process page URLs without it.';
                        $ok = false;
                    }
                    ?>
                    <div class="component <?=$class?>">

                        <h3>Apache mod_rewrite</h3>
                        <p>
                            <?=$text?>
                        </p>

                    </div>
                    <?php
                } else {
                    ?>
                    <div class="component">

                        <h3>Apache mod_rewrite</h3>
                        <p>
                            We couldn't detect if mod_rewrite was installed. Known cannot process page URLs without it. Proceed with caution.
                        </p>

                    </div>
                <?php
                }

                $extensions = array('curl','date','dom','gd','json','libxml','mbstring','mysql','reflection','session','simplexml');
                asort($extensions);
                foreach($extensions as $extension) {
                    if (extension_loaded($extension)) {
                        $class = 'success';
                        $text = 'This extension is installed.';
                    } else {
                        $class = 'failure';
                        $text = 'This extension is not installed.';
                        $ok = false;
                    }
                    ?>
                    <div class="component <?=$class?>">

                        <h3><?=$extension?> for PHP</h3>
                        <p>
                            <?=$text?>
                        </p>

                    </div>
                    <?php
                }

            ?>

        </div>

<?php

    if ($ok) {

        ?>
        <div class="submit page-bottom">
            <p>
                <a class="btn btn-primary btn-lg btn-responsive" href="settings.php">Hooray! Let's get you set up.</a>
            </p>
            <p>
                <small><a href="http://docs.withknown.com/">Want to get set up manually? Here's our documentation.</a></small>
            </p>
        </div>
    <?php

    } else {

        ?>
        <div class="explainer page-bottom">
            <h2>
                We can't move on quite yet.
            </h2>
            <p>
                Unfortunately it looks like you need to install a few things. If you need to, ask your system administrator.
                Scroll up for more details, and <a href="https://withknown.com">check out our website</a> for more services
                and information.
            </p>
            <p>
                <small><a href="http://docs.withknown.com/">Want more information? Here's our documentation.</a></small>
            </p>
        </div>
    <?php

    }

?>

    </div>

<?php

    include 'bottom.php';

?>