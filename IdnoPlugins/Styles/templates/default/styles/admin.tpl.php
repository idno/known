<div class="row">
    <div class="span10 offset1">
        <h1>
            Custom CSS
        </h1>
        <?= $this->draw('admin/menu'); ?>
        <div class="explanation">
            <p>
                The site styles CSS editor lets you easily modify the visual style of your Known site by overriding the default CSS. With Custom CSS, you have more control over the fonts, colors, and visual impact of your site. 
            </p>
        </div>
    </div>
</div>
<form action="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/styles/" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="span10 offset1">
		<h2>Stylesheet editor</h2>
		</div>
	</div>
    <div class="row">
        <div class="span10 offset1">
            Add your changes to Known's core CSS below. <a href="<?= \Idno\Core\site()->config()->url ?>styles/site/">Download
                your stylesheet.</a><br/>
            <textarea class="span10" name="css" style="height: 15em; font-family: Courier, monospace"><?php

                    echo htmlspecialchars($vars['css']);

                ?></textarea>
        </div>
    </div>
    <div class="row">
        <div class="span10 offset1">
            <p>
                Do you have an existing stylesheet that you'd like to use? Import a CSS file from your computer:
                <span class="btn btn-primary btn-file">
                    <span id="css-filename">Select a CSS file</span> <input type="file" name="cssfile" id="cssfile"
                                                                                                       class="span9"/>
                    <input type="file" name="import" accept="text/css" id="cssfileinput" onchange="$('#css-filename').html($('#cssfileinput').val());"/>

                </span>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="span10 offset1">
            <p>
                <input type="submit" class="btn btn-primary" value="Save stylesheet"/>
                <?= \Idno\Core\site()->actions()->signForm(\Idno\Core\site()->config()->getDisplayURL() . 'admin/styles/') ?>
            </p>
        </div>
    </div>
</form>