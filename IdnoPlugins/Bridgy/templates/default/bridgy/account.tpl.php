<div class="row">

    <div class="span10 offset1">

        <?= $this->draw('account/menu') ?>
        <h1>Brid.gy</h1>

    </div>

    <div class="span10 offset1">

        <p class="explanation">

            <a href="https://brid.gy">Brid.gy</a> is a partner service that allows you to import interactions
            on your syndicated content back into your known site. To get started importing your content, click
            below to set up your account:

        </p>

    </div>

</div>
<div class="row">

    <div class="span6 offset1">

        <p>
            <a href="https://brid.gy/facebook/start?feature=listen&callback=<?=urlencode(\Idno\Core\site()->config()->getDisplayURL() . 'account/bridgy/')?>" class="connect fb">Connect Facebook</a><br>
            <small>
                Brid.gy imports comments and likes from Facebook.
            </small>
        </p>
        <p>
            <a href="https://brid.gy/twitter/start?feature=listen&callback=<?=urlencode(\Idno\Core\site()->config()->getDisplayURL() . 'account/bridgy/')?>" class="connect tw">Connect Twitter</a><br>
            <small>
                Brid.gy imports replies, favorites and retweets from Twitter.
            </small>
        </p>

    </div>

</div>