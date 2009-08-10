<?php

/**
 * myTagFilter class.
 *
 * @package    askeet
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id$
 */
class myTagFilter extends sfFilter
{
  /**
    * Execute this filter.
    *
    * @param FilterChain The filter chain.
    *
    * @return void
    * @throws <b>FilterException</b> If an erro occurs during execution.
    */
  public function execute ($filterChain)
  {
    // execute this filter only once
    if (sfConfig::get('app_universe') && $this->isFirstCall())
    {
      // is there a tag in the hostname?
      $request  = $this->getContext()->getRequest();
      $hostname = $request->getHost();
      if (!preg_match($this->getParameter('host_exclude_regex'), $hostname) && $pos = strpos($hostname, '.'))
      {
        $tag = Tag::normalize(substr($hostname, 0, $pos));

        // add a permanent tag constant
        sfConfig::set('app_permanent_tag', $tag);

        // add a custom stylesheet
        $this->getContext()->getResponse()->addStylesheet($tag);

        // is the tag a culture?
        if (is_readable(sfConfig::get('sf_app_i18n_dir').'/messages.'.strtolower($tag).'.xml'))
        {
          $this->getContext()->getUser()->setCulture(strtolower($tag));
        }
        else
        {
          $this->getContext()->getUser()->setCulture('en');
        }
      }
    }

    // execute next filter
    $filterChain->execute();
  }
}

?>