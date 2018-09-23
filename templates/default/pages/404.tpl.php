<div class="container result-404">
    <div class="row" style="margin-bottom: 2em; margin-top: 6em">
        <div class="col-md-offset-1 col-md-5">
            <h1 class="p-name" style="margin-bottom: 2em;">
                <?php echo \Idno\Core\Idno::site()->language()->_("Oops! We couldn't find it."); ?>
            </h1>
            <p><?php echo \Idno\Core\Idno::site()->language()->_("Whatever you were looking for, it's not here. It might have been moved, deleted, or it doesn't exist. Or the robots ate it. That's always a possibility too."); ?></p>
            <p><?php echo \Idno\Core\Idno::site()->language()->_("Maybe you'll find something interesting if you head back to the <a href=\"%s\">%s homepage</a>.", [\Idno\Core\Idno::site()->config()->getDisplayURL(), \Idno\Core\Idno::site()->config()->title]); ?>
            </p>             
        </div>
        <div class="col-md-5">
            <img src="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/robots/aleph_404.png" alt="Robot with a missing sign">
        </div>        
    </div>
</div>