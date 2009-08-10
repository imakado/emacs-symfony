<?php use_helper('Text', 'Global', 'Question', 'Date') ?>

<div class="question">
  <div class="interested_block" id="block_<?php echo $question->getId() ?>">
    <?php echo include_partial('question/interested_user', array('question' => $question)) ?>
  </div>

  <h2><?php echo link_to_question($question) ?></h2>

  <div class="subtitle">
  <?php echo __('asked by %1% on %2%', array('%1%' => link_to_profile($question->getUser()), '%2%' => format_date($question->getCreatedAt(), 'f'))) ?>
  </div>

  <div class="question_body">
    <?php echo truncate_text(strip_tags($question->getHtmlBody()), 200) ?>

    <div class="options">

    <?php if ($question->getAnswers()): ?>
      <?php if (count($question->getAnswers()) > 1): ?>
        <?php echo link_to(__('%1% answers', array('%1%' => count($question->getAnswers()))), '@question?stripped_title='.$question->getStrippedTitle()) ?>
      <?php else: ?>
      <?php echo link_to(__('%1% answer', array('%1%' => count($question->getAnswers()))), '@question?stripped_title='.$question->getStrippedTitle()) ?>
      <?php endif ?>
    <?php else: ?>
      <?php echo link_to(__('answer it'), '@question?stripped_title='.$question->getStrippedTitle()) ?>
    <?php endif ?>

    &nbsp;-&nbsp;

    <?php if ($question->getTags()): ?>
      <?php echo __('tags:') ?> <?php echo tags_for_question($question) ?>
    <?php endif ?>

    </div>

    <div class="options" id="report_question_<?php echo $question->getId() ?>">
      <?php echo link_to_report_question($question, $sf_user) ?>
      <?php include_partial('moderator/question_options', array('question' => $question)) ?>
    </div>

  </div>
</div>
