<?php use_helper('Global') ?>

<div id="answers">

  <?php foreach ($answers as $answer): ?>
    <div class="answer">
      <?php include_partial('answer/answer', array('answer' => $answer)) ?>
    </div>
  <?php endforeach ?>

  <div class="answer" id="add_answer">
    <?php echo form_remote_tag(array(
      'url'      => '@add_answer',
      'update'   => array('success' => 'add_answer'),
      'loading'  => "Element.show('indicator')",
      'complete' => "Element.hide('indicator');".visual_effect('highlight', 'add_answer'),
    ), 'class=form') ?>
      <fieldset>

      <label for="author"><?php echo __('author:') ?></label>
      <div style="display: inline; float: left">
      <?php if ($sf_user->isAuthenticated()): ?>
        <?php echo $sf_user->getNickname() ?>
      <?php else: ?>
        <?php echo __('anonymous coward') ?>&nbsp;
        <?php echo link_to_login('['.__('login').']') ?>
      <?php endif ?>
      </div>
      <br class="clearleft" />

      <label for="body"><?php echo __('your answer:') ?></label>
      <?php echo textarea_tag('body', $sf_params->get('body'), 'size=40x10') ?>
      <br class="clearleft" />
      <?php echo include_partial('content/markdown_help') ?>

      </fieldset>

      <?php echo input_hidden_tag('question_id', $question->getId()) ?>
      <div class="right">
        <?php echo submit_tag('answer it') ?>
      </div>
    </form>
  </div>

</div>
