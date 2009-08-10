<h1><?php echo __('featured questions') ?></h1>

<?php echo include_partial('question_list', array('question_pager' => $question_pager, 'rule'=> '@frontpage_questions')) ?>
