<?php if ($sf_user->hasCredential('administrator')): ?>
<div class="options">
  <?php if ($subscriber->getDeletions()): ?>
    [<?php echo __('%1% contributions removed', array('%1%' => $subscriber->getDeletions())) ?>]
  <?php endif ?>

  &nbsp;
  <?php if ($subscriber->getIsModerator()): ?>
    <?php echo link_to('['.__('moderator').' -]', 'administrator/removeModerator?nickname='.$subscriber->getNickname()) ?>
  <?php else: ?>
    <?php echo link_to('['.__('moderator').' +]', 'administrator/promoteModerator?nickname='.$subscriber->getNickname()) ?>
  <?php endif ?>

  &nbsp;
  <?php if ($subscriber->getIsAdministrator()): ?>
    <?php echo link_to('['.__('administrator').' -]', 'administrator/removeAdministrator?nickname='.$subscriber->getNickname()) ?>
  <?php else: ?>
    <?php echo link_to('['.__('administrator').' +]', 'administrator/promoteAdministrator?nickname='.$subscriber->getNickname()) ?>
  <?php endif ?>

  &nbsp;<?php echo link_to('['.__('delete user').']', 'administrator/deleteUser?nickname='.$subscriber->getNickname(), 'confirm='.__('Are you sure you want to delete this user and all his contributions?')) ?>
</div>

<br />

<?php endif ?>
