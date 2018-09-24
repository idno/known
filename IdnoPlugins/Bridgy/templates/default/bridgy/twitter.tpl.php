<div class="row">

    <div class="col-md-10 col-md-offset-1" style="margin-top: 2em">

        <p>
            <a href="https://brid.gy/twitter/start?feature=listen&amp;callback=<?php echo urlencode(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/twitter/'); ?>&amp;user_url=<?php echo urlencode(\Idno\Core\Idno::site()->getDisplayURL()); ?>" class="btn btn-primary"><?php echo \Idno\Core\Idno::site()->language()->_('Import replies and retweets'); ?></a><br>
            <small>
                <?php echo \Idno\Core\Idno::site()->language()->_('Brid.gy imports replies, favorites and retweets from Twitter and stores them on your Known site.'); ?>
            </small>
        </p>

    </div>

</div>