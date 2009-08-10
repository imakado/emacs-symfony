<?php use_helper('Answer') ?>

<div class="vote_up_mark"><?php echo link_to_user_relevancy_up($sf_user, $answer) ?></div>
<div class="vote_down_mark"><?php echo link_to_user_relevancy_down($sf_user, $answer) ?></div>

<br class="clearleft" />

<div class="vote_up_mark" id="vote_up_<?php echo $answer->getId() ?>">
  <?php echo $answer->getRelevancyUpPercent() ?>%
</div>
<div class="vote_down_mark" id="vote_down_<?php echo $answer->getId() ?>">
  <?php echo $answer->getRelevancyDownPercent() ?>%
</div>
