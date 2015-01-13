<?php

	/**
	 *	Default RSS pageshell.
	 */

	header("content-type: text/xml");
	header('pragma: public', true);
	
?>
<rss version="2.0"> 
	<channel>
		<title><![CDATA[<?=$vars['title'];?>]]></title>
		<?php if (!empty($vars['url'])) { ?><link><?php echo htmlentities($vars['url']);?></link><?php } ?>
		<?=$vars['body']?>
	</channel>
</rss>