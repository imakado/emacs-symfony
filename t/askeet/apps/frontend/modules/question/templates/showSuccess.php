<?php use_helper('Date', 'Answer', 'Question') ?>

<h1></h1>

<div class="question">
  <div class="interested_block" id="block_<?php echo $question->getId() ?>">
    <?php echo include_partial('interested_user', array('question' => $question)) ?>
  </div>

  <h2><?php echo esc_entities($question->getTitle()) ?>&nbsp;<?php echo link_to_rss('this question feed', '@feed_question?stripped_title='.$question->getStrippedTitle()) ?></h2>

  <div class="subtitle">
  <?php echo __('asked by %1% on %2%', array('%1%' => link_to_profile($question->getUser()), '%2%' => format_date($question->getCreatedAt(), 'f'))) ?>
  </div>

  <div class="question_body">
    <?php echo $question->getHtmlBody() ?>
    <div class="options" id="report_question_<?php echo $question->getId() ?>">
      <?php echo link_to_report_question($question, $sf_user) ?>
      <?php include_partial('moderator/question_options', array('question' => $question)) ?>
    </div>
  </div>
</div>

<h2><?php echo __('Answers') ?></h2>

<?php include_partial('answer/list', array('question' => $question, 'answers' => $answers)) ?>
