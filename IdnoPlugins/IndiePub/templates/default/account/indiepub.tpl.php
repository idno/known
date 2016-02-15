<?php

use Idno\Core\Idno;

$baseURL = Idno::site()->config()->getDisplayURL();
$user = Idno::site()->session()->currentUser();
?>

<div class="col-md-offset-1 col-md-10">

    <?= $this->draw('account/menu') ?>
    <h1>IndiePub Accounts</h1>

    <?php foreach ((array) $user->indieauth_tokens as $token => $details) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                Client ID: <a href="<?= $details['client_id'] ?>" target="_blank"><?= $details['client_id'] ?></a>
            </div>
            <div class="panel-body" >
                <p>
                    Authorized <strong><?= strftime('%Y-%m-%d', $details['issued_at']) ?></strong>
                    with the scope <strong><?= $details['scope'] ?></strong>.
                </p>
                <p>
                  Token: <?= substr($token, 0, 5) ?>&hellip;
                </p>
                <form action="<?= Idno::site()->config()->getDisplayURL() ?>account/indiepub/revoke" method="POST">
                    <input name="token" type="hidden" value="<?= $token ?>">
                    <button class="btn btn-warning" type="submit">Revoke Access</button>
                    <?= Idno::site()->actions()->signForm('account/indiepub/revoke') ?>
                </form>
            </div>
        </div>
    <?php } ?>

</div>
