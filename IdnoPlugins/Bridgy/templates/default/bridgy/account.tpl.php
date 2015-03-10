<div class="row">

    <div class="span10 offset1">

        <?= $this->draw('account/menu') ?>
        <h1>Bridgy</h1>

    </div>

    <div class="span10 offset1">

                <p class="explanation">
				<a href="https://brid.gy">Bridgy</a> is a service that pulls social interactions - such as likes and retweets - back to your website.</p> 
				<p class="explanation">If you send content from Known to Facebook or Twitter, use Bridgy to save comments and  interactions from those networks to the original post on your Known site.</p> 
				<p class="explanation">To get started, activate Bridgy for the social network.

        		</p>

    </div>

</div>
<div class="row">

    <div class="span6 offset1">

        <form action="https://www.brid.gy/facebook/start?feature=listen&callback=<?=urlencode(\Idno\Core\site()->config()->getDisplayURL() . 'account/bridgy/')?>" method="post">
            <p>
                <button class="connect fb">Activate Facebook + Bridgy</button>
            </p>
                <p>
                    Bridgy pulls in comments and likes from Facebook.
                </p>
        </form>
        <form action="https://www.brid.gy/twitter/start?feature=listen&callback=<?=urlencode(\Idno\Core\site()->config()->getDisplayURL() . 'account/bridgy/')?>" method="post">
            <p>
                <button class="connect tw">Activate Twitter + Bridgy</button>
            </p>
                <p>
                    Bridgy pulls in replies, favorites, and retweets from Twitter.
            </p>
        </form>

    </div>

</div>