<div class="row">

    <div class="span10 offset1">
        <h1>Cherwell Theme Options</h1>
        <?= $this->draw('admin/menu') ?>
        <div class="explanation">
            <p>
                Update your background image and other display settings.
            </p>
        </div>
    </div>

</div>

<form id="bgform" action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>admin/cherwell/" method="post"
      enctype="multipart/form-data">

    <div class="row">

        <div class="span6 offset3">
            <p>
                Change your background image:
            </p>
        </div>
        <div class="span6 offset3">

            <p>
                <img src="<?= \Themes\Cherwell\Controller::getBackgroundImageURL() ?>"
                     style="width: 50%; float: left; margin-right: 10px; margin-bottom: 10px" id="photopreview">
            </p>

            <p class="upload">
                <label>
                    <span class="btn btn-file">
                    <span id="photo-filename">Upload a new background image</span>
                    <input type="file" name="background" id="photo" class="span9" accept="image/*;capture=camera"
                           onchange="photoPreview(this)"/>
                    </span>
                </label>
            </p>

            <p>
                <?= \Idno\Core\site()->actions()->signForm(\Idno\Core\site()->config()->getDisplayURL() . 'admin/cherwell/') ?>
                <input type="submit" class="btn btn-primary" value="Save">
                <input type="hidden" name="action" value="" id="action">
                <?php

                    if (!empty(\Idno\Core\site()->config->cherwell['bg_id'])) {

                        ?>
                        <input type="button" class="btn" value="Restore default image"
                               onclick="$('#action').val('clear'); $('#bgform').submit();">
                    <?php

                    }

                ?>
            </p>


        </div>
        <div class="span6 offset3" style="margin-top: 1em">
            <?php

                if (!empty($vars['users'])) {

                    ?>
                    <p>
                        Choose whose profile is displayed on the homepage:
                    </p>
                    <select name="profile_user">
                        <?php

                            foreach ($vars['users'] as $user) {
                                ?>
                                <option value="<?= $user->handle ?>" <?php

                                    if (!empty(\Idno\Core\site()->config->cherwell['profile_user'])) {
                                        if ($user->handle == \Idno\Core\site()->config->cherwell['profile_user']) {
                                            echo 'selected';
                                        }
                                    }

                                ?>><?= $user->getTitle() ?> (<?= $user->handle ?>)
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
                $('#photo-filename').html('Choose a different photo');
                $('#photopreview').attr('src', e.target.result);
                $('#photopreview').show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    //}
</script>