<form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>filepicker/" method="post"
      enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div style="text-align: center">
                <h2>
                    Upload an image
                </h2>

                <div id="photo-preview" style="display: inline-block; margin-top: 1em; margin-bottom: 1em">
                    <div style="width: 300px; height: 200px; background-color: #ccc;">&nbsp;</div>
                </div>
            </div>
			<div class="col-md-10 col-md-offset-1" style="text-align: center">
                <label>
                    <div id="photo-preview" ></div>
                                        <span class="btn btn-primary btn-file">
                                            <i class="fa fa-camera"></i> <span
                                                id="photo-filename">Select an image</span>
                                            <input type="file" name="file" id="photo"
                                                   class="col-md-9"
                                                   accept="image/*;capture=camera"
                                                   onchange="photoPreview(this)"/>
                                        </span>
                </label>

                <p>
                    <?= \Idno\Core\Idno::site()->actions()->signForm('/filepicker/'); ?>
                    <input type="submit" value="Upload this image" class="btn btn-primary" style="display:none"
                           id="upload-button">
                </p>

            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $('#photo').click();
    })

    //if (typeof photoPreview !== function) {
    function photoPreview(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#photo-preview').html('<img src="" id="photopreview" style="height: 200px">');
                $('#photo-filename').html('Choose a different image');
                $('#photopreview').attr('src', e.target.result);
                $('#photopreview').show();
                $('#upload-button').show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    //}
</script>