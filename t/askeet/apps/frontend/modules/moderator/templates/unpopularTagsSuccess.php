<h1><?php echo __('unpopular tags') ?></h1>

<ul>
<?php foreach ($tags as $tag => $count): ?>
  <li><?php echo $tag.' ('.$count.')' ?> <?php echo link_to('['.__('delete tag').']', 'moderator/deleteTag?tag='.$tag, 'confirm='.__('Are you sure you want to delete this tag?')) ?></li>
<?php endforeach ?>
</ul>
