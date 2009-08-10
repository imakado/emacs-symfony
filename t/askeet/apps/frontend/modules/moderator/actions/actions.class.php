<?php

/**
 * moderator actions.
 *
 * @package    askeet
 * @subpackage moderator
 * @author     Your name here
 * @version    SVN: $Id$
 */
class moderatorActions extends sfActions
{
  public function executeUnpopularTags()
  {
    $this->tags = QuestionTagPeer::getUnpopularTags();
  }

  public function executeDeleteTagForQuestion()
  {
    $question = QuestionPeer::retrieveByPK($this->getRequestParameter('question_id'));
    $this->forward404Unless($question);

    QuestionTagPeer::deleteSpam($this->getUser()->getSubscriber(), $this->getRequestParameter('tag'), $question->getId());

    $this->redirect($this->getRequest()->getReferer());
  }

  public function executeDeleteTag()
  {
    QuestionTagPeer::deleteSpam($this->getUser()->getSubscriber(), $this->getRequestParameter('tag'));

    $this->redirect($this->getRequest()->getReferer());
  }

  public function executeReportedQuestions()
  {
    $this->question_pager = QuestionPeer::getReportedSpamPager($this->getRequestParameter('page', 1));
  }

  public function executeReportedAnswers()
  {
    $this->answer_pager = AnswerPeer::getReportedSpamPager($this->getRequestParameter('page', 1));
  }

  public function executeDeleteQuestion()
  {
    $question = QuestionPeer::getQuestionFromTitle($this->getRequestParameter('stripped_title'));
    $this->forward404Unless($question);

    $question->deleteSpam($this->getUser()->getSubscriber());

    $this->redirect($this->getRequest()->getReferer());
  }

  public function executeDeleteAnswer()
  {
    $answer = AnswerPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($answer);

    $answer->deleteSpam($this->getUser()->getSubscriber());

    $this->redirect($this->getRequest()->getReferer());
  }

  public function executeResetQuestionReports()
  {
    $question = QuestionPeer::getQuestionFromTitle($this->getRequestParameter('stripped_title'));
    $this->forward404Unless($question);

    $question->deleteReports();
    $question->save();

    $this->redirect($this->getRequest()->getReferer());
  }

  public function executeResetAnswerReports()
  {
    $answer = AnswerPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($answer);

    $answer->deleteReports();
    $answer->save();

    $this->redirect($this->getRequest()->getReferer());
  }
}

?>