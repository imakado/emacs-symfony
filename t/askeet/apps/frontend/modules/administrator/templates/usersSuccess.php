<h1><?php echo $title ?></h1>

<?php foreach ($users as $subscriber): ?>
  <h2><?php echo link_to($subscriber->getNickname(), '@user_profile?nickname='.$subscriber->getNickname()) ?></h2>

  <?php echo include_partial('administrator/user_options', array('subscriber' => $subscriber)) ?>

  <br class="clearleft" />
<?php endforeach ?>

<br />
