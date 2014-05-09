<div class="row" style="margin-top: 5em">
    <div class="span6 offset3">
	<div class="no-content explanation">
	    <p>
		<?php if (\Idno\Core\site()->currentPage->getInput('q')) { 
		    // Search term
		    ?>
		    Sorry, there's nothing here that matches your term!
		<?php } else { ?>
		    Sorry, but there's nothing here yet!
		<?php } ?>
		    
		<?php
		    if (\Idno\Core\site()->session->isLoggedIn()) {
			?>
		    <br />Why not create something?
			<?php
		    }
		?>
	    </p>
	</div>
    </div>
</div>