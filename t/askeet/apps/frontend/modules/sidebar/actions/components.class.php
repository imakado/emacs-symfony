<?php

/**
 * sidebar components.
 *
 * @package    askeet
 * @subpackage sidebar
 * @author     Your name here
 * @version    SVN: $Id$
 */
class sidebarComponents extends sfComponents
{
  public function executeDefault()
  {
  }

  public function executeQuestion()
  {
    $this->question = QuestionPeer::getQuestionFromTitle($this->getRequestParameter('stripped_title'));
  }
}

?>