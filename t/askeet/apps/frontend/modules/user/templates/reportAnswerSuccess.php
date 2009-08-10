<?php use_helper('Answer') ?>

<?php echo link_to_report_answer($answer, $sf_user) ?>
<?php echo include_partial('moderator/answer_options', array('answer' => $answer)) ?>
