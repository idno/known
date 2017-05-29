<?php
    // Display the login link, if the user is not currently logged in.
    // If they're logged out, this is probably why they're denied.
    if (!\Idno\Core\Idno::site()->session()->isLoggedIn()) {
        ?>
        <a id="soft-forward"
           href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login?fwd=' . Idno\Core\Webservice::base64UrlEncode($_SERVER['REQUEST_URI']); ?>">Click
            here to log in.</a>
        <script>
            $('#soft-forward').hide();  // JS users will be forwarded anyway
        </script>
        <?php
    } else {
        ?>
        <div class="container result-403">
            <div class="row" style="margin-bottom: 2em; margin-top: 6em">
                <div class="col-md-offset-1 col-md-5">
                    <h1 class="p-name" style="margin-bottom: 2em;">
                        Hold on. You don't have access to this content.
                    </h1>
                    <p>It's nothing personal. You just don't have the right permissions to see what's here. </p>
                    <p>
                        Find something else to view on the <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>"><?= \Idno\Core\Idno::site()->config()->title ?> homepage</a>.
                    </p>                    
                </div>
                <div class="col-md-5">
					<img src="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/robots/aleph_403.png" alt="Robot with a stop sign">
                </div>                
            </div>
        </div>
    <?php } ?>