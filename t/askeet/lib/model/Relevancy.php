<?php

require_once 'lib/model/om/BaseRelevancy.php';


/**
 * Skeleton subclass for representing a row from the 'ask_relevancy' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class Relevancy extends BaseRelevancy
{
  public function save($con = null)
  {
    $con = Propel::getConnection();
    try
    {
      $con->begin();

      $ret = parent::save();

      // update relevancy in answer table
      $answer    = $this->getAnswer();
      if ($this->getScore() == 1)
      {
        $answer->setRelevancyUp($answer->getRelevancyUp() + 1);
      }
      else
      {
        $answer->setRelevancyDown($answer->getRelevancyDown() + 1);
      }
      $answer->save();

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