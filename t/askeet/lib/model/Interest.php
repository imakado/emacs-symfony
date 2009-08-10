<?php

require_once 'lib/model/om/BaseInterest.php';


/**
 * Skeleton subclass for representing a row from the 'ask_interest' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class Interest extends BaseInterest
{
  public function save($con = null)
  {
    $con = Propel::getConnection();
    try
    {
      $con->begin();

      $ret = parent::save();

      // update interested_users in question table
      $question = $this->getQuestion();
      $interested_users = $question->getInterestedUsers();
      $question->setInterestedUsers($interested_users + 1);
      $question->save();

      $con->commit();

      return $ret;
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }
}

?>