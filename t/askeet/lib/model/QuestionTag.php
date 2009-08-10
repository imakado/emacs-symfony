<?php

require_once 'lib/model/om/BaseQuestionTag.php';


/**
 * Skeleton subclass for representing a row from the 'ask_question_tag' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */	
class QuestionTag extends BaseQuestionTag
{
  public function setTag($v)
  {
    parent::setTag($v);

    $this->setNormalizedTag(Tag::normalize($v));
  }

  public function save($con = null)
  {
    $ret = parent::save($con);

    $this->getQuestion()->updateSearchIndex();

    return $ret;
  }
}

?>