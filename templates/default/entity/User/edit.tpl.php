<form action="<?= $vars['user']->getDisplayURL() ?>" method="post" enctype="multipart/form-data">

    <div class="row beforecontent">
        <div class="col-md-11 col-md-offset-1">
            <h1>Edit your profile</h1>

            <p>
                Your profile is how other users see you across the site. It's up to you how much or how little
                information you choose to provide.
            </p>
        </div>
    </div>

    <div class="row">

        <div class="col-md-6 col-md-offset-1">

            <p>
                <label>
                    About you<br/>
                    <textarea name="profile[description]" id="body"
                              class="col-md-6 bodyInput"><?= htmlspecialchars($vars['user']->getDescription()) ?></textarea>
                </label>
            </p>

            <label>
                <div id="photo-preview"></div>
                                    <span class="btn btn-primary btn-file">
                                        <i class="icon-camera"></i> <span
                                            id="photo-filename">Select a user picture</span>
                                        <input type="file" name="avatar" id="photo"
                                               class="col-md-9"
                                               accept="image/*;capture=camera"
                                               onchange="photoPreview(this)"/>

                                    </span>
            </label>

        </div>

        <div class="col-md-4">
            <p>
                <label>
                    Your name<br>
                    <input type="text" name="name" value="<?= htmlspecialchars($vars['user']->getTitle()) ?>"
                           class="col-md-3">
                </label>
            </p>

            <p id="websitelist">
                Your websites<br/>
                <small>Other places on the web where people can find you.</small>
                <?php

                    if (!empty($vars['user']->profile['url'])) {
                        if (!is_array($vars['user']->profile['url'])) {
                            $urls = array($vars['user']->profile['url']);
                        } else {
                            $urls = $vars['user']->profile['url'];
                        }
                        foreach ($urls as $url) {
                            if (!empty($url)) {
                                ?>
                                <span><input type="text" name="profile[url][]" value="<?= htmlspecialchars($this->fixURL($url)) ?>"
                                             placeholder="http://" class="col-md-3"/> <small><a href="#"
                                                                                             onclick="$(this).parent().parent().remove(); return false;">Remove</a>
                                    </small><br/></span>
                            <?php
                            }
                        }
                    }

                ?>
                <span><input type="text" name="profile[url][]" id="title" value="" placeholder="http://" class="col-md-3"/> <small>
                        <a href="#" onclick="$(this).parent().parent().remove(); return false;">Remove</a></small><br/></span>
            </p>
            <p>
                <small><a href="#"
                          onclick="$('#websitelist').append('<span><input type=&quot;text&quot; name=&quot;profile[url][]&quot; id=&quot;title&quot; value=&quot;&quot; placeholder=&quot;http://&quot; class=&quot;col-md-3&quot; /> <small><a href=&quot;#&quot; onclick=&quot;$(this).parent().parent().remove(); return false;&quot;>Remove</a></small><br /></span>'); return false;">+
                        Add more</a></small>
            </p>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/profile/' . $vars['user']->getHandle()) ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                <input type="submit" class="btn btn-primary" value="Save Changes"/>
            </p>
        </div>

    </div>
</form>
<script>
    //if (typeof photoPreview !== function) {
    function photoPreview(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#photo-preview').html('<img src="" id="photopreview" style="width: 200px">');
                $('#photo-filename').html('Choose different user picture');
                $('#photopreview').attr('src', e.target.result);
                $('#photopreview').show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    //}
</script>