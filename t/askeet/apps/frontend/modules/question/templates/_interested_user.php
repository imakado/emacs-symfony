<?php use_helper('User') ?>

<?php
  $class = 'few_interests';
  if ($question->getInterestedUsers() > 1000)
  {
    $class = 'many_interests';
  }
  else if ($question->getInterestedUsers() > 100)
  {
    $class = 'some_interests';
  }
?>

<div class="interested_mark <?php echo $class ?>" id="mark_<?php echo $question->getId() ?>">
  <?php echo $question->getInterestedUsers().'&nbsp;' ?>
</div>

<div class="interested_link"><?php echo link_to_user_interested($sf_user, $question) ?></div>
