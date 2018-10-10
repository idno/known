<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?php echo $this->draw('admin/menu') ?>
                <h1>Cherwell Theme Options</h1>
        <div class="explanation">
            <p>
                <?= \Idno\Core\Idno::site()->language()->_('Update your background image and other display settings.'); ?>
            </p>
        </div>
    </div>

</div>

<form id="bgform" action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/cherwell/" method="post"
      enctype="multipart/form-data">

    <div class="row">

        <div class="col-md-6 col-md-offset-1">
            <p>
                <?= \Idno\Core\Idno::site()->language()->_('Change your background image:'); ?>
            </p>
        </div>
        <div class="col-md-6 col-md-offset-1">

            <p>
                <img src="<?php echo \Themes\Cherwell\Controller::getBackgroundImageURL() ?>"
                     style="width: 50%; float: left; margin-right: 10px; margin-bottom: 10px" id="photopreview">
            </p>
        </div>
        <div class="col-md-6 col-md-offset-1">

            <p class="upload">
                <label>
                    <span class="btn btn-primary btn-file">
                    <i class="fa fa-camera"></i>
                    <span id="photo-filename">Upload a new image</span>
                    <input type="file" name="background" id="photo" class="col-md-9" accept="image/*" capture="camera"
                           onchange="photoPreview(this)"/>
                    </span>
                </label>
            </p>

            <p>
                <?php echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/cherwell/') ?>
                <input type="submit" class="btn btn-primary" value="Save">
                <input type="hidden" name="action" value="" id="action">
                <?php

                if (!empty(\Idno\Core\Idno::site()->config->cherwell['bg_id'])) {

                    ?>
                        <input type="button" class="btn" value="Restore default image"
                               onclick="$('#action').val('clear'); $('#bgform').submit();">
                    <?php

                }

                ?>
            </p>


        </div>
        <div class="col-md-6 col-md-offset-1" style="margin-top: 1em">
            <?php

            if (!empty($vars['users'])) {

                ?>
                    <p>
                        <?= \Idno\Core\Idno::site()->language()->_('Choose whose profile is displayed on the homepage:'); ?>
                    </p>
                    <select name="profile_user">
                    <?php

                    foreach ($vars['users'] as $user) {
                        ?>
                                <option value="<?php echo $user->handle ?>" <?php

                                if (!empty(\Idno\Core\Idno::site()->config->cherwell['profile_user'])) {
                                    if ($user->handle == \Idno\Core\Idno::site()->config->cherwell['profile_user']) {
                                        echo 'selected';
                                    }
                                }

                                ?>><?php echo $user->getTitle() ?> (<?php echo $user->handle ?>)
                                </option>
                            <?php
                    }

                    ?>
                    </select><br>
                    <input type="submit" class="btn btn-primary" value="Save">
                <?php

            }

            ?>
        </div>

    </div>
</form>

<script>
    //if (typeof photoPreview !== function) {
    function photoPreview(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#photo-filename').html('<?= \Idno\Core\Idno::site()->language()->_('Choose a different photo'); ?>');
                $('#photopreview').attr('src', e.target.result);
                $('#photopreview').show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    //}
</script>