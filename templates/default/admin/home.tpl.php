<div class="row">

    <div class="span10 offset1">
        <h1>
            Site configuration
        </h1>
        <?=$this->draw('admin/menu')?>
        <div class="explanation">
            <p>On this page you can change the basic configurations for your site,
                like its name and the number of items of content on each page.
                To add new kinds of content, and new functionality, visit
                <a href="<?=\Idno\Core\site()->config()->url?>admin/plugins/">Site Features</a>.
            </p>
        </div>
    </div>
</div>
<div class="row">
    <div class="span10 offset1">
        <form action="<?=\Idno\Core\site()->config()->url?>admin/" class="form-horizontal" method="post">

            <div class="control-group">
                <label class="control-label" for="name">Site name</label>
                <div class="controls">
                    <input type="text" id="name" placeholder="Site name" class="span4" name="title" value="<?=htmlspecialchars(\Idno\Core\site()->config()->title)?>" ></div>
                    <div class="controls">
                    <small>Give your site a name!</small></div>
                
            </div>
            <div class="control-group">
                <label class="control-label" for="description">Site description</label>
                <div class="controls">
                    <input type="text" id="name" placeholder="Site description" class="span4" name="description" value="<?=htmlspecialchars(\Idno\Core\site()->config()->description)?>" >
                </div>
                <div class="controls">
                <small>A short description of what your site is about. This is sometimes used by search engines.</small></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="open_registration">Open registration</label>
                <div class="controls">
                    <select class="span4" name="open_registration">
                        <option value="true" <?php if (\Idno\Core\site()->config()->open_registration == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->open_registration == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
                <div class="controls"><small>Can anyone register for this site? If you're installing Known as a personal site or a closed-membership site, you'll want to turn this off.</small></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="open_registration">Public site</label>
                <div class="controls">
                    <select class="span4" name="walled_garden">
                        <option value="false" <?php if (\Idno\Core\site()->config()->walled_garden == false) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="true" <?php if (\Idno\Core\site()->config()->walled_garden == true) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
                <div class="controls"><small>Do you want the content on this site to be public to non-members?</small></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="url">Website URL</label>
                <div class="controls">
                    <input type="url" id="url" placeholder="Site URL" class="span4" name="url" value="<?=htmlspecialchars(\Idno\Core\site()->config()->url)?>" >
                </div>
                <div class="controls"><small>This is the full URL to your Known site.</small></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="hub">PubSubHubbub hub</label>
                <div class="controls">
                    <input type="url" id="hub" placeholder="PubSubHubbub hub address" class="span4" name="hub" value="<?=htmlspecialchars(\Idno\Core\site()->config()->hub)?>" >
                </div>
                <div class="controls"><small>The URI of your <a href="https://code.google.com/p/pubsubhubbub/" target="_blank">PubSubHubbub</a> hub.</small></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="items_per_page">Items per page</label>
                <div class="controls">
                    <input type="text" id="items_per_page" placeholder="10" class="span4" name="items_per_page" value="<?=htmlspecialchars(\Idno\Core\site()->config()->items_per_page)?>" >
                </div>
                <div class="controls"><small>The number of items you want displayed on a single page.</small></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="user_avatar_favicons">Use the user's picture as the website icon on pages they own</small></label>
                <div class="controls">
                    <select class="span4" name="user_avatar_favicons">
                        <option value="true" <?php if (\Idno\Core\site()->config()->user_avatar_favicons == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->user_avatar_favicons == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
            </div>
            <!-- <div class="control-group">
                <label class="control-label" for="items_per_page">Include IndieWeb citations<br /><small>Include a unique, citable code at the bottom of every post.</small></label>
                <div class="controls">
                    <select class="span4" name="indieweb_citation">
                        <option value="true" <?php if (\Idno\Core\site()->config()->indieweb_citation == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->indieweb_citation == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="items_per_page">Include IndieWeb references<br /><small>Link back to posts here when they are syndicated to external sites.</small></label>
                <div class="controls">
                    <select class="span4" name="indieweb_reference">
                        <option value="true" <?php if (\Idno\Core\site()->config()->indieweb_reference == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->indieweb_reference == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
            </div> -->
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/admin/')?>

        </form>
    </div>

</div>
