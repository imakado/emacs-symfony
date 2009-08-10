<?php use_helper('Date', 'User', 'Global', 'Question') ?>

<h1><?php echo __('answers reported as spam') ?></h1>

<div id="answers">
  <?php foreach ($answer_pager->getResults() as $answer): ?>
    <div class="answer">
      <h2><?php echo link_to_question($answer->getQuestion()) ?></h2>
      <?php echo $answer->getHtmlBody() ?>
      <div class="subtitle" style="margin-top: -8px">
      <?php echo __('answered by %1% on %2%', array('%1%' => link_to_profile($answer->getUser()), '%2%' => format_date($answer->getCreatedAt(), 'f'))) ?>
      </div>
      <div class="options">
        <?php include_partial('moderator/answer_options', array('answer' => $answer)) ?>
      </div>
    </div>
  <?php endforeach ?>
</div>

<div id="question_pager" class="right">
  <?php echo pager_navigation($answer_pager, 'moderator/reportedAnswers') ?>
</div>
