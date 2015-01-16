<?php

	// The default object template is only for classes that implement BonDrawable.
	
	// Of course, you can override this template ...

	if ($vars['object'] instanceof BonDrawable) {

?>

		<item>
			<title><![CDATA[<?=$vars['title'];?>]]></title>
			<link><?php echo htmlentities($vars['object']->getURI());?></link>
			<description><![CDATA[<?=$t->process($vars['body']);?>]]></description>
		</item>

<?php

	}

?>