<?php use_helper('Global') ?>

<h1><?php echo __('questions matching "%1%"', array('%1%' => htmlspecialchars($sf_params->get('search')))) ?></h1>

<?php foreach($questions as $question): ?>
  <?php echo include_partial('question/question_block', array('question' => $question)) ?>
<?php endforeach ?>

<?php if ($sf_params->get('page') > 1 && !count($questions)): ?>
  <div><?php echo __('There is no more result to your search.') ?></div>
<?php elseif (!count($questions)): ?>
  <div><?php echo __('Sorry, there is no question matching your search terms.') ?></div>
<?php endif ?>

<?php if (count($questions) == sfConfig::get('app_search_results_max')): ?>
  <div class="right">
    <?php echo link_to(__('more results').' &raquo;', '@search_question?search='.$sf_params->get('search').'&page='.($sf_params->get('page', 1) + 1)) ?>
  </div>
<?php endif ?>
