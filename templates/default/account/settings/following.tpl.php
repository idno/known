<div class="row">
    
    <div class="span10 offset1">
        <h1>
            Following...
        </h1>
        <?= $this->draw('account/menu') ?>
	
	<div class="explanation">
            <p>
                Manage your subscriptions here. These users can also be given access to private content you produce on your site. 
            </p>
        </div>
	
	<div class="well">
	    <p>Use this bookmarklet to make is easy
		to add new friends.</p>
	
	    <?= $this->draw('account/settings/following/bookmarklet'); ?>
	</div>
	
    </div>
</div>