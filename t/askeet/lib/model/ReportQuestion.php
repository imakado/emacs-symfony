<?php

require_once 'lib/model/om/BaseReportQuestion.php';


/**
 * Skeleton subclass for representing a row from the 'ask_report_quesiton' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class ReportQuestion extends BaseReportQuestion
{
  public function save($con = null)
  {
    $con = Propel::getConnection();
    try
    {
      $con->begin();

      $ret = parent::save();

      // update spam_count in question table
      $question = $this->getQuestion();
      $question->setReports($question->getReports() + 1);
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