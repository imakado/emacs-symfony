<?php if ($sf_user->hasCredential('administrator')): ?>
  <h2><?php echo __('administration') ?></h2>

  <ul>
    <li><?php echo link_to(__('moderator candidates'), 'administrator/moderatorCandidates') ?> (<?php echo UserPeer::getModeratorCandidatesCount() ?>)</li>
    <li><?php echo link_to(__('moderator list'), 'administrator/moderators') ?></li>
    <li><?php echo link_to(__('administrator list'), 'administrator/administrators') ?></li>
    <li><?php echo link_to(__('problematic users'), 'administrator/problematicUsers') ?> (<?php echo UserPeer::getProblematicUsersCount() ?>)</li>
  </ul>
<?php endif ?>
