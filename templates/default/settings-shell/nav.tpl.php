
<nav class="navbar navbar-default navbar-fixed-top col-lg-12 col-12 ">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                <span class="sr-only"><?php echo \Idno\Core\Idno::site()->language()->_('Toggle navigation'); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" tabindex="1" href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>"><?php echo
                $this->draw('shell/toolbar/title')
            ?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse">

            <ul class="nav navbar-nav navbar-right">
                <?php

                if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {

                    echo $this->draw('settings-shell/toolbar/logged-in');

                } 
                ?>
            </ul>

        </div><!-- /.navbar-collapse -->
</nav>