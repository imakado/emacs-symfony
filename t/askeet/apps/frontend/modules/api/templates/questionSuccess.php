<?php echo '<?' ?>xml version="1.0" encoding="utf-8" ?>
<rsp stat="ok" version="1.0">
  <question href="<?php echo url_for('@question?stripped_title='.$question->getStrippedTitle(), true) ?>" time="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', $question->getCreatedAt('U')) ?>">
    <title><?php echo esc_entities($question->getTitle()) ?></title>
    <tags>
      <?php foreach ($sf_user->getSubscriber()->getTagsFor($question) as $tag): ?>
      <tag><?php echo $tag ?></tag>
      <?php endforeach ?>
    </tags>
    <answers>
      <?php foreach ($answers as $answer): ?>
      <answer relevancy="<?php echo $answer->getRelevancyUpPercent() ?>" time="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', $answer->getCreatedAt('U')) ?>"><?php echo $answer->getBody() ?></answer>
      <?php endforeach ?>
    </answers>
  </question>
</rsp>
