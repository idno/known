<div class="container result-410">
    <div class="row" style="margin-bottom: 2em; margin-top: 6em">

        <div class="col-md-offset-1 col-md-5">
            <h1 class="p-name" style="margin-bottom: 2em;">
               <?php echo \Idno\Core\Idno::site()->language()->_("Sorry, this content isn't here anymore."); ?>
            </h1>
            <p><?php echo \Idno\Core\Idno::site()->language()->_("You may be wondering where it went, but we can't tell you. It's a secret."); ?></p>
            <p>
                <?php echo \Idno\Core\Idno::site()->language()->_("Maybe you'd like to head back to the <a href=\"%s\">%s homepage</a> instead.", [\Idno\Core\Idno::site()->config()->getDisplayURL(), \Idno\Core\Idno::site()->config()->title]); ?>
            </p>            
        </div>
        <div class="col-md-5">
            <img src="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/robots/aleph_410.png" alt="Robot with a gone sign">
        </div>
        
    </div>
</div>