<?php use_helper('Text', 'Global', 'Question', 'Date', 'User') ?>

<h1><?php echo __('questions reported as spam') ?></h1>

<?php foreach($question_pager->getResults() as $question): ?>
<div class="question">
  <h2><?php echo link_to_question($question) ?></h2>
  <div class="subtitle">
  <?php echo __('asked by %1% on %2%', array('%1%' => link_to_profile($question->getUser()), '%2%' => format_date($question->getCreatedAt(), 'f'))) ?>
  </div>
  <div><?php echo truncate_text(strip_tags($question->getHtmlBody()), 200) ?></div>
  <div class="options">
    <?php include_partial('moderator/question_options', array('question' => $question)) ?>
  </div>
</div>
<?php endforeach ?>

<div id="question_pager" class="right">
  <?php echo pager_navigation($question_pager, 'moderator/reportedQuestions') ?>
</div>
