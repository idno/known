<div class="row">

    <div class="col-md-10 col-md-offset-1">
	            <?=$this->draw('admin/menu')?>
        <h1><?= \Idno\Core\Idno::site()->language()->_('Custom JavaScript'); ?></h1>

        <div class="explanation">
            <p>
            <?= \Idno\Core\Idno::site()->language()->_('This editor lets you easily customize your website using a combination of JavaScript and HTML in the header and footer. You can use this space to add code to your site pages for things like analytics, page optimization, and customer tracking.'); ?>
            </p>
        </div>
    </div>

</div>
<form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/footerjs/" class="form-horizontal" method="post">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
		<h2><?= \Idno\Core\Idno::site()->language()->_('Code editor'); ?></h2>
		</div>
	</div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
	                <p class="js-controls"><strong><?= \Idno\Core\Idno::site()->language()->_('Header code'); ?></strong></p>
                    <textarea class="form-control" name="headerjs"><?=htmlspecialchars(\Idno\Core\Idno::site()->config()->headerjs)?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
	               <p class="js-controls"><strong><?= \Idno\Core\Idno::site()->language()->_('Footer code'); ?></strong></p> 
                    <textarea name="footerjs" class="form-control"><?=htmlspecialchars(\Idno\Core\Idno::site()->config()->footerjs)?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
	             
                    <button type="submit" class="btn btn-primary code"><?= \Idno\Core\Idno::site()->language()->_('Save code'); ?></button>
	                
                </div>
            </div>
            <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/footerjs/')?>
        </form>
