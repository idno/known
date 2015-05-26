<?php
    $user = \Idno\Core\site()->session()->currentUser();
?>
<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('account/menu') ?>
        <h1>
            Tools and Apps
        </h1>

        <div>
            <h2>Bookmarklet</h2>
        </div>
    </div>
</div>


<div class="row">
	<div class="col-md-4 col-md-offset-1">
            <p>
                The Known bookmarklet is the best way to save links, reply to posts, and share articles.</p> 
                <p>Just drag the bookmarklet button below into your browser's Bookmark Bar. 
            </p>
            <p>
                <?=$this->draw('entity/bookmarklet'); ?>             </p>
        </div>
	<div class="col-md-4 col-md-offset-1">
		<p>
			<img src="<?= \Idno\Core\site()->config()->getDisplayURL() ?>gfx/other/bookmarklet-mouse.png" alt="bookmarklet-mouse" class="img-responsive" />
		</p>
        </div>
</div>


<div class="row">
	<div class="col-md-4 col-md-offset-1">
		<img src="<?= \Idno\Core\site()->config()->getDisplayURL() ?>gfx/other/bookmarklet.png" alt="bookmarklet" class="img-responsive"  />
	</div>
	<div class="col-md-4 col-md-offset-1">
        <p>
            <strong>Don't see a bookmarks bar?</strong>
        </p>
        <p>
            Sometimes web browsers have their bookmarked links bar hidden by default. Here's how to reveal it:</p>
			<p>
            <strong>Chrome:</strong> Select <em>Always Show Bookmarks Bar</em> from the <em>View</em> menu.<br>
            <strong>Firefox:</strong> Select <em>View</em>, then <em>Toolbars</em>, and make sure
            <em>Bookmarks Toolbar</em> is checked.<br>
            <strong>Internet Explorer:</strong> Select <em>Tools</em>, then make sure <em>Favorites Bar</em> is
            checked.
        </p>
    </div>
</div>

<?=$this->draw('account/settings/tools/list')?>

<div class="row" style="margin-top: 2em">

    <div class="col-md-4 col-md-offset-1">

        <h2>API</h2>
        <p>
            Your API key: <input type="text" id="apikey" class="form-control" name="apikey"
                                 value="<?= htmlspecialchars($user->getAPIkey()) ?>" readonly>
        </p>

    </div>

</div>