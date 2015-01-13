<?php

	header("content-type: text/xml");
	header('pragma: public', true);
	
?>
<rss version="2.0"> 
	<channel>
		<title><![CDATA[<?=$vars['title'];?>]]></title>
		<link><?php echo htmlentities($vars['url']);?></link>
		<item>
			<title><![CDATA[<?=$vars['title'];?>]]></title>
			<link><?php echo htmlentities($vars['url']);?>#</link>			
			<description><![CDATA[<?=$vars['body'];?>]]></description>
		</item>
	</channel>
</rss>