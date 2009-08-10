<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/2000/REC-xhtml1-200000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<?php echo include_http_metas() ?>
<?php echo include_metas() ?>

<?php echo include_title() ?>




<?php echo auto_discovery_link_tag('rss', 'feed/popular') ?>
<?php echo auto_discovery_link_tag('rss', 'feed/recentQuestions') ?>
<?php echo auto_discovery_link_tag('rss', 'feed/recentAnswers') ?>

<link rel="shortcut icon" href="/favicon.ico">

</head>
<body>

  <?php use_helper('Javascript')?>

  <div id="indicator" style="display: none"></div>

  <div id="header">
    <ul>
      <?php if ($sf_user->isAuthenticated()): ?>
        <li><?php echo link_to(__('%1% profile', array('%1%' => $sf_user->getAttribute('nickname', '', 'subscriber'))), '@current_user_profile') ?></li>
        <li><?php echo link_to(__('sign out'), '@logout') ?></li>
      <?php else: ?>
        <li><?php echo link_to(__('sign in / register'), '@login') ?></li>
      <?php endif ?>
      <li class="last"><?php echo link_to(__('about'), '@about') ?></li>
    </ul>
    <h1>
    <?php echo link_to(image_tag('askeet_logo.gif', 'alt=askeet align=middle'), '@homepage') ?>
    <?php echo __('ask %1%it%2% - find %1%it%2% - answer %1%it%2%', array('%1%' => '<strong>', '%2%' => '</strong>')) ?>
    </h1>
  </div>

  <div id="login" style="display: none">
    <h2><?php echo __('please sign-in first') ?></h2>
    <?php echo form_tag('@login', 'id=loginform') ?>
      <label for="nickname"><?php echo __('nickname:') ?></label><?php echo input_tag('nickname') ?>
      <label for="password"><?php echo __('password:') ?></label><?php echo input_password_tag('password') ?>
      <?php echo input_hidden_tag('referer', $sf_params->get('referer') ? $sf_params->get('referer') : $sf_request->getUri()) ?>
      <?php echo submit_tag(__('login')) ?>
      <?php echo link_to_function(__('cancel'), visual_effect('blind_up', 'login', array('duration' => 0.5))) ?>
    </form>
  </div>

  <div id="content">
    <div id="content_main">
      <?php echo $content ?>
      <div class="verticalalign"></div>
    </div>

    <div id="content_bar">
      <div class="topbar"></div>
      <?php include_component_slot('sidebar') ?>
      <div class="verticalalign"></div>
    </div>
  </div>

  <div id="footer">
  <?php echo __('powered by %1%', array('%1%' => link_to(image_tag('symfony.gif', 'align=absmiddle'), 'http://www.symfony-project.com/'))) ?>
  </div>

</body>
</html>