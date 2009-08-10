<?php use_helper('Question') ?>

<?php echo link_to_report_question($question, $sf_user) ?>
<?php include_partial('moderator/question_options', array('question' => $question)) ?>
