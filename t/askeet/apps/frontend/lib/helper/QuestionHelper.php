<?php

function tags_for_question($question, $max = 5)
{
  $tags = array();
 
  foreach ($question->getPopularTags($max) as $tag => $count)
  {
    $tags[] = link_to($tag, '@tag?tag='.$tag);
  }
 
  return implode(' + ', $tags);
}

function link_to_question($question)
{
  return link_to(esc_entities($question->getTitle()), '@question?stripped_title='.$question->getStrippedTitle());
}

function link_to_report_question($question, $user)
{
  use_helper('Javascript');

  $text = '['.__('report to moderator').']';
  if ($user->isAuthenticated())
  {
    $has_already_reported_question = ReportQuestionPeer::retrieveByPk($question->getId(), $user->getSubscriberId());
    if ($has_already_reported_question)
    {
      // already spam for this user
      return '['.__('reported as spam').']';
    }
    else
    {
      return link_to_remote($text, array(
        'url'      => '@user_report_question?id='.$question->getId(),
        'update'   => array('success' => 'report_question_'.$question->getId()),
        'loading'  => "Element.show('indicator')",
        'complete' => "Element.hide('indicator');".visual_effect('highlight', 'report_question_'.$question->getId()),
      ));
    }
  }
  else
  {
    return link_to_login($text);
  }
}

?>