<div class="row">
    <div class="span10 offset1">
        <h1>
            Site Styles
        </h1>
        <?= $this->draw('admin/menu'); ?>
        <div class="explanation">
            <p>
                Site styles let you easily modify Known's default CSS by overriding it. You can always find more
                Known style templates, as well as tutorials and other resources, on
                <a href="http://withknown.com" target="_blank">the Known website</a>.
            </p>
        </div>
    </div>
</div>
<form action="<?=\Idno\Core\site()->config()->getURL()?>admin/styles/" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="span10 offset1">
            Your changes to Known's core CSS (<a href="<?= \Idno\Core\site()->config()->url ?>styles/site/">download
                this</a>)<br/>
            <textarea class="span10" name="css" style="height: 15em; font-family: Courier, monospace"><?php

                    echo htmlspecialchars($vars['css']);

                ?></textarea>
        </div>
    </div>
    <div class="row">
        <div class="span10 offset1">
            <p>
                Or, import CSS from a file on your computer:
                <input type="file" name="import" accept="text/css"/>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="span10 offset1">
            <p>
                <input type="submit" class="btn btn-primary" value="Save"/>
                <?= \Idno\Core\site()->actions()->signForm(\Idno\Core\site()->config()->getURL() . 'admin/styles/') ?>
            </p>
        </div>
    </div>
</form>