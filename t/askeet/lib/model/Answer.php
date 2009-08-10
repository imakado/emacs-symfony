<?php

require_once 'lib/model/om/BaseAnswer.php';


/**
 * Skeleton subclass for representing a row from the 'ask_answer' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class Answer extends BaseAnswer
{
  public function getRelevancyUpPercent()
  {
    $total = $this->getRelevancyUp() + $this->getRelevancyDown();

    return $total ? sprintf('%.0f', $this->getRelevancyUp() * 100 / $total) : 0;
  }

  public function getRelevancyDownPercent()
  {
    $total = $this->getRelevancyUp() + $this->getRelevancyDown();

    return $total ? sprintf('%.0f', $this->getRelevancyDown() * 100 / $total) : 0;
  }

  public function setBody($v)
  {
    parent::setBody($v);

    require_once('markdown.php');

    // strip all HTML tags
    $v = htmlentities($v, ENT_QUOTES, 'UTF-8');

    $this->setHtmlBody(markdown($v));
  }

  public function deleteReports()
  {
    $reports = $this->getReportAnswers();
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
      if ($user->getNickname() != 'anonymous')
      {
        $user->setDeletions($user->getDeletions() + 1);
        $user->save();
      }

      $this->delete();

      $con->commit();

      $log = 'moderator "%s" deleted answer "%s" for question "%s" (%s)';
      $log = sprintf($log, $moderator->getNickname(), $this->getId(), $this->getQuestion()->getTitle(), substr($this->getBody(), 0, 50));
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