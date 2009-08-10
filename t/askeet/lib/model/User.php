<?php

require_once 'lib/model/om/BaseUser.php';


/**
 * Skeleton subclass for representing a row from the 'ask_user' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class User extends BaseUser
{
  public function __toString()
  {
    if ($this->getLastName())
    {
      return ucfirst(strtolower($this->getFirstName())).' '.strtoupper($this->getLastName());
    }
    else
    {
      return $this->getNickname();
    }
  }

  public function isInterestedIn($question)
  {
    $interest = new Interest();
    $interest->setQuestion($question);
    $interest->setUserId($this->getId());
    $interest->save();
  }

  public function setPassword($password)
  {
    $salt = md5(rand(100000, 999999).$this->getNickname().$this->getEmail());
    $this->setSalt($salt);
    $this->setSha1Password(sha1($salt.$password));
  }

  public function getTagsFor($question, $max = 10)
  {
    $tags = array();

    $con = Propel::getConnection();
    $query = '
      SELECT %s AS tag, %s AS raw_tag, COUNT(%s) AS count
      FROM %s
      WHERE %s = ? AND %s = ?
      GROUP BY %s
      ORDER BY count DESC
    ';

    $query = sprintf($query,
      QuestionTagPeer::NORMALIZED_TAG,
      QuestionTagPeer::TAG,
      QuestionTagPeer::NORMALIZED_TAG,
      QuestionTagPeer::TABLE_NAME,
      QuestionTagPeer::QUESTION_ID,
      QuestionTagPeer::USER_ID,
      QuestionTagPeer::NORMALIZED_TAG
    );

    $stmt = $con->prepareStatement($query);
    $stmt->setInt(1, $question->getId());
    $stmt->setInt(2, $this->getId());
    $stmt->setLimit($max);
    $rs = $stmt->executeQuery();
    while ($rs->next())
    {
      if (sfConfig::get('app_permanent_tag') == $rs->getString('tag'))
      {
        continue;
      }

      $tags[$rs->getString('tag')] = $rs->getString('raw_tag');
    }

    return $tags;
  }

  public function removeTag($question, $tag)
  {
    $c = new Criteria();
    $c->add(QuestionTagPeer::QUESTION_ID, $question->getId());
    $c->add(QuestionTagPeer::USER_ID, $this->getId());
    $c->add(QuestionTagPeer::NORMALIZED_TAG, Tag::normalize($tag));

    QuestionTagPeer::doDelete($c);
  }

  public function getPopularTags($max = 40)
  {
    $tags = array();

    $con = Propel::getConnection();

    // get popular tags
    $query = '
      SELECT '.QuestionTagPeer::NORMALIZED_TAG.' AS tag, COUNT('.QuestionTagPeer::NORMALIZED_TAG.') AS count
      FROM '.QuestionTagPeer::TABLE_NAME.'
      WHERE '.QuestionTagPeer::USER_ID.' = ?
      GROUP BY '.QuestionTagPeer::NORMALIZED_TAG.'
      ORDER BY count DESC
    ';

    $stmt = $con->prepareStatement($query);
    $stmt->setInt(1, $this->getId());
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
}

?>