<?php

  // include base peer class
  require_once 'lib/model/om/BaseQuestionTagPeer.php';
  
  // include object class
  include_once 'lib/model/QuestionTag.php';


/**
 * Skeleton subclass for performing query and update operations on the 'ask_question_tag' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class QuestionTagPeer extends BaseQuestionTagPeer
{
  public static function getPopularTags($max = 5)
  {
    $tags = array();

    $con = Propel::getConnection();
    $query = '
      SELECT t1.normalized_tag AS tag,
      COUNT(t1.normalized_tag) AS count
      FROM '.QuestionTagPeer::TABLE_NAME.' AS t1';

    if (sfConfig::get('app_permanent_tag'))
    {
      $query .= '
        INNER JOIN '.QuestionTagPeer::TABLE_NAME.' AS t2 ON t1.question_id = t2.question_id
        WHERE t2.normalized_tag = ? AND t1.normalized_tag != ?
      ';
    }

    $query .= '
      GROUP BY t1.normalized_tag
      ORDER BY count DESC
    ';

    $stmt = $con->prepareStatement($query);
    if (sfConfig::get('app_permanent_tag'))
    {
      $stmt->setString(1, sfConfig::get('app_permanent_tag'));
      $stmt->setString(2, sfConfig::get('app_permanent_tag'));
    }
    $stmt->setLimit($max);
    $rs = $stmt->executeQuery();
    $max_popularity = 0;
    while ($rs->next())
    {
      if (!$max_popularity)
      {
        $max_popularity = $rs->getInt('count');
      }

      $tags[$rs->getString('tag')] = floor(($rs->getInt('count') / $max_popularity * 3) + 1);
    }

    ksort($tags);

    return $tags;
  }

  public static function getPopularTagsFor($question, $max = 10)
  {
    $tags = array();

    $con = Propel::getConnection();

    // get popular tags
    $query = '
      SELECT '.QuestionTagPeer::NORMALIZED_TAG.' AS tag, COUNT('.QuestionTagPeer::NORMALIZED_TAG.') AS count
      FROM '.QuestionTagPeer::TABLE_NAME;
    if ($question !== null)
    {
      $query .= '  WHERE '.QuestionTagPeer::QUESTION_ID.' = ?';
    }
    $query .= '
      GROUP BY '.QuestionTagPeer::NORMALIZED_TAG.'
      ORDER BY count DESC
    ';

    $stmt = $con->prepareStatement($query);
    if ($question !== null)
    {
      $stmt->setInt(1, $question->getId());
    }
    $stmt->setLimit($max);
    $rs = $stmt->executeQuery();
    $max_popularity = 0;
    while ($rs->next())
    {
      if (sfConfig::get('app_permanent_tag') == $rs->getString('tag'))
      {
        continue;
      }

      if (!$max_popularity)
      {
        $max_popularity = $rs->getInt('count');
      }

      $tags[$rs->getString('tag')] = floor(($rs->getInt('count') / $max_popularity * 3) + 1);
    }

    ksort($tags);

    return $tags;
  }

  public static function getForUserLike($user_id, $tag)
  {
    $tags = array();

    $con = Propel::getConnection();
    $query = '
      SELECT DISTINCT %s AS tag
      FROM %s
      WHERE %s = ? AND %s LIKE ?
      ORDER BY %s
    ';

    $query = sprintf($query,
      QuestionTagPeer::TAG,
      QuestionTagPeer::TABLE_NAME,
      QuestionTagPeer::USER_ID,
      QuestionTagPeer::TAG,
      QuestionTagPeer::TAG
    );

    $stmt = $con->prepareStatement($query);
    $stmt->setInt(1, $user_id);
    $stmt->setString(2, $tag.'%');
    $stmt->setLimit(10);
    $rs = $stmt->executeQuery();
    while ($rs->next())
    {
      $tags[] = $rs->getString('tag');
    }

    return $tags;
  }

  public static function getUnpopularTags()
  {
    $tags = array();

    $con = Propel::getConnection();
    $query = '
      SELECT t1.normalized_tag AS tag,
      COUNT(t1.normalized_tag) AS count
      FROM '.QuestionTagPeer::TABLE_NAME.' AS t1';

    if (sfConfig::get('app_permanent_tag'))
    {
      $query .= '
        INNER JOIN '.QuestionTagPeer::TABLE_NAME.' AS t2 ON t1.question_id = t2.question_id
        WHERE t2.normalized_tag = ? AND t1.normalized_tag != ?
      ';
    }

    $query .= '
      GROUP BY t1.normalized_tag
      HAVING count < 5
      ORDER BY count ASC
    ';

    $stmt = $con->prepareStatement($query);
    if (sfConfig::get('app_permanent_tag'))
    {
      $stmt->setString(1, sfConfig::get('app_permanent_tag'));
      $stmt->setString(2, sfConfig::get('app_permanent_tag'));
    }
    $rs = $stmt->executeQuery();
    while ($rs->next())
    {
      $tags[$rs->getString('tag')] = $rs->getInt('count');
    }

    return $tags;
  }

  public static function getByNormalizedTag($tag)
  {
    $c = new Criteria();
    $c->add(self::NORMALIZED_TAG, Tag::normalize($tag));

    return self::doSelect($c);
  }

  public static function deleteSpam($moderator, $tag, $question_id = null)
  {
    if ($question_id === null)
    {
      $tags = self::getByNormalizedTag($tag);
    }
    else
    {
      $c = new Criteria();
      $c->add(self::NORMALIZED_TAG, Tag::normalize($tag));
      $c->add(self::QUESTION_ID, $question_id);

      $tags = self::doSelect($c);
    }

    $con = Propel::getConnection();
    try
    {
      $con->begin();

      foreach ($tags as $tag)
      {
        $user = $tag->getUser();
        $user->setDeletions($user->getDeletions() + 1);
        $user->save();

        $tag->delete();
      }

      $con->commit();

      $log = 'moderator "%s" deleted tag "%s"';
      $log = sprintf($log, $moderator->getNickname(), $tag->getNormalizedTag());
      sfContext::getInstance()->getLogger()->warning($log);
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e;
    }
  }
}

?>