<div class="row">

    <div class="col-md-10 col-md-offset-1">

        <?= $this->draw('account/menu') ?>

        <h1>
            Export your data
        </h1>



        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/export/rss" method="post">

            <!--<h2>
                Export to WordPress
            </h2>-->
            <p class="explanation">
                You can download an RSS version of everything you've posted on this site. This file is suitable for
                importing into content management systems like WordPress, or another Known site.
            </p>

            <div class="row">
                <div class="col-md-3">
                    <p><label class="control-label" for="allposts"><strong>Include private posts?</strong></label></p>
                </div>
                <div class="config-toggle col-md-2">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                           name="allposts" id="allposts"
                           value="0">
                </div>
                <div class="col-md-7">
                    <p class="config-desc">Platforms like WordPress may assume that all your posts should be displayed publicly.
                        In order to protect your privacy, you may wish to just download your public posts.</p>
                </div>
            </div>
            <div class="">
                <button type="submit" class="btn btn-primary">Download RSS Feed</button>
                <?= \Idno\Core\Idno::site()->actions()->signForm('/account/export/rss') ?>
            </div>

        </form>

    </div>

</div>