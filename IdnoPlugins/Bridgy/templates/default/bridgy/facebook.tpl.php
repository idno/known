<div class="row">

    <div class="col-md-12" style="margin-top: 2em">

        <p>
            <a href="https://brid.gy/facebook/start?feature=listen&amp;callback=<?php echo urlencode(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/facebook/')?>&amp;user_url=<?php echo urlencode(\Idno\Core\Idno::site()->config()->getDisplayURL())?>" ><icon class="icon-plus"></icon> <?php echo \Idno\Core\Idno::site()->language()->_('Connect Brid.gy'); ?></a><br>
            <small>
                <?php echo \Idno\Core\Idno::site()->language()->_('Brid.gy imports comments and likes from Facebook and stores them on your Known site.'); ?>
            </small>
        </p>

    </div>

</div>