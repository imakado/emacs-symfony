<?php use_helper('Date', 'Global') ?>

<h2><?php echo esc_entities($question->getTitle()) ?></h2>
<p><?php echo __('asked by %1% %2% ago', array('%1%' => '<strong>'.$question->getUser().'</strong>', '%2%' => time_ago_in_words($question->getCreatedAt('U')))) ?></p>
 
<?php echo __('%1% askeet users are interested by this question', array('%1%' => $interested_users_pager->getNbResults())) ?>
<ul>
  <?php foreach ($interested_users_pager->getResults() as $interested_user): ?>
  <li><?php echo link_to($interested_user->__toString(), '@user_profile?nickname='.$interested_user->getNickname()) ?></li>
  <?php endforeach ?>
</ul>

<div id="users_pager">
  <?php echo pager_navigation($interested_users_pager, '@user_interests?stripped_title='.$sf_request->getParameter('stripped_title')) ?>
</div>
