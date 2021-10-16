<a href="<?php echo strip_tags($annotation['owner_url']) ?>" rel="nofollow" class="icon-container"><img
        src="<?php echo \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL(strip_tags($annotation['owner_image'])) ?>"
        alt="<?php echo htmlentities($annotation['owner_name']) ?>"/></a>