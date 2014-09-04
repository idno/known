<?php

    $title = 'Requirements';

    include 'top.php';

    $ok = true;

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

                if (version_compare(phpversion(), '5.4.0') >= 0) {
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

                $extensions = ['curl','date','dom','fileinfo','gd','intl','json','libxml','mbstring','mysql','oauth','reflection','session','simplexml', 'xmlrpc'];
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
                Scroll up for more details, and <a href="http://withknown.com">check out our website</a> for more services
                and information.
            </p>
        </div>
    <?php

    }

?>

    </div>

<?php

    include 'bottom.php';

?>