<div class="row">
    <div class="span10 offset1">
        <h1>API Tester</h1>
        <?= $this->draw('admin/menu') ?>
    </div>
</div>
<div class="row">
    <div class="span10 offset1">
        <div class="explanation">
            This API tester helps you use Known's built-in API functionality. Every page in Known is also an API
            endpoint, which means you can access it using third-party applications as well as through as web browser.
        </div>
    </div>
</div>
<?php

    if (!empty($vars['sent_request'])) {

        ?>
        <div class="row" id="apiResponse">
            <div class="span10 offset1">
                <div class="well">
                    <p>
                        Your last API request:
                    </p>
                    <textarea style="width: 100%; height: 5em"><?= htmlspecialchars($vars['sent_request']) ?></textarea>

                    <p>
                        The API response:
                    </p>
                    <textarea style="width: 100%; height: 5em"><?= htmlspecialchars($vars['response']) ?></textarea>

                    <p style="font-size: small">
                        <a href="#" onclick="$('#apiResponse').slideUp(); return false;">Hide this</a>
                    </p>
                </div>
            </div>
        </div>
    <?php

    }

?>
<div class="row">
    <div class="span10 offset1">

        <form action="<?= \Idno\Core\site()->config()->url ?>admin/apitester/" method="post">

            <div class="row">
                <div class="span8 offset2">
                    <p>
                        Some examples:
                        <a href="?request=/status/edit&json=<?=urlencode(json_encode(['body' => 'Status body']))?>">post a status</a>,
                        <a href="?request=/">get feed</a>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="span2">
                    <p>Request</p>
                </div>
                <div class="span5">
                    <p>
                        <input type="text" class="span5" name="request" id="apirequest"
                               value="<?= htmlspecialchars($vars['request']) ?>"/>
                    </p>
                </div>
                <div class="span3">
                    <p style="text-align: right">
                        <a href="#" class="btn" onclick="return setResponseType('json')">JSON</a>
                        <a href="#" class="btn" onclick="return setResponseType('rss')">RSS</a>
                        <a href="#" class="btn" onclick="return setResponseType('default')">HTML</a>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="span2">
                    <p>Username</p>
                </div>
                <div class="span8">
                    <p>
                        <input type="text" class="span8" name="username"
                               value="<?= htmlspecialchars($vars['username']) ?>"/>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="span2">
                    <p>API key</p>
                </div>
                <div class="span8">
                    <p>
                        <input type="text" class="span8" name="key" value="<?= htmlspecialchars($vars['key']) ?>"/>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="span2">
                    <p>JSON payload</p>
                </div>
                <div class="span8">
                    <textarea class="span8" name="json"><?= htmlspecialchars($vars['json']) ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="span2">
                    <p>&nbsp;</p>
                </div>
                <div class="span8">
                    <label class="checkbox">
                        <input type="checkbox" name="follow_redirects" value="1" checked>
                        Follow redirects
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="span8 offset2">
                    <?= \Idno\Core\site()->actions()->signForm('admin/apitester') ?>
                    <input type="submit" class="btn btn-primary" value="Make API call"/>
                </div>
            </div>
            <div class="row" style="margin-top: 2em">
                <div class="span8 offset2">
                    <p>
                        Technical details: API calls are a POST request  with the HTTP header X-KNOWN-USERNAME
                        set to the user's username, and X-KNOWN-SIGANTURE to be an HMAC signature, computed with
                        sha256, using the user's API key.
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