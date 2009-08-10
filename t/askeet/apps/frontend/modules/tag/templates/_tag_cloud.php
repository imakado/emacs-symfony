<ul id="tag_cloud">
<?php foreach($tags as $tag => $count): ?>
  <li class="tag_popularity_<?php echo $count ?>"><?php echo link_to($tag, '@tag?tag='.$tag, 'rel=tag') ?></li>
<?php endforeach ?>
</ul>
