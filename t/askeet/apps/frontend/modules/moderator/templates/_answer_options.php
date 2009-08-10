<?php if ($sf_user->hasCredential('moderator')): ?>
  <?php if ($answer->getReports()): ?>
    &nbsp;[<?php echo __('%1% reports', array('%1%' => $answer->getReports())) ?>]
    &nbsp;<?php echo link_to('['.__('reset reports').']', 'moderator/resetAnswerReports?id='.$answer->getId(), 'confirm='.__('Are you sure you want to reset the report spam counter for this answer?')) ?>
  <?php endif ?>
  &nbsp;<?php echo link_to('['.__('delete answer').']', 'moderator/deleteAnswer?id='.$answer->getId(), 'confirm='.__('Are you sure you want to delete this answer?')) ?>
<?php endif ?>
