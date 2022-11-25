<div class="row">

    <div class="col-md-10 col-md-offset-1">
	            <?=$this->draw('admin/menu')?>
        <h1><?= \Idno\Core\Idno::site()->language()->_('OAuth2 Client'); ?></h1>

    </div>

</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        
            <div class="controls-group">
                <div class="controls-config">
                    <p>
			<?= \Idno\Core\Idno::site()->language()->_('Configure your OAuth2 Apps here.'); ?>
		    </p>
                    
                </div>
            </div>
            
            <div class="controls-group">
		
		<div class="row">
		    <div class="col-md-10">
			<h3><?= \Idno\Core\Idno::site()->language()->_('Configure client details'); ?></h3>
		    </div>
		</div>
		
		<div class="controls-group">
		<?php
		if ($clients = \IdnoPlugins\OAuth2Client\Entities\OAuth2Client::get()) {
		
		    foreach ($clients as $client) {

			echo $this->__(['object' => $client])->draw('admin/oauth2client/form');

		    }
		    
		}
		
		?>
		</div>
		
		<hr>
		
		<div class="well">
		<?php
		
		echo $this->__(['object' => ''])->draw('admin/oauth2client/form');
		
		?>
		</div>
		
                    
            </div>
            
                        
            
        
    </div>
</div>