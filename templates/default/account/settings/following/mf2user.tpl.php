<?php
$mf2_user = $vars['mf2'];

$name = $mf2_user['properties']['name'][0];
$urls = array_unique($mf2_user['properties']['url']);
$photo = $mf2_user['properties']['photo'][0];
?>
<form action="<?= \Idno\Core\site()->config()->url ?>account/settings/following/bookmarklet" method="post" class="form-horizontal">
    <div class="row idno-entry following-user">

	<div class="span1 offset1 owner h-card hidden-phone">
	    <p>
		<span class="u-url icon-container"><img class="u-photo" src="<?= $photo ?>" /></span><br />
		<?= $name; ?>
	    </p>
	</div>
	<div class="span8 idno-object idno-content">
	    <div class="visible-phone">
		<p class="p-author author h-card vcard">
		    <img class="u-logo logo u-photo photo" src="<?= $photo ?>" />
		    <span class="p-name fn u-url url"><?= $name ?></span>
		</p>
	    </div>
	    <div class="e-content entry-content">
		<?php
		foreach ($urls as $url) {
		    ?>
    		<input type="hidden" name="urls[]" value="<?= $url; ?>" />
		    <?php
		}
		?>
		<div class="control-group">
		    <label class="control-label" for="inputName">Name</label>

		    <div class="controls">
			<input id="inputName" type="text" class="span4" name="name"
			       value="<?= htmlspecialchars($name) ?>">
		    </div>
		</div>
		
		<div class="control-group">
		    <label class="control-label" for="inputUrl">Profile URL</label>
		    
		    <div class="controls">
			<?php if (count($urls)>1) { ?>
			<select name="url">
			    <?php
				foreach($urls as $url) {
				    ?>
			    <option><?=$url;?></option>
			    <?php
				}
			    ?>
			</select>
			<?php } else { ?>
			    <a href="<?= $url; ?>" target="_blank"><?= $url; ?></a>
			<?php } ?>
		    </div>

		</div>
		
		<div class="control-group">
		    <div class="controls">
			<button type="submit" class="btn">Add as friend...</button>
		    </div>
		</div>
	    </div>
	    <div class="footer">

	    </div>

	</div>

    </div>


    <?= \Idno\Core\site()->actions()->signForm('/account/settings/following/bookmarklet') ?>

</form>
