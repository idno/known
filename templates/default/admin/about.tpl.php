<div class="row">

    <div class="span10 offset1">
        <h1>About idno</h1>
        <?=$this->draw('admin/menu')?>
    </div>

</div>
<div class="row">
    <div class="span1 offset1">
        <a href="http://idno.co"><img src="http://idno.co/idno.png" style="width: 100%; border: 0"></a>
    </div>
    <div class="span9">
        <p style="font-size: 1.6em"><a href="http://idno.co">Idno</a> is an open source social publishing platform.</p>
        <p>
            Version: <?= \Idno\Core\site()->version(); ?>
        </p>
    </div>
</div>
<div class="row" style="margin-top: 1em">
    <div class="span8 offset1">
        <div style="height: 200em; overflow-y: scroll; background-color: #fff; font-family: monospace; font-size: 0.9em; padding: 2em">
            <?php

                $contributors = file_get_contents(\Idno\Core\site()->config()->path . '/CONTRIBUTORS.md');
                echo $this->autop($this->parseURLs($contributors));

            ?>
        </div>
    </div>
    <div class="span2">
        <iframe src="http://idno.co/patrons.php?css=1" style="width: 100%; height: 200em; border: 0;" id="patrons"></iframe>
    </div>
</div>
