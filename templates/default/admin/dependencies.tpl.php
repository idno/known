<div class="row">

    <div class="span10 offset1">
        <h1>Dependencies</h1>
        <?=$this->draw('admin/menu')?>
        <div class="explanation">
            <p>
                The following are system components that are required for idno to fully run.
                It's worth checking to make sure that they're all installed. If you need help
                installing any required packages, ask your web host or system administrator.
            </p>
        </div>
    </div>

</div>
<div class="row">

    <div class="span5 offset1">
        <h3>PHP</h3>
        <p>
            Version 5.4 or greater<br />
            <?php

                if (strnatcmp(phpversion(),'5.4.0') <= 0) {
                    $label = 'label-important';
                } else {
                    $label = 'label-success';

                    ?><span class="label <?=$label?>"><?=phpversion()?> installed</span><?php
                }

            ?>
        </p>
        <p>
            PHP extensions required<br />
            <small>These extend the way PHP works, and are required for functionality like
            database storage, webmentions, etc. Click an extension name to learn more about it,
            including installation instructions.</small><br />
            <?php

                foreach(['curl','date','dom','fileinfo','gd','intl','json','libxml','mbstring','mongo','oauth','reflection','session','simplexml'] as $extension) {
                    if (extension_loaded($extension)) {
                        $label = 'label-success';
                    } else {
                        $label = 'label-important';
                    }
                    ?><span class="label <?=$label?>"><a href="http://php.net/<?=urlencode($extension)?>" target="_blank" style="color: #fff"><?=$extension?></a></span> <?php
                }

            ?>
        </p>
    </div>

    <div class="span5">

    </div>

</div>