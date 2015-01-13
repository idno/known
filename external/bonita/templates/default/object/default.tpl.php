<?php

	// The default object template is only for classes that implement BonDrawable.
	
	// Of course, you can override this template ...

	if ($vars['object'] instanceof BonDrawable) {


?>

<div class="object <?php echo get_class($vars['object']); ?>">
	<div class="title">
		<?php echo $t->process($vars['object']->getTitle()); ?>
	</div>
	<div class="description">
		<?php echo $t->process($vars['object']->getDescription()); ?>
	</div>
</div>

<?php

	}

?>