<div id="form-main">
    <div id="form-div">
        <h2 class="profile"><?php echo \Idno\Core\Idno::site()->language()->_('Create your profile'); ?></h2>

        <?php echo $this->draw('shell/simple/messages') ?>

        <form action="<?php echo $vars['user']->getDisplayURL() ?>" method="post" enctype="multipart/form-data">

            <p class="profile-pic" id="photo-preview">
                <img src="<?php echo $vars['user']->getIcon() ?>" alt="" style="width: 150px; cursor: pointer"
                     class="icon-container" onclick="$('#photo').click();"/>
            </p>

            <div class="upload">
                <span class="camera btn-file" type="button" value="Add a photo of yourself">
                    <span id="photo-filename"><?php echo \Idno\Core\Idno::site()->language()->_('Add a photo of yourself'); ?></span>
                    <input type="file" name="avatar" id="photo" class="col-md-9" accept="image/*" capture="camera"
                           onchange="photoPreview(this)"/>
                </span>
            </div>
            <p class="name">
                <label class="control-label" for="inputName"><?php echo \Idno\Core\Idno::site()->language()->_('Your name'); ?> <br/></label>
                <input name="name" type="text" class="profile-input" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('Ben Franklin'); ?>" id="name"/>
            </p>

            <p class="text">
                <label class="control-label" for="inputName"><?php echo \Idno\Core\Idno::site()->language()->_('Your short bio'); ?><br/></label>
                <textarea name="profile[description]" class="profile-input" id="description"
                          placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('I fly kites...'); ?>"></textarea>
            </p>

            <p class="website">
                <span id="websites">
                    <label class="control-label" for="inputWebsite"><?php echo \Idno\Core\Idno::site()->language()->_('Your other websites'); ?>
                        <small><?php echo \Idno\Core\Idno::site()->language()->_('(a blog, a portfolio, Twitter, Facebook, etc)'); ?></small>
                        <br/></label>
                    <input name="profile[url][]" type="url" class="profile-input" id="website"
                           placeholder="https://..."/>
                </span>
                <a href="#" onclick="$('#websites').append($('#website-template').html()); return false;"><?php echo \Idno\Core\Idno::site()->language()->_('Add another website'); ?></a>
            </p>
            <div class="col-md-12">
                <div class="submit">
                    <?php echo \Idno\Core\Idno::site()->actions()->signForm('/profile/' . $vars['user']->getHandle()) ?>
                    <input type="submit" value="Save profile" class="btn btn-primary btn-lg btn-responsive">
                    <input type="hidden" name="onboarding" value="1"/>
                </div>
        </form>
        <div id="website-template" style="display:none"><input name="profile[url][]" type="url" class="profile-input"
                                                               id="website" placeholder="https://..."/></div>

    </div>
</div>
</div>

<script>
    //if (typeof photoPreview !== function) {
    function photoPreview(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#photo-preview').html('<img src="" id="photopreview" style="display:none; width: 150px">');
                $('#photo-filename').html('<?php echo \Idno\Core\Idno::site()->language()->_('Choose different photo'); ?>');
                $('#photopreview').attr('src', e.target.result);
                $('#photopreview').show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    //}
</script>