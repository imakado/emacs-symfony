<?php use_helper('Global') ?>

<div id="add_question">
  <?php echo link_to_login(__('ask a new question'), '@add_question') ?>
</div>

<h2><?php echo __('popular tags') ?></h2>
<?php echo include_partial('tag/tag_cloud', array('tags' => QuestionTagPeer::getPopularTags(20))) ?>
<div class="right" style="padding-top: 5px"><?php echo link_to(__('more popular tags').' &raquo;', '@popular_tags') ?></div>

<h2><?php echo __('find it') ?></h2>
<?php echo include_partial('question/search') ?>

<h2><?php echo __('browse askeet') ?></h2>
<?php echo include_partial('sidebar/rss_links') ?>

<?php echo include_partial('sidebar/moderation') ?>

<?php echo include_partial('sidebar/administration') ?>
