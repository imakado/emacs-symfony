<?php

/**
 * user actions.
 *
 * @package    askeet
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id$
 */
class userActions extends sfActions
{
  public function executeListInterestedBy()
  {
    $this->question = QuestionPeer::getQuestionFromTitle($this->getRequestParameter('stripped_title'));
    $this->forward404Unless($this->question instanceof Question);

    $page = $this->getRequestParameter('page', 1);

    $this->interested_users_pager = $this->question->getInterestedUsersPager($page);
  }

  public function executeInterested()
  {
    $this->question = QuestionPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->question);

    $this->getUser()->getSubscriber()->isInterestedIn($this->question);
  }

  public function executeVote()
  {
    $this->answer = AnswerPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->answer);

    $user = $this->getUser()->getSubscriber();

    $relevancy = new Relevancy();
    $relevancy->setAnswer($this->answer);
    $relevancy->setUser($user);
    $relevancy->setScore($this->getRequestParameter('score') == 1 ? 1 : -1);
    $relevancy->save();
  }

  public function executeShow()
  {
    if ($this->hasRequestParameter('nickname'))
    {
      $this->subscriber = UserPeer::getUserFromNickname($this->getRequestParameter('nickname'));
    }
    else
    {
      $this->subscriber = $this->getUser()->getSubscriber();
    }
    $this->forward404Unless($this->subscriber);

    $this->setShowVars();
  }

  public function executeUpdate()
  {
    if ($this->getRequest()->getMethod() != sfRequest::POST)
    {
      $this->forward404();
    }

    $this->subscriber = $this->getUser()->getSubscriber();
    $this->forward404Unless($this->subscriber);

    $this->updateUserFromRequest();

    // password update
    if ($this->getRequestParameter('password'))
    {
      $this->subscriber->setPassword($this->getRequestParameter('password'));
    }

    $this->subscriber->save();

    $this->redirect('@user_profile?nickname='.$this->subscriber->getNickname());
  }

  public function executeLogin()
  {
    $this->getRequest()->setAttribute('newaccount', false);

    if ($this->getRequest()->getMethod() != sfRequest::POST)
    {
      // display the form
      $this->getResponse()->setTitle('askeet! &raquo; sign in / register');
      $this->getRequest()->getAttributeHolder()->set('referer', $this->getRequest()->getReferer());

      return sfView::SUCCESS;
    }
    else
    {
      // handle the form submission
      // redirect to last page
      return $this->redirect($this->getRequestParameter('referer', '@homepage'));
    }
  }

  public function executeLogout()
  {
    $this->getUser()->signOut();

    $this->redirect('@homepage');
  }

  public function executePasswordRequest()
  {
    if ($this->getRequest()->getMethod() != sfRequest::POST)
    {
      // display the form
      return sfView::SUCCESS;
    }

    // handle the form submission
    $c = new Criteria();
    $c->add(UserPeer::EMAIL, $this->getRequestParameter('email'));
    $user = UserPeer::doSelectOne($c);

    // email exists?
    if ($user)
    {
      // set new random password
      $password = substr(md5(rand(100000, 999999)), 0, 6);
      $user->setPassword($password);

      $this->getRequest()->setAttribute('password', $password);
      $this->getRequest()->setAttribute('nickname', $user->getNickname());

      $raw_email = $this->sendEmail('mail', 'sendPassword');
      $this->getLogger()->debug($raw_email);

      // save new password
      $user->save();

      return 'MailSent';
    }
    else
    {
      $this->getRequest()->setError('email', 'There is no askeet user with this email address. Please try again');

      return sfView::SUCCESS;
    }
  }

  public function executeAdd()
  {
    // process only POST requests
    if ($this->getRequest()->getMethod() == sfRequest::POST)
    {
      $user = new User();
      $user->setNickname($this->getRequestParameter('nickname'));
      $user->setEmail($this->getRequestParameter('email'));
      $user->setPassword($this->getRequestParameter('password'));

      $user->save();

      $this->forward('user', 'login');
    }

    $this->getRequest()->setAttribute('newaccount', true);
    $this->forward('user', 'login');
  }

  public function executeReportQuestion()
  {
    $this->question = QuestionPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->question);

    $spam = new ReportQuestion();
    $spam->setQuestionId($this->question->getId());
    $spam->setUserId($this->getUser()->getSubscriberId());
    $spam->save();
  }

  public function executeReportAnswer()
  {
    $this->answer = AnswerPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($this->answer);

    $spam = new ReportAnswer();
    $spam->setAnswerId($this->answer->getId());
    $spam->setUserId($this->getUser()->getSubscriberId());
    $spam->save();
  }

  public function handleErrorLogin()
  {
    return sfView::SUCCESS;
  }

  public function handleErrorAdd()
  {
    $this->getRequest()->setAttribute('newaccount', true);

    return array('user', 'loginSuccess');
  }

  public function handleErrorPasswordRequest()
  {
    return sfView::SUCCESS;
  }

  public function handleErrorUpdate()
  {
    $this->subscriber = $this->getUser()->getSubscriber();
    $this->forward404Unless($this->subscriber);

    $this->updateUserFromRequest();
    $this->setShowVars();

    $this->setTemplate('show');
    return sfView::SUCCESS;
  }

  private function updateUserFromRequest()
  {
    $this->subscriber->setFirstName($this->getRequestParameter('first_name'));
    $this->subscriber->setLastName($this->getRequestParameter('last_name'));
    $this->subscriber->setEmail($this->getRequestParameter('email'));
    $this->subscriber->setHasPaypal($this->getRequestParameter('has_paypal'), 0);
    $this->subscriber->setWantToBeModerator($this->getRequestParameter('want_to_be_moderator'));
  }

  private function setShowVars()
  {
    $this->interests = $this->subscriber->getInterestsJoinQuestion();
    $this->answers   = $this->subscriber->getAnswersJoinQuestion();
    $this->questions = $this->subscriber->getQuestions();

    $this->getResponse()->setTitle('askeet! &raquo; '.$this->subscriber->__toString().'\'s profile');
  }
}

?>