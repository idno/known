<?php
    // Display the login link, if the user is not currently logged in.
    // If they're logged out, this is probably why they're denied.
    if (!\Idno\Core\Idno::site()->session()->isLoggedIn()) {
        ?>
        <a id="soft-forward"
           href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login?fwd=' . urlencode($_SERVER['REQUEST_URI']); ?>">Click
            here to log in.</a>
        <script>
            $('#soft-forward').hide();  // JS users will be forwarded anyway
        </script>
        <?php
    } else {
        ?>
        <div class="h-entry result-403">
            <div class="row" style="margin-bottom: 2em; margin-top: 4em">
                <div class="col-md-offset-1 col-md-10">
                    <h1 class="p-name">
                        You don't have access to this content.
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-offset-1 col-md-10">
                    <p class="p-summary">It looks like you don't have permission to view this content. Sorry!</p>
                    <p>
                        <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>">Click here to head back to
                            the <?= \Idno\Core\Idno::site()->config()->title ?> homepage</a>.
                    </p>
                </div>
            </div>
        </div>
    <?php } ?>