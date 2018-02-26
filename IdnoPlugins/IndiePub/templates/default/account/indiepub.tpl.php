<?php

use Idno\Core\Idno;

$baseURL = Idno::site()->config()->getDisplayURL();
$user = Idno::site()->session()->currentUser();
?>
<div class="row">
    <div class="col-md-offset-1 col-md-10">

        <?= $this->draw('account/menu') ?>
        <h1><?= \Idno\Core\Idno::site()->language()->_('IndiePub Accounts'); ?></h1>


        <?php
        if (empty($user->indieauth_tokens)) {
            ?>
            <div class="explanation">
                <p>
                    <?= \Idno\Core\Idno::site()->language()->_('There are currently no IndiePub accounts associated with this site.'); ?>
                </p>
            </div>
        <?php
        } else {
            foreach ((array) $user->indieauth_tokens as $token => $details) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= \Idno\Core\Idno::site()->language()->_('Client ID'); ?>: <a href="<?= $details['client_id'] ?>" target="_blank"><?= $details['client_id'] ?></a>
                </div>
                <div class="panel-body" >
                    <p>
                        <?= \Idno\Core\Idno::site()->language()->_('Authorized'); ?> <strong><?= strftime('%Y-%m-%d', $details['issued_at']) ?></strong>
                        <?= \Idno\Core\Idno::site()->language()->_('with the scope'); ?> <strong><?= $details['scope'] ?></strong>.
                    </p>
                    <p>
                      <?= \Idno\Core\Idno::site()->language()->_('Token'); ?>: <?= substr($token, 0, 5) ?>&hellip;
                    </p>
                    <form action="<?= Idno::site()->config()->getDisplayURL() ?>account/indiepub/revoke" method="POST">
                        <input name="token" type="hidden" value="<?= $token ?>">
                        <button class="btn btn-warning" type="submit"><?= \Idno\Core\Idno::site()->language()->_('Revoke Access'); ?></button>
                        <?= Idno::site()->actions()->signForm('account/indiepub/revoke') ?>
                    </form>
                </div>
            </div>
        <?php
            }
        }?>

</div>
</div>