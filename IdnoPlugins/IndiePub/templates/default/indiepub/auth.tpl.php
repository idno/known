<div class="row">
  <div class="span6 offset3 well text-center">

    <h2 class="text-center">
      <?= empty($vars['scope']) ? 'Authenticate' : 'Authorize'; ?>
    </h2>

    <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>indieauth/approve" method="post">

      <p>
        <?php
        echo \Idno\Core\Idno::site()->language()->_('You are logged in as %s.', [\Idno\Core\site()->session()->currentUser()->getHandle()]);
        if (empty($vars['scope'])) {
          echo \Idno\Core\Idno::site()->language()->_('Authenticate to %s?', [$vars['client_id']]);
        } else {
          echo \Idno\Core\Idno::site()->language()->_('Authorize %s to access this site with the scope(s) %s?', [$vars['client_id'], $vars['scope']]);
        }
        ?>
      </p>

      <div class="control-group">
        <div class="controls">
          <button type="submit" class="btn btn-primary">
            <?= empty($vars['scope']) ? \Idno\Core\Idno::site()->language()->_('Authenticate') : \Idno\Core\Idno::site()->language()->_('Authorize'); ?>
          </button>
          <a class="btn btn-cancel" href="<?=$vars['redirect_uri']?>"><?= \Idno\Core\Idno::site()->language()->_('Cancel'); ?></a>
        </div>
      </div>

      <?php
      foreach (array("me", "client_id", "redirect_uri", "scope", "state") as $param) {
        echo '<input type="hidden" name="'.$param.'" value="'.$vars[$param].'" />';
      }
      ?>

      <?= \Idno\Core\site()->actions()->signForm('/indiepub/auth') ?>
    </form>

  </div>
</div>
