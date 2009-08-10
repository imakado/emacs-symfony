<?php use_helper('Global') ?>

<?php foreach($question_pager->getResults() as $question): ?>
  <?php echo include_partial('question/question_block', array('question' => $question)) ?>
<?php endforeach ?>

<div id="question_pager" class="right">
  <?php echo pager_navigation($question_pager, $rule) ?>
</div>
