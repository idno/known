<div class="row">
	<div class="col-md-12">
		<form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/export/generate/" method="post">
		    <p>
		        <input type="submit" class="btn btn-primary" value="<?= \Idno\Core\Idno::site()->language()->_('Create archive'); ?>">
		        <?php
		
		            echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/generate');
		
		        ?>
		    </p>
		</form>		
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<p class="config-desc">
		    <?= \Idno\Core\Idno::site()->language()->_("It may take a while to generate your archive. You can leave this page while it's working. Once the export file is complete, this page will update, and you'll be able to download the archive right here."); ?>
		</p>		
	</div>
</div>