<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('admin/menu') ?>
        <?= $this->draw('admin/home/description')?>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/" class="admin" method="post">

            <div class="row">
                <div class="col-md-10">
                    <h3>Site Details</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="name"><strong>Site name</strong></label></p>
                </div>
                <div class="col-md-4">
                    <input type="text" id="name" placeholder="Site name" class="input col-md-4 form-control" name="title"
                           value="<?= htmlspecialchars(\Idno\Core\Idno::site()->config()->title) ?>">
                </div>
                <div class="col-md-6">
                    <p class="config-desc">Give your site a name!</p>
                </div>
            </div>

            <!-------->

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="description"><strong>Site summary</strong></label></p>
                </div>
                <div class="col-md-4">
                    <input type="text" id="name" placeholder="Site description" class="input col-md-4 form-control" name="description"
                           value="<?= htmlspecialchars(\Idno\Core\Idno::site()->config()->description) ?>">
                </div>
                <div class="col-md-6">
                    <p class="config-desc">What's your site about?</p>
                </div>
            </div>

            <!----------->

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="homepage-title"><strong>Homepage title</strong></label></p>
                </div>
                <div class="col-md-4">
                    <input type="text" id="homepage-title" placeholder="Homepage title" class="input col-md-4 form-control" name="homepagetitle"
                           value="<?= htmlspecialchars(\Idno\Core\Idno::site()->config()->homepagetitle) ?>">
                </div>
                <div class="col-md-6">
                    <p class="config-desc">What should the browser display as the title on your homepage?<br>By default this is just your site title.</p>
                </div>
            </div>

            <!-------->

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="items_per_page"><strong>Items per page</strong></label></p>
                </div>
                <div class="col-md-4">
                    <input type="text" id="items_per_page" placeholder="10" class="input col-md-4 form-control" name="items_per_page"
                           value="<?= htmlspecialchars(\Idno\Core\Idno::site()->config()->items_per_page) ?>">
                </div>
                <div class="col-md-6"><p class="config-desc">This is the number of content posts displayed on each page.</p>
                </div>
            </div>

            <!-------->

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="single_user"><strong>Single user</strong></label>
                    </p>
                </div>
                <div class="config-toggle col-md-4">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                           value="true" id="single_user"
                           name="single_user" <?php if (\Idno\Core\Idno::site()->config()->single_user) echo 'checked'; ?>>
                </div>
                <div class="col-md-6"><p class="config-desc">
                        Is this a single-user site? If so, your profile information will be shown at the top of the homepage.
                    </p>
                </div>


            </div>

            <!-------->

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="permalink_structure"><strong>Permalink Structure</strong></label></p>
                </div>
                <div class="col-md-4">
                    <?php foreach (array(
                        '/:year/:slug' => '/:year/:slug <strong>(default)</strong>',
                        '/:year/:month/:slug' => '/:year/:month/:slug',
                        '/:year/:month/:day/:slug' => '/:year/:month/:day/:slug',
                    ) as $value => $label) { ?>
                        <div class="radio">
                            <label>
                                <input type="radio" name="permalink_structure" value="<?=$value?>"
                                  <?= \Idno\Core\Idno::site()->config()->getPermalinkStructure() == $value ? 'checked' : ''?> />
                                <?=$label?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-6"><p class="config-desc">How permalinks for individual posts are constructed.</p>
                </div>
            </div>



            <?=$this->draw('admin/home/settings/details')?>

            <!----------->

            <div class="row">
                <div class="col-md-10">
                    <h3>Registration and privacy</h3>
                </div>
            </div>

            <!----------->

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="open_registration"><strong>Allow registration</strong></label></p>
                </div>
                <div class="config-toggle col-md-4">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                           name="open_registration"
                           value="true" <?php if (\Idno\Core\Idno::site()->config()->open_registration == true) echo 'checked'; ?>>
                </div>
                <div class="col-md-6">
                    <p class="config-desc">Allow registration if you want others to sign up for your site.</p>
                </div>
            </div>

            <?php

                if (\Idno\Core\Idno::site()->config()->walled_garden == true || \Idno\Core\Idno::site()->config()->canMakeSitePrivate()) {

                    ?>

                    <!---------->
                    <div class="row">
                        <div class="col-md-2">
                            <p><label class="control-label" for="walled_garden"><strong>Make site
                                        private</strong></label></p>
                        </div>
                        <div class="config-toggle col-md-4">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                                   name="walled_garden" id="walled_garden"
                                   value="true" <?php if (\Idno\Core\Idno::site()->config()->walled_garden == true) echo 'checked'; ?>>
                        </div>
                        <div class="col-md-6"><p class="config-desc">Content on a private site is only visible if you're
                                logged in.</p>
                        </div>
                    </div>
                <?php

                }

            ?>

            <?php

                if (\Idno\Core\Idno::site()->config()->show_privacy == true || \Idno\Core\Idno::site()->config()->canMakeSitePrivate()) {

                    ?>
                    <!---------->

                    <div class="row">
                        <div class="col-md-2">
                            <p><label class="control-label" for="show_privacy"><strong>Per-post privacy</strong></label>
                            </p>
                        </div>
                        <div class="config-toggle col-md-4">
                            <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                                   name="show_privacy" id="show_privacy"
                                   value="true" <?php if (\Idno\Core\Idno::site()->config()->show_privacy == true) echo 'checked'; ?>>
                        </div>
                        <div class="col-md-6"><p class="config-desc">
                                Show per-post privacy settings.
                            </p>
                        </div>
                    </div>
                    <?php

                }

                echo $this->draw('admin/home/settings/privacy');

            ?>

            <!---------->

            <div class="row">
                <div class="col-md-10">
                    <h3>Technical Settings</h3>
                </div>
            </div>

            <!---------->

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="hub"><strong>PubSubHubbub hub</strong></label></p>
                </div>
                <div class="col-md-4">
                    <input type="url" id="hub" placeholder="PubSubHubbub hub address" class="input col-md-4 form-control" name="hub"
                           value="<?= htmlspecialchars(\Idno\Core\Idno::site()->config()->hub) ?>">
                </div>
                <div class="col-md-6">
                    <p class="config-desc">You can probably leave this as is.
                        <a href="https://code.google.com/p/pubsubhubbub/" target="_blank">Learn more about PuSH</a>.

                    </p>
                </div>
            </div>

            <!----------->

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="user_avatar_favicons"><strong>Avatar as icon</strong></label>
                    </p>
                </div>
                <div class="config-toggle col-md-4">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                           value="true" id="user_avatar_favicons"
                           name="user_avatar_favicons" <?php if (\Idno\Core\Idno::site()->config()->user_avatar_favicons == true) echo 'checked'; ?>>
                </div>
                <div class="col-md-6"><p class="config-desc">
                        This uses members' avatar images as the site favicon.
                    </p>
                </div>
            </div>

            <!---------->
            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="include_permalinks"><strong>Include permalinks</strong></label></p>
                </div>
                <div class="config-toggle col-md-4">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                           name="indieweb_reference" id="include_permalinks"
                           value="true" <?php if (\Idno\Core\Idno::site()->config()->indieweb_reference == true) echo 'checked'; ?>>
                </div>
                <div class="col-md-6"><p class="config-desc">Always add a link back to your site when you
                        syndicate to external networks.</p>
                </div>
            </div>

            <!---------->

            <?=$this->draw('admin/home/settings/technical')?>

            <?=$this->draw('admin/home/settings')?>


                <div class="controls-save">
                    <button type="submit" class="btn btn-primary">Save updates</button>
                </div>

            <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/') ?>

            <?=$this->draw('admin/home/footer/settings')?>

        </form>
    </div>

</div>
<script>
    /**
     * Trigger service calls
     */
    $(document).ready(function(){

       /* Optimise DB */
       $.get(wwwroot() + 'service/db/optimise');

       /* Vendor messages */
       $.get(wwwroot() + 'service/vendor/messages', function(data, textstatus, xhr){
           addMessage(data);
       })

    });
</script>
