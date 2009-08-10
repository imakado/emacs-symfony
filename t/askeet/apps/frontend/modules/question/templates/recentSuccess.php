<h1><?php echo __('recent questions') ?></h1>

<?php echo include_partial('question_list', array('question_pager' => $question_pager, 'rule' => '@recent_questions')) ?>
