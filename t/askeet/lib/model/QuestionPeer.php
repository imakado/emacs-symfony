<?php

  // include base peer class
  require_once 'lib/model/om/BaseQuestionPeer.php';
  
  // include object class
  include_once 'lib/model/Question.php';


/**
 * Skeleton subclass for performing query and update operations on the 'ask_question' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class QuestionPeer extends BaseQuestionPeer
{
  public static function getQuestionFromTitle($title)
  {
    $c = new Criteria();
    $c->add(self::STRIPPED_TITLE, $title);

    $questions = self::doSelectJoinUser($c);

    return $questions ? $questions[0] : null;
  }

  public static function getPopularPager($page)
  {
    $pager = new sfPropelPager('Question', sfConfig::get('app_pager_homepage_max'));
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::INTERESTED_USERS);
    $c->addDescendingOrderByColumn(self::CREATED_AT);
    $c = self::addPermanentTagToCriteria($c);
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->setPeerMethod('doSelectJoinUser');
    $pager->init();

    return $pager;
  }
  
  public static function getFrontPagePager($page)
  {
    $pager = new sfPropelPager('Question', sfConfig::get('app_pager_homepage_max'));
    $c = new Criteria();
    $c->addAsColumn('count', "count(".InterestPeer::USER_ID.")");
    $c->addJoin(InterestPeer::QUESTION_ID, QuestionPeer::ID);
    $c->addGroupByColumn(InterestPeer::QUESTION_ID);
    $c->addDescendingOrderByColumn('count');
    $c->addDescendingOrderByColumn(self::CREATED_AT);
    $c->add(InterestPeer::CREATED_AT, time()-86400*10, Criteria::GREATER_THAN);
    $c = self::addPermanentTagToCriteria($c);
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->setPeerMethod('doSelectJoinUser');
    $pager->init();

    return $pager;
  }

  public static function getPopular($max = 10)
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::INTERESTED_USERS);
    $c = self::addPermanentTagToCriteria($c);
    $c->setLimit($max);

    return self::doSelectJoinUser($c);
  }

  public static function getRecentPager($page)
  {
    $pager = new sfPropelPager('Question', sfConfig::get('app_pager_homepage_max'));
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::CREATED_AT);
    $c = self::addPermanentTagToCriteria($c);
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->setPeerMethod('doSelectJoinUser');
    $pager->init();

    return $pager;
  }

  public function getRecent($max = 10)
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::CREATED_AT);
    $c = self::addPermanentTagToCriteria($c);
    $c->setLimit($max);

    return self::doSelectJoinUser($c);
  }

  public static function getPopularByTag($tag, $page)
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(QuestionPeer::INTERESTED_USERS);

    // tags
    $c->addJoin(self::ID, QuestionTagPeer::QUESTION_ID, Criteria::LEFT_JOIN);
    $criterion = $c->getNewCriterion(QuestionTagPeer::NORMALIZED_TAG, $tag);
    if (sfConfig::get('app_permanent_tag'))
    {
      $criterion->addAnd($c->getNewCriterion(QuestionTagPeer::NORMALIZED_TAG, sfConfig::get('app_permanent_tag')));
    }
    $c->add($criterion);
    $c->setDistinct();

    $pager = new sfPropelPager('Question', 20);
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  public static function search($phrase, $exact = false, $offset = 0, $max = 10)
  {
    $words    = array_values(myTools::stemPhrase($phrase));
    $nb_words = count($words);

    if (!$words)
    {
      return array();
    }

    $con = Propel::getConnection();
    $query = '
      SELECT DISTINCT %s, COUNT(*) AS nb, SUM(%s) AS total_weight
      FROM %s
    ';

    if (sfConfig::get('app_permanent_tag'))
    {
      $query .= sprintf('
        LEFT JOIN %s ON %s = %s
        WHERE %s = ? AND ',
        QuestionTagPeer::TABLE_NAME,
        QuestionTagPeer::QUESTION_ID,
        SearchIndexPeer::QUESTION_ID,
        QuestionTagPeer::NORMALIZED_TAG
      );
    }
    else
    {
      $query .= 'WHERE';
    }

    $query .= '
      ('.implode(' OR ', array_fill(0, $nb_words, SearchIndexPeer::WORD.' = ?')).')
      GROUP BY %s
    ';

    // AND query?
    if ($exact)
    {
      $query .= ' HAVING nb = '.$nb_words;
    }

    $query .= ' ORDER BY nb DESC, total_weight DESC';

    $query = sprintf($query,
      SearchIndexPeer::QUESTION_ID,
      SearchIndexPeer::WEIGHT,
      SearchIndexPeer::TABLE_NAME,
      SearchIndexPeer::QUESTION_ID
    );

    $stmt = $con->prepareStatement($query);
    $stmt->setOffset($offset);
    $stmt->setLimit($max);
    $placeholder_offset = 1;
    if (sfConfig::get('app_permanent_tag'))
    {
      $stmt->setString(1, sfConfig::get('app_permanent_tag'));
      $placeholder_offset = 2;
    }
    for ($i = 0; $i < $nb_words; $i++)
    {
      $stmt->setString($i + $placeholder_offset, $words[$i]);
    }
    $rs = $stmt->executeQuery(ResultSet::FETCHMODE_NUM);
    $questions = array();
    while ($rs->next())
    {
      $questions[] = self::retrieveByPK($rs->getInt(1));
    }

    return $questions;
  }

  public static function getReportedSpamPager($page)
  {
    $pager = new sfPropelPager('Question', sfConfig::get('app_pager_homepage_max'));
    $c = new Criteria();
    $c->add(self::REPORTS, 0, Criteria::GREATER_THAN);
    $c->setLimit(20);
    $c->addDescendingOrderByColumn(self::REPORTS);
    $c->addAscendingOrderByColumn(self::CREATED_AT);
    $c = self::addPermanentTagToCriteria($c);
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->setPeerMethod('doSelectJoinUser');
    $pager->init();

    return $pager;
  }

  public static function getReportCount()
  {
    $c = new Criteria();
    $c->add(self::REPORTS, 0, Criteria::GREATER_THAN);
    $c = self::addPermanentTagToCriteria($c);

    return self::doCount($c);
  }

  private static function addPermanentTagToCriteria($criteria)
  {
    if (sfConfig::get('app_permanent_tag'))
    {
      $criteria->addJoin(self::ID, QuestionTagPeer::QUESTION_ID, Criteria::LEFT_JOIN);
      $criteria->add(QuestionTagPeer::NORMALIZED_TAG, sfConfig::get('app_permanent_tag'));
      $criteria->setDistinct();
    }

    return $criteria;
  }
}

?>
