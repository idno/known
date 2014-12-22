<div class="row">

    <div class="span10 offset1">
	    <?=$this->draw('admin/menu')?>
        <h1>
            Site configuration
        </h1>
        
        <div class="explanation">
            <p>On this page you can change the basic configurations for your site,
                like its name and the number of items of content on each page.
                To add new types of content or new features, visit the
                <a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/plugins/">plugins</a>.
            </p>
        </div>
    </div>
</div>
<div class="row">
    <div class="span10 offset1">
        <form action="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/" class="form-horizontal" method="post">

		<div class="row">
            <div class="span2">
                <p class="control-label" for="name"><strong>Site name</strong></p>
            </div>
                <div class="span4">
                    <input type="text" id="name" placeholder="Site name" class="span4" name="title" value="<?=htmlspecialchars(\Idno\Core\site()->config()->title)?>" ></div>
                 <div class="span4">
                    <p class="config-desc">Give your site a name!</p>
                    </div>
		</div>
		
<!-------->	

		<div class="row">
			<div class="span2">
				<p class="control-label" for="description"><strong>Site description</strong></p>
			</div>
			<div class="span4">
					<input type="text" id="name" placeholder="Site description" class="span4" name="description" value="<?=htmlspecialchars(\Idno\Core\site()->config()->description)?>" >
			</div>
			<div class="span4">
                <p class="config-desc">You might want to add a short tagline for your site.</p>
                </div>            
		</div>
		
<!---------->
            <div class="row">
	            <div class="span2">
                	<p class="control-label" for="url"><strong>Site URL</strong></p>
	            </div>
                <div class="span4">
                    <input type="url" id="url" placeholder="Site URL" class="span4" name="url" value="<?=htmlspecialchars(\Idno\Core\site()->config()->getDisplayURL())?>" >
                </div>
                <div class="span4">
	                <p class="config-desc">This is your site's URL.</p>
	                </div>
            </div>
            
<!--------->                
            
            <div class="row">
	            <div class="span2">
                	<p class="control-label" for="open_registration"><strong>Allow registration</strong></p>
	            </div>
                <div class="span4">
                    <select class="span4" name="open_registration">
                        <option value="true" <?php if (\Idno\Core\site()->config()->open_registration == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->open_registration == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
                <div class="span4">
	                <p class="config-desc">Allow registration if you want others to sign up for your site.</p>
	             </div>
            </div>
            
<!---------->
            <div class="row">
	            <div class="span2">
                	<p class="control-label" for="open_registration"><strong>Set site private</strong></p>
	            </div>
                <div class="span4">
                    <select class="span4" name="walled_garden">
                        <option value="false" <?php if (\Idno\Core\site()->config()->walled_garden == false) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="true" <?php if (\Idno\Core\site()->config()->walled_garden == true) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
                <div class="span4"><p class="config-desc">Content on a private site is only visible if you're logged in.</p>
                </div>
            </div>
            
<!---------->

            <div class="row">
	            <div class="span2">
                	<p class="control-label" for="hub"><strong>PubSubHubbub hub</strong></p>
	            </div>
                <div class="span4">
                    <input type="url" id="hub" placeholder="PubSubHubbub hub address" class="span4" name="hub" value="<?=htmlspecialchars(\Idno\Core\site()->config()->hub)?>" >
                </div>
                <div class="span4"><p class="config-desc">Learn more about <a href="https://code.google.com/p/pubsubhubbub/" target="_blank">PubSubHubbub</a>.</p>
                </div>
            </div>

<!----------->

            <div class="row">
	            <div class="span2">
                	<p class="control-label" for="items_per_page"><strong>Items per page</strong></p>
	            </div>
                <div class="span4">
                    <input type="text" id="items_per_page" placeholder="10" class="span4" name="items_per_page" value="<?=htmlspecialchars(\Idno\Core\site()->config()->items_per_page)?>" >
                </div>
                <div class="span4"><p class="config-desc">The number of items displayed on each page.</p>
                </div>
            </div>
            
<!----------->

            <div class="row">
	            <div class="span2">
                	<p class="control-label" for="user_avatar_favicons"><strong>User avatar as icon</strong></p><!-- We need to take a look at this one and make sure that the language is clear; it's a little confusing right now.--->
	            </div>
                <div class="span4">
                    <select class="span4" name="user_avatar_favicons">
                        <option value="true" <?php if (\Idno\Core\site()->config()->user_avatar_favicons == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->user_avatar_favicons == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
            </div>
            
            <!--Why is this commented out? <div class="control-group">
                <label class="control-label" for="items_per_page">Include citations<br /><small>Include a unique, citable code at the bottom of every post.</small></label>
                <div class="controls">
                    <select class="span4" name="indieweb_citation">
                        <option value="true" <?php if (\Idno\Core\site()->config()->indieweb_citation == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->indieweb_citation == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                </div>
            </div> -->
            
            
<!---------->            
            <div class="row">
	            <div class="span2">
                	<p class="control-label" for="items_per_page"><strong>Include permalinks</strong></p>
	            </div>
                <div class="span4">
                    <select class="span4" name="indieweb_reference">
                        <option value="true" <?php if (\Idno\Core\site()->config()->indieweb_reference == true) echo 'selected="selected"'; ?>>Yes</option>
                        <option value="false" <?php if (\Idno\Core\site()->config()->indieweb_reference == false) echo 'selected="selected"'; ?>>No</option>
                    </select>
                    
                </div>
                <div class="span4"><p class="config-desc">Include a permalink to the original post when you syndicate.</p>
                </div>
            </div>
            
<!---------->            
            <div class="control-group">
                <div class="controls-save">
                    <button type="submit" class="btn btn-primary">Save updates</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/admin/')?>

        </form>
    </div>

</div>


