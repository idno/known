<?php
$mf2_user = $vars['mf2'];

$properties = $mf2_user['properties'];
//if (isset($mf2_user['properties']['author'])) // If there's an author, then use that
     //$properties =$mf2_user['properties']['author'][0];

$name = $properties['name'][0];
$urls = array_unique($properties['url']);
$photo =  $properties['photo'][0];
if (empty($photo)) {
    
    // No photo URL found, lets fake one for niceness
    
    $bn = hexdec(substr(md5($properties['url'][0]), 0, 15));
    $number = 1 + ($bn % 5);
    $photo = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'gfx/users/default-'. str_pad($number, 2, '0', STR_PAD_LEFT) .'.png';
}

$email =  $properties['email'][0];
if (strpos('mailto:', 'mailto:')!==false) $email = substr($email, 7); // Sanitise email

$nickname =  $properties['nickname'][0];

?>
<form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/following/bookmarklet" method="post" class="form-horizontal">
    <div class="row idno-entry following-user">

	<div class="col-md-1 col-md-offset-1 owner h-card hidden-sm">
	    <p>
		<span class="u-url icon-container"><img class="u-photo" src="<?= $photo ?>" /></span><br />
		<?= $name; ?>
	    </p>
	</div>
	<div class="col-md-8 idno-object idno-content">
	    <div class="visible-sm">
		<p class="p-author author h-card vcard">
		    <img class="u-logo logo u-photo photo" src="<?= htmlspecialchars($photo) ?>" />
		    <span class="p-name fn"><?= $name ?></span>
		</p>
	    </div>
	    <div class="e-content entry-content">
		<?php
		foreach ($urls as $url) {
		    ?>
    		<input type="hidden" name="urls[]" value="<?= htmlspecialchars($url); ?>" />
		    <?php
		}
		?>
                <input type="hidden" name="photo" value="<?= htmlspecialchars($photo) ?>" />
                
		<div class="control-group">
		    <label class="control-label" for="inputName">Name</label>

		    <div class="controls">
			<input id="inputName" type="text" class="col-md-4" name="name" required
			       value="<?= htmlspecialchars($name) ?>">
		    </div>
		</div>
		
		<div class="control-group">
		    <label class="control-label" for="inputNickname">Nickname</label>

		    <div class="controls">
			<input id="inputNickname" type="text" class="col-md-4" name="nickname" required placeholder="Handle (for your reference)"
			       value="<?= htmlspecialchars($nickname) ?>">
		    </div>
		</div>
		
		<div class="control-group">
		    <label class="control-label" for="inputEmail">Email</label>

		    <div class="controls">
			<input id="inputName" type="email" class="col-md-4" name="email" required
			       value="<?= htmlspecialchars($email) ?>">
		    </div>
		</div>
		
		<div class="control-group">
		    <label class="control-label" for="inputUrl">Profile URL</label>
		    
		    <div class="controls">
			<?php if (count($urls)>1) { ?>
			<select name="uuid">
			    <?php
				foreach($urls as $url) {
				    ?>
			    <option><?= htmlspecialchars($url);?></option>
			    <?php
				}
			    ?>
			</select>
			<?php } else { ?>
			    <a href="<?= $url; ?>" target="_blank"><?= $url; ?></a>
			    <input type="hidden" name="uuid" value="<?= $url; ?>" />
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


    <?= \Idno\Core\Idno::site()->actions()->signForm('/account/settings/following/bookmarklet') ?>

</form>
