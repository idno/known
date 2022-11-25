<div class="row">
    <div class="col-md-10 col-md-offset-1">
                <?php echo $this->draw('admin/menu') ?>
        <h1><?php echo \Idno\Core\Idno::site()->language()->_('API Tester'); ?></h1>

    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="explanation">
            <?php echo \Idno\Core\Idno::site()->language()->_("This API tester helps you use Known's built-in API functionality. Every page in Known is also an API endpoint, which means you can access it using third-party applications as well as through as web browser."); ?>
        </div>
    </div>
</div>
<?php

if (!empty($vars['sent_request'])) {

    ?>
        <div class="row" id="apiResponse">
            <div class="col-md-10 col-md-offset-1">
                <div class="well">
                    <p>
                    <?php echo \Idno\Core\Idno::site()->language()->_('Your last API request'); ?>:
                    </p>
                    <textarea style="width: 100%; height: 5em"><?php echo htmlspecialchars($vars['sent_request']) ?></textarea>

                    <p>
                    <?php echo \Idno\Core\Idno::site()->language()->_('The API response'); ?>:
                    </p>
                    <textarea style="width: 100%; height: 5em"><?php echo htmlspecialchars($vars['response']) ?></textarea>

                    <p style="font-size: small">
                        <a href="#" onclick="$('#apiResponse').slideUp(); return false;"><?php echo \Idno\Core\Idno::site()->language()->_('Hide this'); ?></a>
                    </p>
                </div>
            </div>
        </div>
    <?php

}

?>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

        <form action="<?php echo \Idno\Core\Idno::site()->config()->url ?>admin/apitester/" method="post">

            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <p>
                        <?php echo \Idno\Core\Idno::site()->language()->_('Some examples'); ?>:
                        <a href="?request=/status/edit&json=<?php echo urlencode(json_encode(array('body' => 'Status body')))?>&method=post"><?php echo \Idno\Core\Idno::site()->language()->_('post a status'); ?></a>,
                        <a href="?request=/"><?php echo \Idno\Core\Idno::site()->language()->_('get feed'); ?></a>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label-api"><?php echo \Idno\Core\Idno::site()->language()->_('Request'); ?></label>
                </div>
                <div class="col-md-5">
                    <p>
                        <input type="text" class="form-control" name="request" id="apirequest"
                               value="<?php echo htmlspecialchars($vars['request']) ?>"/>
                    </p>
                </div>
                <div class="col-md-3">
                    <p style="text-align: right">
                        <a href="#" class="btn" onclick="return setResponseType('json')"><?php echo \Idno\Core\Idno::site()->language()->_('JSON'); ?></a>
                        <a href="#" class="btn" onclick="return setResponseType('rss')"><?php echo \Idno\Core\Idno::site()->language()->_('RSS'); ?></a>
                        <a href="#" class="btn" onclick="return setResponseType('default')"><?php echo \Idno\Core\Idno::site()->language()->_('Default'); ?></a>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label-api"><?php echo \Idno\Core\Idno::site()->language()->_('Method'); ?></label>
                </div>
                <div class="col-md-5">
                    <p>
                        <select name="method" class="btn">
                            <option value="GET">GET</option>
                            <option value="POST" <?php

                            if (strtolower($vars['method']) == 'post') {
                                echo 'selected="selected"';
                            }

                            ?>>POST</option>
                        </select>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label-api"><?php echo \Idno\Core\Idno::site()->language()->_('Username'); ?></label>
                </div>
                <div class="col-md-8">
                    <p>
                        <input type="text" class="form-control" name="username"
                               value="<?php echo htmlspecialchars($vars['username']) ?>"/>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label-api"><?php echo \Idno\Core\Idno::site()->language()->_('API key'); ?></label>
                </div>
                <div class="col-md-8">
                    <p>
                        <input type="text" class="form-control" name="key" value="<?php echo htmlspecialchars($vars['key']) ?>"/>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label-api"><?php echo \Idno\Core\Idno::site()->language()->_('JSON payload'); ?></label>
                </div>
                <div class="col-md-8">
                    <textarea class="form-control" name="json"><?php echo htmlspecialchars($vars['json']) ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <p>&nbsp;</p>
                </div>
                <div class="checkbox col-md-8">
                    <label class="checkbox">
                        <input type="checkbox" name="follow_redirects" value="1" checked>
                        <?php echo \Idno\Core\Idno::site()->language()->_('Follow redirects'); ?>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <?php echo \Idno\Core\Idno::site()->actions()->signForm('admin/apitester') ?>
                    <input type="submit" class="btn btn-primary" value="<?php echo \Idno\Core\Idno::site()->language()->_('Make API call'); ?>"/>
                </div>
            </div>
            <div class="row" style="margin-top: 2em">
                <div class="col-md-8 col-md-offset-2">
                    <p>
                        <strong><?php echo \Idno\Core\Idno::site()->language()->_('Technical details'); ?>:</strong> 
                        <?php echo \Idno\Core\Idno::site()->language()->_("API calls are a GET or POST request (for retrieval and publishing / deleting calls respectively) with the HTTP header X-KNOWN-USERNAME set to the user's username, and X-KNOWN-SIGNATURE to be an HMAC signature, computed with sha256, using the user's API key."); ?>
                    </p>
                </div>
            </div>

        </form>

    </div>
</div>
<script>

    function setResponseType(responseType) {

        var responseText = $('#apirequest').val();

        // First, strip all sign of the templates
        responseText = responseText.replace('_t=rss', '');
        responseText = responseText.replace('_t=json', '');
        responseText = responseText.replace('?&', '?');
        responseText = responseText.replace('&&', '&');
        var lastChar = responseText.substr(responseText.length - 1, 1);

        // Remove any trailing URL variable separators
        if (lastChar == '?' || lastChar == '&') {
            responseText = responseText.substr(0, responseText.length - 1);
        }

        // If we've been asked to set the template to JSON or RSS, add that field back
        if (responseType != 'default') {
            if (responseText.indexOf('?') != -1) {
                responseText += '&';
            } else {
                responseText += '?';
            }
            responseText += '_t=' + responseType.toString();
        }

        // Set the modified value back to the input field
        $('#apirequest').val(responseText);
        return false;

    }

</script>