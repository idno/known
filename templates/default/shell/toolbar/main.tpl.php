<a href="#maincontent" style="display:none">Skip to main content</a>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" tabindex="1" href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>"><?=
                    $this->draw('shell/toolbar/title')
                ?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse">

            <?php
                if (\Idno\Core\Idno::site()->config()->isPublicSite() || \Idno\Core\Idno::site()->session()->isLoggedOn()) {
                    echo $this->draw('shell/toolbar/search');

                    echo $this->draw('shell/toolbar/content');
                }
            ?>

            <ul class="nav navbar-nav navbar-right">
                <?php

                    echo $this->draw('shell/toolbar/links');

                    if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {

                        echo $this->draw('shell/toolbar/logged-in');

                    } else {

                        echo $this->draw('shell/toolbar/logged-out');

                    }

                ?>
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>