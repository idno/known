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

        <form action="https://www.brid.gy/facebook/start?feature=listen&callback=<?=urlencode(\Idno\Core\site()->config()->getDisplayURL() . 'account/bridgy/')?>" method="post">
            <p>
                <button class="connect fb">Connect Facebook</button><br>
                <small>
                    Brid.gy imports comments and likes from Facebook.
                </small>
            </p>
        </form>
        <form action="https://www.brid.gy/twitter/start?feature=listen&callback=<?=urlencode(\Idno\Core\site()->config()->getDisplayURL() . 'account/bridgy/')?>" method="post">
            <p>
                <button class="connect tw">Connect Twitter</button><br>
                <small>
                    Brid.gy imports replies, favorites and retweets from Twitter.
                </small>
            </p>
        </form>

    </div>

</div>