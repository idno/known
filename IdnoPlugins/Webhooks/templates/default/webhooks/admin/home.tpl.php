<div class="row">

    <div class="col-md-10 col-md-offset-1">
	            <?=$this->draw('admin/menu');?>

        <h1>Webhooks</h1>

        <p class="explanation">
            Webhooks let you syndicate content to external applications very simply. The content of your
            post is sent to an external URL. Services like Slack, Wufoo, and Mailchimp all use Webhooks.
        </p>
        <p class="explanation">
	        To learn more about Webhooks and what they can be used for, read the <a href="https://webhooks.pbworks.com/w/page/13385124/FrontPage">PBWiki Webhooks page</a>.</p>
        <p>
            When content is syndicated via Webhooks, the external URL is sent the following data:
        </p>
        <ul>
            <li><strong><em>text</em></strong>: the text of the update</li>
            <li><strong><em>username</em></strong>: the username of the account-holder</li>
            <li><strong><em>icon_url</em></strong>: the URL of the user's icon</li>
            <li><strong><em>content_type</em></strong>: the type of content being sent</li>
        </ul>

    </div>

</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

        <h3>Create a new Webhook</h3>

        <form action="" method="post">

            <?php

                if (!empty(\Idno\Core\Idno::site()->config()->webhook_syndication)) {
                    foreach(\Idno\Core\Idno::site()->config()->webhook_syndication as $webhook) {

?>
				<div class="row">
					<div class="col-md-4">
                        <input type="text" name="titles[]" value="<?=htmlspecialchars($webhook['title'])?>" placeholder="Name of this webhook" class="form-control">
					</div>
					<div class="col-md-5">
                        <input type="text" name="webhooks[]" value="<?=htmlspecialchars($webhook['url'])?>" placeholder="Webhook URL" class="form-control">
					</div>
					<div class="col-md-3" style="margin-top: 0.75em">
                        <small><a href="#" onclick="$(this).closest('.row').remove(); return false;"><i class="fa fa-times"></i> Remove this Webhook</a></small>
					</div>
				</div>
<?php

                    }
                }

            ?>
            <div class="row">
	            <div class="col-md-4">
	                <input type="text" value="" name="titles[]" placeholder="Name of this webhook" class="form-control">
	            </div>
				<div class="col-md-5">
	                <input type="text" value="" name="webhooks[]" placeholder="Webhook URL" class="form-control">
				</div>
				<div class="col-md-3" style="margin-top: 0.75em">
	                <small><a href="#" onclick="$(this).closest('.row').remove(); return false;"><i class="fa fa-times"></i> Remove this Webhook</a></small>
	        	</div>
            </div>
            <div id="morefields"></div>
            
            	<p style="margin-top:1em; margin-bottom:1.5em">
	            	<a href="#" onclick="$('#morefields').append($('#field_template').html());"><i class="fa fa-plus"></i> Add another Webhook</a>
	            </p>
            <p>
                <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/webhooks/') ?>
                <input class="btn btn-primary" value="Save Webhooks" type="submit">
            </p>

        </form>
        <div id="field_template" style="display:none">
            <p>
                <input type="text" value="" name="titles[]" placeholder="Name of this webhook" class="span3">
                <input type="text" value="" name="webhooks[]" placeholder="Webhook URL" class="span5">
                <small><a href="#" onclick="$(this).closest('p').remove(); return false;">- Remove</a></small>
            </p>
        </div>

    </div>
</div>