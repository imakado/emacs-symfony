<?php use_helper('Date', 'Answer', 'Question') ?>

<h1><?php echo __('recent answers') ?></h1>

<div id="answers">
<?php foreach ($answer_pager->getResults() as $answer): ?>
  <div class="answer">
    <h2><?php echo link_to_question($answer->getQuestion()) ?></h2>
    <?php include_partial('answer/answer', array('answer' => $answer)) ?>
  </div>
<?php endforeach ?>
</div>

<div id="answers_pager">
  <?php echo pager_navigation($answer_pager, '@recent_answers') ?>
</div>
