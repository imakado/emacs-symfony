<h1><?php echo __('popular questions') ?></h1>

<?php echo include_partial('question_list', array('question_pager' => $question_pager, 'rule' => '@popular_questions')) ?>
