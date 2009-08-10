<?php if ($sf_user->hasCredential('moderator')): ?>
  <?php if ($question->getReports()): ?>
    &nbsp;[<?php echo __('%1% reports', array('%1%' => $question->getReports())) ?>]
    &nbsp;<?php echo link_to('['.__('reset reports').']', 'moderator/resetQuestionReports?stripped_title='.$question->getStrippedTitle(), 'confirm='.__('Are you sure you want to reset the report spam counter for this question?')) ?>
  <?php endif ?>
  &nbsp;<?php echo link_to('['.__('delete question').']', 'moderator/deleteQuestion?stripped_title='.$question->getStrippedTitle(), 'confirm='.__('Are you sure you want to delete this question?')) ?>
<?php endif ?>
