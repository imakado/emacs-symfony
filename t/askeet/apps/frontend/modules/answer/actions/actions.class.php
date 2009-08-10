<?php

/**
 * answer actions.
 *
 * @package    askeet
 * @subpackage answer
 * @author     Your name here
 * @version    SVN: $Id$
 */
class answerActions extends sfActions
{
  public function executeRecent()
  {
    $this->answer_pager = AnswerPeer::getRecentPager($this->getRequestParameter('page', 1));

    $this->getResponse()->setTitle('askeet! &raquo; recent answers');
  }

  public function executeAdd()
  {
    if ($this->getRequest()->getMethod() == sfRequest::POST)
    {
      if (!$this->getRequestParameter('body'))
      {
        return sfView::NONE;
      }

      $question = QuestionPeer::retrieveByPk($this->getRequestParameter('question_id'));
      $this->forward404Unless($question);

      // user or anonymous coward
      $user = $this->getUser()->isAuthenticated() ? $this->getUser()->getSubscriber() : UserPeer::getUserFromNickname('anonymous');

      // create answer
      $this->answer = new Answer();
      $this->answer->setQuestion($question);
      $this->answer->setBody($this->getRequestParameter('body'));
      $this->answer->setUser($user);
      $this->answer->save();

      return sfView::SUCCESS;
    }

    $this->forward404();
  }
}

?>