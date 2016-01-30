<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <h1>Dependencies</h1>
        <?=$this->draw('admin/menu')?>
        <div class="explanation">
            <p>
                The following are system components that are required for Known to fully run.
                It's worth checking to make sure that they're all installed. If you need help
                installing any required packages, ask your web host or system administrator.
            </p>
        </div>
    </div>

</div>
<div class="row">

    <div class="col-md-5 col-md-offset-1">
        <h3>PHP</h3>
        <p>
            Version 5.4 or greater<br />
            <?php

                echo $this->__(array('version' => '5.4.0'))->draw('admin/dependencies/php');

            ?>
        </p>
        <p>
            PHP extensions required<br />
            <small>These extend the way PHP works, and are required for functionality like
            database storage, webmentions, etc. Click an extension name to learn more about it,
            including installation instructions.</small><br />
            <?php

                foreach(array(
                            //'curl','date','dom','fileinfo','gd','intl','json','libxml','mbstring','mongo','oauth','reflection','session','simplexml'
                            'curl','date','dom','gd','json','libxml','mbstring','pdo','pdo_mysql','reflection','session','simplexml', 'openssl'
                        ) as $extension) {
		     echo $this->__(array('extension' => $extension))->draw('admin/dependencies/extension');
                }

            ?>
        </p>
    </div>

    <div class="col-md-5">

    </div>

</div>