<?php

$user = $vars['user'];

if ($user instanceof \Idno\Entities\User) {
    $handle = $user->getHandle();
    if (!empty($handle)) {
	if (strlen($handle) > 18) {
	    $display_handle = substr($handle, 0, 16) . '...';
	} else {
	    $display_handle = $handle;
	}
	/* @var \Idno\Entities\User $user */
	?>

	<div class="row <?= strtolower(str_replace('\\', '-', get_class($user))); ?>">
	    <div class="col-sm-4 col-xs-12">
		<p class="user-tbl">
		    <img src="<?= $user->getIcon() ?>">
		    <a href="<?= $user->getDisplayURL() ?>"><?= htmlentities($user->getTitle()) ?></a>
		    (<a href="<?= $user->getDisplayURL() ?>"><?= $display_handle ?></a>)<br>
		    <small><?= $user->email ?></small>
		</p>
	    </div>
	    <div class="col-sm-2 col-xs-6">
		<p class="user-tbl">
		    <small><strong>Joined</strong><br>
			<time datetime="<?= date('r', $user->created) ?>"
			      class="dt-published"><?= date('r', $user->created) ?></time>
		    </small>
		</p>
	    </div>
	    <div class="col-sm-2 col-xs-6">
		<p class="user-tbl">
		    <small><strong>Last update posted</strong>
			<br>
			<?php
			$feed = \Idno\Common\Entity::getFromX(null, ['owner' => $user->getUUID()], array(), 1, 0);
			if (!empty($feed) && is_array($feed)) {
			    ?>
	    		<time datetime="<?= date('r', $feed[0]->updated) ?>"
	    		      class="dt-published"><?= date('r', $feed[0]->updated) ?></time>
			      <?php } else {
				  ?>
	    		Never
			    <?php }
			?>
		    </small>
		</p>
	    </div>
	    <div class="col-sm-2 col-xs-6">
		<p class="user-tbl">
		    <small>
			<?php
			if ($user instanceof \Idno\Entities\RemoteUser) {
			    ?>
	    		Remote member
			    <?php
			} else {
			    if ($user->isAdmin()) {
				?>
				<strong>Administrator</strong><br>
				<?php
				if ($user->getUUID() != \Idno\Core\Idno::site()->session()->currentUserUUID()) {
				    echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', 'Remove rights', array('user' => $user->getUUID(), 'action' => 'remove_rights'), array('class' => ''));
				} else {
				    echo 'Yes';
				}
			    } else {
				?>
				Standard member<br>
				<?= \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', 'Make admin', array('user' => $user->getUUID(), 'action' => 'add_rights'), array('class' => '')); ?>
				<?php
			    }
			}
			?>
		    </small>
		</p>
	    </div>
	    <div class="col-sm-2 col-xs-6">
		<p class="user-tbl">
		    <small>
			<?php
			if ($user->getUUID() != \Idno\Core\Idno::site()->session()->currentUserUUID()) {
			    if (\Idno\Core\Idno::site()->config()->emailIsBlocked($user->email)) {
				echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-check-circle-o"></i> Clear', array('blocked_emails' => $user->email, 'action' => 'unblock_emails'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? The user will be able to log in and post again.')) . '<br>';
			    } else {
				echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-ban"></i> Block', array('blocked_emails' => $user->email, 'action' => 'block_emails'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? The user will be logged out and will no longer be able to log in or post.')) . '<br>';
			    }
			    echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-times"></i> Delete', array('user' => $user->getUUID(), 'action' => 'delete'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? This will delete this user and all their content.'));
			} else {
			    echo '&nbsp';
			}
			?>
		    </small>
		</p>
	    </div>
	</div>

	<?php
    }
}