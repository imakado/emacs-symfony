<?php use_helper('Javascript', 'Validation') ?>

<h1><?php echo __('ask a question') ?></h1>

<div class="in_form">
  <p>
  <?php echo __('Have you looked for similar questions? Check if a related question already exists: The more interesting a question is, the more people will be willing to answer it.') ?>
  </p>

  <p>
  <?php echo __('Be as accurate as you can when giving a title to your question. Keep it short and put the details in the question body.') ?>
  </p>
</div>

<?php echo form_tag('@add_question', 'class=form') ?>

  <fieldset>

  <?php echo form_error('title') ?>
  <label for="title"><?php echo __('question:') ?></label>
  <?php echo input_tag('title', $sf_params->get('title'), 'size=40') ?>
  <br class="clearleft" />

  <?php echo form_error('body') ?>
  <label for="label"><?php echo __('describe it:') ?></label>
  <?php echo textarea_tag('body', $sf_params->get('body'), 'size=40x10') ?>
  <br class="clearleft" />
  <?php echo include_partial('content/markdown_help') ?>

  <?php echo form_error('tag') ?>
  <label for="tag"><?php echo __('tags:') ?></label>
  <?php echo input_auto_complete_tag('tag', '', '@tag_autocomplete', 'autocomplete=off', 'use_style=true') ?>
  <br class="clearleft" />
  <div class="small in_form"><?php echo __('example: askeet "how to"') ?></div>

  </fieldset>

  <div class="right">
    <?php echo submit_tag(__('ask it')) ?>
  </div>
</form>
