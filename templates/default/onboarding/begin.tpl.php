<div id="form-main">

    <div>

        <div class="h-register"><img src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>gfx/onboarding/logo_black.png"
                                     alt="Known" class="img-responsive"></div>

        <p class="p-register"><?php echo \Idno\Core\Idno::site()->language()->_('Known is your space for sharing content and discussing ideas.'); ?></p>

        <div class="container" style="margin-bottom: 1 em; margin-top: 2em">
            <div class="row row-centered">
                <div class="scoot col-centered col-max">
                    <img class="img-responsive" src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>gfx/onboarding/kite.png" alt="<?php echo \Idno\Core\Idno::site()->language()->_('Take a picture'); ?>"
                         width="100%">
                </div>
                <div class="scoot col-centered col-max">
                    <img class="img-responsive" src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>gfx/onboarding/text.png" alt="<?php echo \Idno\Core\Idno::site()->language()->_('Share a message'); ?>"
                         width="100%">
                </div>
                <div class="scoot col-centered col-max">
                    <img class="img-responsive" src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>gfx/onboarding/map.png" alt="<?php echo \Idno\Core\Idno::site()->language()->_('Save your location'); ?>"
                         width="100%">
                </div>
            </div>
        </div>


        <?php echo $this->draw('shell/simple/messages') ?>

        <div align="center">
            <form class="form" method="get" action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>begin/register/">

                <div class="col-md-12 submit">
                    <input class="btn btn-primary btn-lg btn-responsive" type="submit" value="<?php echo \Idno\Core\Idno::site()->language()->_('Get started'); ?>">

                </div>


            </form>
        </div>

        <p class="signin" align="center"><a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>session/login/"><?php echo \Idno\Core\Idno::site()->language()->_('Already have an account? Sign in.'); ?></a></p>

        <div class="space">&nbsp;</div>

    </div>
</div>
