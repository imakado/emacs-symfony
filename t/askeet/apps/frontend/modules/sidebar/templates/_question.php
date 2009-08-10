<?php use_helper('Global', 'Javascript') ?>

<div id="add_question">
  <?php echo link_to_login(__('ask a new question'), '@add_question') ?>
</div>

<h2><?php echo __('find it') ?></h2>
<?php echo include_partial('question/search') ?>

<h2><?php echo __('browse askeet') ?></h2>
<?php echo include_partial('sidebar/rss_links') ?>

<h2><?php echo __('question tags') ?></h2>

<ul id="question_tags">
  <?php echo include_partial('tag/question_tags', array('question' => $question, 'tags' => $question->getTags())) ?> 
</ul>

<?php if ($sf_user->isAuthenticated()): ?>
  <div><?php echo __('add your own:') ?>
  <?php echo form_remote_tag(array(
    'url'      => '@tag_add',
    'update'   => 'question_tags',
    'loading'  => "Element.show('indicator'); \$('tag').value = ''",
    'complete' => "Element.hide('indicator');".visual_effect('highlight', 'question_tags'),
  )) ?>
    <?php echo input_hidden_tag('question_id', $question->getId()) ?>
    <?php echo input_auto_complete_tag('tag', '', '@tag_autocomplete', 'autocomplete=off', 'use_style=true') ?>
    <?php echo submit_tag('tag') ?>
  </form>
  </div>
<?php endif ?>

<?php echo include_partial('sidebar/moderation') ?>

<?php echo include_partial('sidebar/administration') ?>
