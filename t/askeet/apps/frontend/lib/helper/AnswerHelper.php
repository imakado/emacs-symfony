<?php

use_helper('Global');

function answer_pager_link($name, $question, $page)
{
  return link_to($name, '@question?stripped_title='.$question->getStrippedTitle().'&page='.$page);
}

function link_to_user_relevancy_up($user, $answer)
{
  return link_to_user_relevancy('up', $user, $answer);
}

function link_to_user_relevancy_down($user, $answer)
{
  return link_to_user_relevancy('down', $user, $answer);
}

function link_to_user_relevancy($name, $user, $answer)
{
  use_helper('Javascript');

  if ($user->isAuthenticated())
  {
    $has_already_voted = RelevancyPeer::retrieveByPk($answer->getId(), $user->getSubscriberId());
    if ($has_already_voted || $answer->getUserId() == $user->getSubscriberId())
    {
      // already interested
      return image_tag('thumb_'.$name.'_voted.gif');
    }
    else
    {
      // didn't declare interest yet
      return link_to_remote(image_tag('thumb_'.$name.'.gif'), array(
        'url'      => 'user/vote?id='.$answer->getId().'&score='.($name == 'up' ? 1 : -1),
        'update'   => array('success' => 'vote_'.$answer->getId()),
        'loading'  => "Element.show('indicator')",
        'complete' => "Element.hide('indicator');".visual_effect('highlight', 'vote_'.$name.'_'.$answer->getId()),
      ));
    }
  }
  else
  {
    return link_to_login(image_tag('thumb_'.$name.'.gif'));
  }
}

function link_to_report_answer($answer, $user)
{
  use_helper('Javascript');

  $text = '['.__('report to moderator').']';
  if ($user->isAuthenticated())
  {
    $has_already_reported_answer = ReportAnswerPeer::retrieveByPk($answer->getId(), $user->getSubscriberId());
    if ($has_already_reported_answer)
    {
      // already spam for this user
      return '['.__('reported as spam').']';
    }
    else
    {
      return link_to_remote($text, array(
        'url'      => '@user_report_answer?id='.$answer->getId(),
        'update'   => array('success' => 'report_answer_'.$answer->getId()),
        'loading'  => "Element.show('indicator')",
        'complete' => "Element.hide('indicator');".visual_effect('highlight', 'report_answer_'.$answer->getId()),
      ));
    }
  }
  else
  {
    return link_to_login($text);
  }
}

?>