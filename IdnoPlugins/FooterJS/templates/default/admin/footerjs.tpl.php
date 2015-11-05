<div class="row">

    <div class="col-md-10 col-md-offset-1">
	            <?=$this->draw('admin/menu')?>
        <h1>Custom JavaScript</h1>

        <div class="explanation">
            <p>
            This editor lets you easily customize your website using a combination of JavaScript and HTML in the header and footer. You can use this space to add code to your site pages for things like analytics, page optimization, and customer tracking.                    </p>
        </div>
    </div>

</div>
<form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/footerjs/" class="form-horizontal" method="post">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
		<h2>Code editor</h2>
		</div>
	</div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
	                <p class="js-controls"><strong>Header code</strong></p>
                    <textarea class="form-control" name="headerjs"><?=htmlspecialchars(\Idno\Core\Idno::site()->config()->headerjs)?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
	               <p class="js-controls"><strong>Footer code</strong></p> 
                    <textarea name="footerjs" class="form-control"><?=htmlspecialchars(\Idno\Core\Idno::site()->config()->footerjs)?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
	             
                    <button type="submit" class="btn btn-primary code">Save code</button>
	                
                </div>
            </div>
            <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/footerjs/')?>
        </form>
