<?php

require_once 'lib/model/om/BaseQuestion.php';


/**
 * Skeleton subclass for representing a row from the 'ask_question' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class Question extends BaseQuestion
{
  public function setTitle($v)
  {
    parent::setTitle($v);

    $this->setStrippedTitle(myTools::stripText($v));
  }

  public function setBody($v)
  {
    parent::setBody($v);

    require_once('markdown.php');

    // strip all HTML tags
    $v = htmlentities($v, ENT_QUOTES, 'UTF-8');

    $this->setHtmlBody(markdown($v));
  }

  public function getAllInterestedUsers()
  {
    $c = new Criteria();
    $c->addJoin(UserPeer::ID, InterestPeer::USER_ID, Criteria::LEFT_JOIN);
    $c->add(InterestPeer::QUESTION_ID, $this->getId());

    return UserPeer::doSelect($c);
  }

  public function getInterestedUsersPager($page)
  {   
    $c = new Criteria();
    $c->addJoin(UserPeer::ID, InterestPeer::USER_ID, Criteria::LEFT_JOIN);
    $c->add(InterestPeer::QUESTION_ID, $this->getId());

    $pager = new sfPropelPager('User', sfConfig::get('app_pager_users_max'));
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  public function getTags()
  {
    $c = new Criteria();
    $c->add(QuestionTagPeer::QUESTION_ID, $this->getId());
    $c->addGroupByColumn(QuestionTagPeer::NORMALIZED_TAG);
    $c->setDistinct();
    $c->addAscendingOrderByColumn(QuestionTagPeer::NORMALIZED_TAG);

    $tags = array();
    foreach (QuestionTagPeer::doSelect($c) as $tag)
    {
      if (sfConfig::get('app_permanent_tag') == $tag)
      {
        continue;
      }

      $tags[] = $tag->getNormalizedTag();
    }

    return $tags;
  }

  public function getPopularTags($max = 5)
  {
    $tags = array();

    $con = Propel::getConnection();
    $query = '
      SELECT %s AS tag, COUNT(%s) AS count
      FROM %s
      WHERE %s = ?
      GROUP BY %s
      ORDER BY count DESC
    ';

    $query = sprintf($query,
      QuestionTagPeer::NORMALIZED_TAG,
      QuestionTagPeer::NORMALIZED_TAG,
      QuestionTagPeer::TABLE_NAME,
      QuestionTagPeer::QUESTION_ID,
      QuestionTagPeer::NORMALIZED_TAG
    );

    $stmt = $con->prepareStatement($query);
    $stmt->setInt(1, $this->getId());
    $stmt->setLimit($max);
    $rs = $stmt->executeQuery();
    while ($rs->next())
    {
      if (sfConfig::get('app_permanent_tag') == $rs->getString('tag'))
      {
        continue;
      }

      $tags[$rs->getString('tag')] = $rs->getInt('count');
    }

    return $tags;
  }

  public function addTagsForUser($phrase, $userId)
  {
    // split phrase into individual tags
    $tags = Tag::splitPhrase($phrase.' '.sfConfig::get('app_permanent_tag'));

    // add tags
    foreach ($tags as $tag)
    {
      $questionTag = new QuestionTag();
      $questionTag->setQuestionId($this->getId());
      $questionTag->setUserId($userId);
      $questionTag->setTag($tag);
      try
      {
        $questionTag->save();
      }
      catch (PropelException $e)
      {
        // duplicate tag for this user and question
      }
    }
  }

  public function getPopularAnswers()
  {
    $c = new Criteria();
    $c->add(AnswerPeer::QUESTION_ID, $this->getId());
    $c->addAsColumn('relevancy', AnswerPeer::RELEVANCY_UP.' / ('.AnswerPeer::RELEVANCY_UP.' + '.AnswerPeer::RELEVANCY_DOWN.')');
    $c->addDescendingOrderByColumn('relevancy');
    $c->addDescendingOrderByColumn(AnswerPeer::RELEVANCY_UP);

    return AnswerPeer::doSelect($c);
  }

  public function getWords()
  {
    // body
    $raw_text =  str_repeat(' '.strip_tags($this->getHtmlBody()), sfConfig::get('app_search_body_weight'));

    // title
    $raw_text .= str_repeat(' '.$this->getTitle(), sfConfig::get('app_search_title_weight'));

    // title and body stemming
    $stemmed_words = myTools::stemPhrase($raw_text);

    // unique words with weight
    $words = array_count_values($stemmed_words);

    // add tags
    $max = 0;
    foreach ($this->getPopularTags(20) as $tag => $count)
    {
      if (!$max)
      {
        $max = $count;
      }

      $stemmed_tag = PorterStemmer::stem($tag);

      if (!isset($words[$stemmed_tag]))
      {
        $words[$stemmed_tag] = 0;
      }
      $words[$stemmed_tag] += ceil(($count / $max) * sfConfig::get('app_search_tag_weight'));
    }

    return $words;
  }

  public function save($con = null)
  {
    $con = Propel::getConnection();
    try
    {
      $con->begin();

      $ret = parent::save();

      $this->updateSearchIndex();

      $con->commit();

      return $ret;
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }

  public function updateSearchIndex()
  {
    // update search index
    $c = new Criteria();
    $c->add(SearchIndexPeer::QUESTION_ID, $this->getId());
    SearchIndexPeer::doDelete($c);

    foreach ($this->getWords() as $word => $weight)
    {
      $index = new SearchIndex();
      $index->setQuestionId($this->getId());
      $index->setWord($word);
      $index->setWeight($weight);
      $index->save();
    }
  }

  public function hasTag($tag)
  {
    $c = new Criteria();
    $c->add(QuestionTagPeer::QUESTION_ID, $this->getId());
    $c->add(QuestionTagPeer::NORMALIZED_TAG, Tag::normalize($tag));

    return QuestionTagPeer::doSelectOne($c) ? true : false;
  }

  public function deleteReports()
  {
    $reports = $this->getReportQuestions();
    foreach ($reports as $report)
    {
      $report->delete();
    }

    $this->setReports(0);
  }

  public function deleteSpam($moderator)
  {
    $con = Propel::getConnection();
    try
    {
      $con->begin();

      $user = $this->getUser();
      $user->setDeletions($user->getDeletions() + 1);
      $user->save();

      $this->delete();

      $con->commit();

      $log = 'moderator "%s" deleted question "%s"';
      $log = sprintf($log, $moderator->getNickname(), $this->getTitle());
      sfContext::getInstance()->getLogger()->warning($log);
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e;
    }
  }
  
  public function getFeedAuthorEmail()
  {
    return '';  
  }  
}

?>