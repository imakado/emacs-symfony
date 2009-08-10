<?php

require_once 'lib/model/om/BaseReportAnswer.php';


/**
 * Skeleton subclass for representing a row from the 'ask_report_answer' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class ReportAnswer extends BaseReportAnswer
{
  public function save($con = null)
  {
    $con = Propel::getConnection();
    try
    {
      $con->begin();

      $ret = parent::save();

      // update spam_count in answer table
      $answer = $this->getAnswer();
      $answer->setReports($answer->getReports() + 1);
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