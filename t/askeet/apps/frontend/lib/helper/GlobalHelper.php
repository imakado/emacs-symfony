<?php

function link_to_rss($name, $uri)
{
  return link_to(image_tag('feed.gif', array('alt' => $name, 'title' => $name, 'align' => 'absmiddle')), $uri);
}

function link_to_login($name, $uri = null)
{
  use_helper('Javascript');

  if ($uri && sfContext::getInstance()->getUser()->isAuthenticated())
  {
    return link_to($name, $uri);
  }
  else
  {
    return link_to_function($name, visual_effect('blind_down', 'login', array('duration' => 0.5)));
  }
}

function pager_navigation($pager, $uri)
{
  $navigation = '';
 
  if ($pager->haveToPaginate())
  {  
    $uri .= (preg_match('/\?/', $uri) ? '&' : '?').'page=';

    // First and previous page
    if ($pager->getPage() != 1)
    {
      $navigation .= link_to(image_tag('first.gif', 'align=absmiddle'), $uri.'1');
      $navigation .= link_to(image_tag('previous.gif', 'align=absmiddle'), $uri.$pager->getPreviousPage()).'&nbsp;';
    }
    
    // Pages one by one
    $links = array();
    foreach ($pager->getLinks() as $page)
    {
      $links[] = link_to_unless($page == $pager->getPage(), $page, $uri.$page);
    }
    $navigation .= join('&nbsp;&nbsp;', $links);

    // Next and last page
    if ($pager->getPage() != $pager->getCurrentMaxLink())
    {
      $navigation .= '&nbsp;'.link_to(image_tag('next.gif', 'align=absmiddle'), $uri.$pager->getNextPage());
      $navigation .= link_to(image_tag('last.gif', 'align=absmiddle'), $uri.$pager->getLastPage());
    }

  }

  return $navigation;
}

?>