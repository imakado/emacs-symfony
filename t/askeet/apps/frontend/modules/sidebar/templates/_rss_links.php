<ul id="rss_links">
  <li><?php echo link_to(__('featured questions'), '@frontpage_questions') ?> </li>
  <li><?php echo link_to(__('popular questions'), '@popular_questions') ?> <?php echo link_to_rss('popular questions', 'feed/popular') ?></li>
  <li><?php echo link_to(__('latest questions'), '@recent_questions') ?> <?php echo link_to_rss('latest questions', '@feed_recent_questions') ?></li>
  <li><?php echo link_to(__('latest answers'), '@recent_answers') ?> <?php echo link_to_rss('latest answers', '@feed_recent_answers') ?></li>
</ul>
