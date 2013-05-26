<div class="row">

    <div class="span10 offset1">
        <h1>Administration</h1>
        <?=$this->draw('admin/menu')?>
    </div>
</div>
<div class="row">
    <div class="span10 offset1">
        <form action="/admin/" class="form-horizontal" method="post">

            <div class="control-group">
                <label class="control-label" for="name">Site name<br /><small>This can be anything you want. Except probably Facebook.</small></label>
                <div class="controls">
                    <input type="text" id="name" placeholder="Site name" class="span4" name="title" value="<?=htmlspecialchars(\Idno\Core\site()->config()->title)?>" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="url">Website URL<br /><small>The full URL to your idno-powered site. Include a trailing slash.</small></label>
                <div class="controls">
                    <input type="url" id="url" placeholder="Site URL" class="span4" name="url" value="<?=htmlspecialchars(\Idno\Core\site()->config()->url)?>" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="host">Website host<br /><small>Just the hostname of your idno-powered site (no http:// or trailing slash).</small></label>
                <div class="controls">
                    <input type="text" id="host" placeholder="Site hostname" class="span4" name="host" value="<?=htmlspecialchars(\Idno\Core\site()->config()->host)?>" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="path">Full path to installation<br /><small>Make sure you don't include a trailing slash.</small></label>
                <div class="controls">
                    <input type="text" id="path" placeholder="Path to idno installation" class="span4" name="path" value="<?=htmlspecialchars(\Idno\Core\site()->config()->path)?>" >
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/admin/')?>

        </form>
    </div>

</div>