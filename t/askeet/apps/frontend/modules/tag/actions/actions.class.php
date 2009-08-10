<?php

/**
 * tag actions.
 *
 * @package    askeet
 * @subpackage tag
 * @author     Your name here
 * @version    SVN: $Id$
 */
class tagActions extends sfActions
{
  public function executeShow()
  {
    $this->question_pager = QuestionPeer::getPopularByTag($this->getRequestParameter('tag'), $this->getRequestParameter('page', 1));

    $this->getResponse()->setTitle('askeet! &raquo; question tagged '.Tag::normalize($this->getRequestParameter('tag')));
  }

  public function executeAutocomplete()
  {
    // disable web debug toolbar
    sfConfig::set('sf_web_debug', false);

    $this->tags = QuestionTagPeer::getForUserLike($this->getUser()->getSubscriberId(), $this->getRequestParameter('tag'));
  }

  public function executeAdd()
  {
    // disable web debug toolbar
    sfConfig::set('sf_web_debug', false);

    $this->question = QuestionPeer::retrieveByPk($this->getRequestParameter('question_id'));
    $this->forward404Unless($this->question);

    $userId = $this->getUser()->getSubscriberId();
    $phrase = $this->getRequestParameter('tag');
    $this->question->addTagsForUser($phrase, $userId);

    $this->tags = $this->question->getTags();

    // clear the question tag list fragment in cache
    if (sfConfig::get('sf_cache'))
    {
      $this->getContext()->getViewCacheManager()->remove('@question?stripped_title='.$this->question->getStrippedTitle(), 'fragment_question_tags');
    }
  }

  public function executeRemove()
  {
    // disable web debug toolbar
    sfConfig::set('sf_web_debug', false);

    $this->question = QuestionPeer::getQuestionFromTitle($this->getRequestParameter('stripped_title'));
    $this->forward404Unless($this->question);

    // remove tag for this user and question
    $user = $this->getUser()->getSubscriber();
    $tag  = $this->getRequestParameter('tag');

    $user->removeTag($this->question, $tag);

    $this->tags = $this->question->getTags();

    // clear the question tag list fragment in cache
    if (sfConfig::get('sf_cache'))
    {
      $this->getContext()->getViewCacheManager()->remove('@question?stripped_title='.$this->question->getStrippedTitle(), 'fragment_question_tags');
    }
  }
 
  public function executePopular()
  {
    $this->tags = QuestionTagPeer::getPopularTags(40);

    $this->getResponse()->setTitle('askeet! &raquo; popular tags');
  }
}

?>