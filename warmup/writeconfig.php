<?php

    $title = 'Save configuration file';
    include 'top.php';

?>

    <div id="form-div">

        <h2>Saving the configuration file</h2>
        <p>
            You're almost there! Unfortunately, Known couldn't save the configuration file. This is common: it usually
            means your server is more secure!
        </p>
        <p>
            To continue, create a new file called <code>config.ini</code> at the root of your Known installation,
            with the following contents.
        </p>
        <p>
            Once you've saved it, just reload this page to continue.
        </p>

        <textarea class="installation"><?=htmlspecialchars($ini_file);?></textarea>

    </div>

<?php

    include 'bottom.php';