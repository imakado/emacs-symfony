<h1><?php echo __('receive your login details by email') ?></h1>

<div class="in_form">
  <p><?php echo __('Did you forget your password?') ?>
  <br /><?php echo __('Enter your email to receive your login details:') ?></p>
</div>

<?php echo form_tag('@user_require_password', 'class=form') ?>
  <?php echo form_error('email') ?>
  <label for="email"><?php echo __('email:') ?></label>
  <?php echo input_tag('email', $sf_params->get('email'), 'style=width:150px') ?>
  <br class="clearleft" />

  <div class="right">
    <?php echo submit_tag(__('send it')) ?>
  </div>
</form>
