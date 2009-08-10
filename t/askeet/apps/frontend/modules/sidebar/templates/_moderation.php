<?php if ($sf_user->hasCredential('moderator')): ?>
  <h2><?php echo __('moderation') ?></h2>

  <ul>
    <li><?php echo link_to(__('reported questions'), 'moderator/reportedQuestions') ?> (<?php echo QuestionPeer::getReportCount() ?>)</li>
    <li><?php echo link_to(__('reported answers'), 'moderator/reportedAnswers') ?> (<?php echo AnswerPeer::getReportCount() ?>)</li>
    <li><?php echo link_to(__('unpopular tags'), 'moderator/unpopularTags') ?></li>
  </ul>
<?php endif ?>
