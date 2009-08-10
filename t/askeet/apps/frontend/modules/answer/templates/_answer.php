<?php use_helper('Date', 'User') ?>

<div class="vote_block" id="vote_<?php echo $answer->getId() ?>">
  <?php echo include_partial('answer/vote_user', array('answer' => $answer)) ?>
</div>

<div class="answer_body">
  <?php echo $answer->getHtmlBody() ?>
  <div class="subtitle" style="margin-top: -8px"><?php echo __('answered by %1% on %2%', array('%1%' => link_to_profile($answer->getUser()), '%2%' => format_date($answer->getCreatedAt(), 'f'))) ?></div>
  <div class="options" id="report_answer_<?php echo $answer->getId() ?>">
    <?php echo link_to_report_answer($answer, $sf_user) ?>
    <?php echo include_partial('moderator/answer_options', array('answer' => $answer)) ?>
  </div>
</div>

<br class="clearleft" />
