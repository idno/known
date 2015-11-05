<div id="form-main">

    <div>

        <div class="h-register"><img src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>gfx/onboarding/logo_black.png"
                                     alt="Known" class="img-responsive"></div>

        <p class="p-register">Known is your space for sharing content and discussing ideas.</p>

        <div class="container" style="margin-bottom: 1 em; margin-top: 2em">
            <div class="row row-centered">
                <div class="scoot col-centered col-max">
                    <img class="img-responsive" src="../../../gfx/onboarding/kite.png" alt="Take a picture"
                         width="100%">
                </div>
                <div class="scoot col-centered col-max">
                    <img class="img-responsive" src="../../../gfx/onboarding/text.png" alt="Share a message"
                         width="100%">
                </div>
                <div class="scoot col-centered col-max">
                    <img class="img-responsive" src="../../../gfx/onboarding/map.png" alt="Save your location"
                         width="100%">
                </div>
            </div>
        </div>


        <?= $this->draw('shell/simple/messages') ?>

        <div align="center">
            <form class="form" method="get" action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>begin/register/">

                <div class="col-md-12 submit">
                    <input class="btn btn-primary btn-lg btn-responsive" type="submit" value="Get started">

                </div>


            </form>
        </div>

        <p class="signin" align="center"><a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>session/login/">Already
                have an account? Sign in.</a></p>

        <div class="space">&nbsp;</div>

    </div>
</div>