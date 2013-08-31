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
                <label class="control-label" for="open_registration">Open registration<br /><small>Can anyone register for this site?</small></label>
                <div class="controls">
                    <select class="span4" name="open_registration">
                        <option value="true" <?php if (\Idno\Core\site()->config()->open_registration == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->open_registration == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="secure_sensitive_pages">Secure sensitive pages<br /><small>Are security sensitive pages (login, registration, etc) required to use https?</small></label>
                <div class="controls">
                    <select class="span4" name="secure_sensitive_pages">
                        <option value="true" <?php if (\Idno\Core\site()->config()->secure_sensitive_pages == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->secure_sensitive_pages == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="url">Website URL<br /><small>The full URL to your idno-powered site. Include a trailing slash.</small></label>
                <div class="controls">
                    <input type="text" id="url" placeholder="Site URL" class="span4" name="url" value="<?=htmlspecialchars(\Idno\Core\site()->config()->url)?>" >
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
                <label class="control-label" for="items_per_page">Items per page<br /><small>The number of items you want displayed on a single page.</small></label>
                <div class="controls">
                    <input type="text" id="items_per_page" placeholder="10" class="span4" name="items_per_page" value="<?=htmlspecialchars(\Idno\Core\site()->config()->items_per_page)?>" >
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
