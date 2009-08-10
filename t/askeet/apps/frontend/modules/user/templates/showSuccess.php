<?php use_helper('Date', 'Question', 'Text', 'Object', 'Validation') ?>

<h1><?php echo __('%1%\'s profile', array('%1%' => $subscriber->__toString())) ?>
<?php if ($subscriber->getIsModerator()): ?> [<?php echo __('moderator') ?>]<?php endif ?>
<?php if ($subscriber->getIsAdministrator()): ?> [<?php echo __('administrator') ?>]<?php endif ?>
</h1>

<?php echo include_partial('administrator/user_options', array('subscriber' => $subscriber)) ?>

<?php if ($subscriber->getId() == $sf_user->getSubscriberId()): ?>
  <?php echo form_tag('user/update', 'class=form') ?>
    <fieldset>

    <label for="nickname"><?php echo __('nickname:') ?></label>
    <strong><?php echo $subscriber->getNickname() ?></strong>
    <br class="clearleft" />

    <?php echo form_error('first_name') ?>
    <label for="first_name"><?php echo __('first name:') ?></label>
    <?php echo object_input_tag($subscriber, 'getFirstName', 'size=30') ?>
    <br class="clearleft" />

    <?php echo form_error('last_name') ?>
    <label for="last_name"><?php echo __('last name:') ?></label>
    <?php echo object_input_tag($subscriber, 'getLastName', 'size=30') ?>
    <br class="clearleft" />

    <?php echo form_error('email') ?>
    <label for="email"><?php echo __('email:') ?></label>
    <?php echo object_input_tag($subscriber, 'getEmail', 'size=30') ?>
    <br class="clearleft" />

    <label for="has_paypal"><?php echo __('paypal account?') ?></label>
    <?php echo object_checkbox_tag($subscriber, 'getHasPaypal') ?>
    <br class="clearleft" />

    <?php echo form_error('password') ?>
    <label for="password"><?php echo __('password:') ?></label>
    <?php echo input_password_tag('password', '', 'size=30') ?>
    <br class="clearleft" />

    <?php echo form_error('password_bis') ?>
    <label for="password_bis"><?php echo __('confirm your password:') ?></label>
    <?php echo input_password_tag('password_bis', '', 'size=30') ?>
    <br class="clearleft" />

    <?php if (!$subscriber->getIsModerator()): ?>
      <label for="want_to_be_moderator"><?php echo __('do you want to be a moderator?') ?></label>
      <?php echo object_checkbox_tag($subscriber, 'getWantToBeModerator') ?>
      <br class="clearleft" />
    <?php endif ?>

    </fieldset>

    <div class="right">
      <?php echo submit_tag(__('update it')) ?>
    </div>
  </form>
<?php endif ?>

<?php if ($subscriber->getHasPaypal()): ?>
  <p><?php echo __('If you appreciated this user\'s contributions, you can grant him a small donation.') ?></p>
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="<?php echo $subscriber->getEmail() ?>">
    <input type="hidden" name="item_name" value="askeet">
    <input type="hidden" name="return" value="http://www.askeet.com">
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="tax" value="0">
    <input type="hidden" name="bn" value="PP-DonationsBF">
    <input type="image" src="http://images.paypal.com/images/x-click-but04.gif" border="0" name="submit" alt="Donate to this user">
  </form>
<?php endif ?>

<h3><?php echo __('tags') ?></h3>

<ul id="question_tags">
  <?php echo include_partial('tag/tag_cloud', array('tags' => $subscriber->getPopularTags())) ?>
</ul>

<h3><?php echo __('contributions') ?></h3>

<?php foreach ($answers as $answer): $question = $answer->getQuestion() ?>

<div class="vote_block" id="vote_<?php echo $answer->getId() ?>">
  <?php echo include_partial('answer/vote_user', array('answer' => $answer)) ?>
</div>

<div class="answer_body">
  <h2><?php echo link_to_question($answer->getQuestion()) ?></h2>
  <?php echo $answer->getHtmlBody() ?>
  <div class="subtitle"><?php echo __('posted on %1%', array('%1%' => format_date($answer->getCreatedAt(), 'f'))) ?></div>
</div>

<br class="clearleft" />

<?php endforeach ?>

<h3><?php echo __('questions') ?></h3>

<?php foreach ($questions as $question): ?>
<div class="question">
  <div class="interested_block" id="block_<?php echo $question->getId() ?>">
    <?php echo include_partial('question/interested_user', array('question' => $question)) ?>
  </div>

  <h2><?php echo link_to_question($question) ?></h2>

  <div class="subtitle"><?php echo __('submitted on %1%', array('%1%' => format_date($question->getCreatedAt(), 'f'))) ?></div>

  <div class="question_body">
    <?php echo truncate_text(strip_tags($question->getHtmlBody()), 200) ?>
    <?php if ($question->getTags()): ?>
      <div class="tags"><?php echo __('tags:') ?> <?php echo tags_for_question($question) ?></div>
    <?php endif ?>
  </div>
</div>
<?php endforeach ?>

<h3><?php echo __('interests') ?></h3>

<ul class="plain_list">
<?php foreach ($interests as $interest): $question = $interest->getQuestion() ?>
  <li><?php echo link_to(esc_entities($question->getTitle()), '@question?stripped_title='.$question->getStrippedTitle()) ?></li>
<?php endforeach ?>
</ul>
