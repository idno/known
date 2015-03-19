<!--<form action="<?= $vars['user']->getDisplayURL() ?>" method="post" enctype="multipart/form-data">

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
                <label for="body">
                    About you</label>
                    <textarea name="profile[description]" id="body"
                              class="form-control bodyInput"><?= htmlspecialchars($vars['user']->getDescription()) ?></textarea>
            </p>

            <label>
                <div id="photo-preview"></div>
                                    <span class="btn btn-primary btn-file">
                                        <i class="fa fa-camera"></i> <span
                                            id="photo-filename">Select a user picture</span>
                                        <input type="file" name="avatar" id="photo"
                                               class="form-control"
                                               accept="image/*;capture=camera"
                                               onchange="photoPreview(this)"/>

                                    </span>
            </label>

        </div>

        <div class="col-md-4">
            <p>
                <label for="name">
                    Your name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($vars['user']->getTitle()) ?>"
                           class="form-control">
            </p>

            <p id="websitelist">
	            <label for="website">
                Your websites</label><br>
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
                                <span><input type="text" name="profile[url][]" id="website" value="<?= htmlspecialchars($this->fixURL($url)) ?>"
                                             placeholder="http://" class="form-control"/> <small><a href="#"
                                                                                             onclick="$(this).parent().parent().remove(); return false;">Remove</a>
                                    </small><br/></span>
                            <?php
                            }
                        }
                    }

                ?>
                <span><input type="text" name="profile[url][]" id="title" value="" placeholder="http://" class="form-control"/> <small>
                        <a href="#" onclick="$(this).parent().parent().remove(); return false;">Remove</a></small><br/></span>
            </p>
            <p>
                <small><a href="#"
                          onclick="$('#websitelist').append('<span><input type=&quot;text&quot; name=&quot;profile[url][]&quot; id=&quot;title&quot; value=&quot;&quot; placeholder=&quot;http://&quot; class=&quot;form-control&quot; /> <small><a href=&quot;#&quot; onclick=&quot;$(this).parent().parent().remove(); return false;&quot;>Remove</a></small><br /></span>'); return false;">+
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
</script>-->

<div class="container col-md-11 col-md-offset-1">
	<div class="row beforecontent">
    <h1>Edit your profile</h1>
                <p>
                Your profile is how other users see you across the site. It's up to you how much or how little
                information you choose to provide.
            </p>
	</div>

	<div class="row">
      <!-- left column -->
      <div class="col-md-3">
        <div class="text-center">

          <img src="//placehold.it/100" class="avatar img-circle" alt="avatar">
          		<div id="photo-preview"></div>

                        <span class="btn btn-primary btn-file">
                            <i class="fa fa-camera"></i> 
                        <span id="photo-filename">Select a user picture</span>
                            <input type="file" name="avatar" id="photo"
                                               class="form-control"
                                               accept="image/*;capture=camera"
                                               onchange="photoPreview(this)"/>

                        </span>
        </div>
      </div>
      
      <!-- edit form column -->
      <div class="col-md-8 personal-info">
        <form class="form-horizontal" role="form" action="<?= $vars['user']->getDisplayURL() ?>" method="post" enctype="multipart/form-data">
	        
	        <div class="form-group">
                <label class="control-label" for="body">About you</label><br>

                    <textarea name="profile[description]" id="body"
                              class="form-control bodyInput"><?= htmlspecialchars($vars['user']->getDescription()) ?></textarea>


			</div>
	        
          <div class="form-group">
            <label class="control-label" for="name">Your name</label>
              <input class="form-control" type="text" id="name" name="name" value="<?= htmlspecialchars($vars['user']->getTitle()) ?>">
          </div>
          
          <div class="form-group">           
               <p id="websitelist">
	            <label for="website">
                Your websites</label><br>
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
                                <div class="row">
                                <div class="col-md-10"><input type="text" name="profile[url][]" id="website" value="<?= htmlspecialchars($this->fixURL($url)) ?>"
                                             placeholder="http://" class="form-control"/></div> 
                                        <div class="col-md-2">    <small><a href="#"
                                                                                             onclick="$(this).parent().parent().remove(); return false;">Remove</a>
                                    </small></div></div>
                            <?php
                            }
                        }
                    }

                ?>
                <div class="row">
	                <div class="col-md-10">
		                <input type="text" name="profile[url][]" id="title" value="" placeholder="http://" class="form-control"/></div> 
		            <div class="col-md-2">    
		                <small>
                        <a href="#" onclick="$(this).parent().parent().remove(); return false;">Remove</a></small></div></div>
            </p>
            <p>
                <small><a href="#"
                          onclick="$('#websitelist').append('<span><input type=&quot;text&quot; name=&quot;profile[url][]&quot; id=&quot;title&quot; value=&quot;&quot; placeholder=&quot;http://&quot; class=&quot;form-control&quot; /> <small><a href=&quot;#&quot; onclick=&quot;$(this).parent().parent().remove(); return false;&quot;>Remove</a></small><br /></span>'); return false;">+
                        Add more</a></small>
            </p>
        </div>

          <div class="form-group">
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/profile/' . $vars['user']->getHandle()) ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                <input type="submit" class="btn btn-primary" value="Save Changes"/>
            </p>
    
            
            
          </div>
        </form>
      </div>
  </div>
</div>


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