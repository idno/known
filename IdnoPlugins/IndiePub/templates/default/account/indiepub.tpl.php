<?php

use Idno\Core\Idno;

$baseURL = Idno::site()->config()->getDisplayURL();
$user = Idno::site()->session()->currentUser();
?>
<div class="row">
    <div class="col-md-offset-1 col-md-10">

        <?php echo $this->draw('account/menu') ?>
        <h1><?php echo \Idno\Core\Idno::site()->language()->_('IndiePub Accounts'); ?></h1>


        <?php
        if (empty($user->indieauth_tokens)) {
            ?>
            <div class="explanation">
                <p>
                    <?php echo \Idno\Core\Idno::site()->language()->_('There are currently no IndiePub accounts associated with this site.'); ?>
                </p>
            </div>
            <?php
        } else {
            foreach ((array) $user->indieauth_tokens as $token => $details) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo \Idno\Core\Idno::site()->language()->_('Client ID'); ?>: <a href="<?php echo $details['client_id'] ?>" target="_blank"><?php echo $details['client_id'] ?></a>
                </div>
                <div class="panel-body" >
                    <p>
                        <?php echo \Idno\Core\Idno::site()->language()->_('Authorized'); ?> <strong><?php echo strftime('%Y-%m-%d', $details['issued_at']) ?></strong>
                        <?php echo \Idno\Core\Idno::site()->language()->_('with the scope'); ?> <strong><?php echo $details['scope'] ?></strong>.
                    </p>
                    <p>
                      <?php echo \Idno\Core\Idno::site()->language()->_('Token'); ?>: <?php echo substr($token, 0, 5) ?>&hellip;
                    </p>
                    <form action="<?php echo Idno::site()->config()->getDisplayURL() ?>account/indiepub/revoke" method="POST">
                        <input name="token" type="hidden" value="<?php echo $token ?>">
                        <button class="btn btn-warning" type="submit"><?php echo \Idno\Core\Idno::site()->language()->_('Revoke Access'); ?></button>
                        <?php echo Idno::site()->actions()->signForm('account/indiepub/revoke') ?>
                    </form>
                </div>
            </div>
                <?php
            }
        }?>

</div>
</div>