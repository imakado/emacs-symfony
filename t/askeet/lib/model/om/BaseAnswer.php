<?php


abstract class BaseAnswer extends BaseObject  implements Persistent {


	
	const DATABASE_NAME = 'symfony';

	
	protected static $peer;


	
	protected $id;


	
	protected $question_id;


	
	protected $user_id;


	
	protected $body;


	
	protected $html_body;


	
	protected $relevancy_up = 0;


	
	protected $relevancy_down = 0;


	
	protected $reports = 0;


	
	protected $created_at;

	
	protected $aQuestion;

	
	protected $aUser;

	
	protected $collRelevancys;

	
	protected $lastRelevancyCriteria = null;

	
	protected $collReportAnswers;

	
	protected $lastReportAnswerCriteria = null;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

	
	public function getId()
	{

		return $this->id;
	}

	
	public function getQuestionId()
	{

		return $this->question_id;
	}

	
	public function getUserId()
	{

		return $this->user_id;
	}

	
	public function getBody()
	{

		return $this->body;
	}

	
	public function getHtmlBody()
	{

		return $this->html_body;
	}

	
	public function getRelevancyUp()
	{

		return $this->relevancy_up;
	}

	
	public function getRelevancyDown()
	{

		return $this->relevancy_down;
	}

	
	public function getReports()
	{

		return $this->reports;
	}

	
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{

		if ($this->created_at === null || $this->created_at === '') {
			return null;
		} elseif (!is_int($this->created_at)) {
						$ts = strtotime($this->created_at);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse value of [created_at] as date/time value: " . var_export($this->created_at, true));
			}
		} else {
			$ts = $this->created_at;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	
	public function setId($v)
	{

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AnswerPeer::ID;
		}

	} 
	
	public function setQuestionId($v)
	{

		if ($this->question_id !== $v) {
			$this->question_id = $v;
			$this->modifiedColumns[] = AnswerPeer::QUESTION_ID;
		}

		if ($this->aQuestion !== null && $this->aQuestion->getId() !== $v) {
			$this->aQuestion = null;
		}

	} 
	
	public function setUserId($v)
	{

		if ($this->user_id !== $v) {
			$this->user_id = $v;
			$this->modifiedColumns[] = AnswerPeer::USER_ID;
		}

		if ($this->aUser !== null && $this->aUser->getId() !== $v) {
			$this->aUser = null;
		}

	} 
	
	public function setBody($v)
	{

		if ($this->body !== $v) {
			$this->body = $v;
			$this->modifiedColumns[] = AnswerPeer::BODY;
		}

	} 
	
	public function setHtmlBody($v)
	{

		if ($this->html_body !== $v) {
			$this->html_body = $v;
			$this->modifiedColumns[] = AnswerPeer::HTML_BODY;
		}

	} 
	
	public function setRelevancyUp($v)
	{

		if ($this->relevancy_up !== $v || $v === 0) {
			$this->relevancy_up = $v;
			$this->modifiedColumns[] = AnswerPeer::RELEVANCY_UP;
		}

	} 
	
	public function setRelevancyDown($v)
	{

		if ($this->relevancy_down !== $v || $v === 0) {
			$this->relevancy_down = $v;
			$this->modifiedColumns[] = AnswerPeer::RELEVANCY_DOWN;
		}

	} 
	
	public function setReports($v)
	{

		if ($this->reports !== $v || $v === 0) {
			$this->reports = $v;
			$this->modifiedColumns[] = AnswerPeer::REPORTS;
		}

	} 
	
	public function setCreatedAt($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse date/time value for [created_at] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->created_at !== $ts) {
			$this->created_at = $ts;
			$this->modifiedColumns[] = AnswerPeer::CREATED_AT;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->id = $rs->getInt($startcol + 0);

			$this->question_id = $rs->getInt($startcol + 1);

			$this->user_id = $rs->getInt($startcol + 2);

			$this->body = $rs->getString($startcol + 3);

			$this->html_body = $rs->getString($startcol + 4);

			$this->relevancy_up = $rs->getInt($startcol + 5);

			$this->relevancy_down = $rs->getInt($startcol + 6);

			$this->reports = $rs->getInt($startcol + 7);

			$this->created_at = $rs->getTimestamp($startcol + 8, null);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 9; 
		} catch (Exception $e) {
			throw new PropelException("Error populating Answer object", $e);
		}
	}

	
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(AnswerPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			AnswerPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	public function save($con = null)
	{
    if ($this->isNew() && !$this->isColumnModified(AnswerPeer::CREATED_AT))
    {
      $this->setCreatedAt(time());
    }

		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(AnswerPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			$affectedRows = $this->doSave($con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	protected function doSave($con)
	{
		$affectedRows = 0; 		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;


												
			if ($this->aQuestion !== null) {
				if ($this->aQuestion->isModified()) {
					$affectedRows += $this->aQuestion->save($con);
				}
				$this->setQuestion($this->aQuestion);
			}

			if ($this->aUser !== null) {
				if ($this->aUser->isModified()) {
					$affectedRows += $this->aUser->save($con);
				}
				$this->setUser($this->aUser);
			}


						if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AnswerPeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setId($pk);  
					$this->setNew(false);
				} else {
					$affectedRows += AnswerPeer::doUpdate($this, $con);
				}
				$this->resetModified(); 			}

			if ($this->collRelevancys !== null) {
				foreach($this->collRelevancys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collReportAnswers !== null) {
				foreach($this->collReportAnswers as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;
		}
		return $affectedRows;
	} 
	
	protected $validationFailures = array();

	
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


												
			if ($this->aQuestion !== null) {
				if (!$this->aQuestion->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aQuestion->getValidationFailures());
				}
			}

			if ($this->aUser !== null) {
				if (!$this->aUser->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aUser->getValidationFailures());
				}
			}


			if (($retval = AnswerPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collRelevancys !== null) {
					foreach($this->collRelevancys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collReportAnswers !== null) {
					foreach($this->collReportAnswers as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}


			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = AnswerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getQuestionId();
				break;
			case 2:
				return $this->getUserId();
				break;
			case 3:
				return $this->getBody();
				break;
			case 4:
				return $this->getHtmlBody();
				break;
			case 5:
				return $this->getRelevancyUp();
				break;
			case 6:
				return $this->getRelevancyDown();
				break;
			case 7:
				return $this->getReports();
				break;
			case 8:
				return $this->getCreatedAt();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = AnswerPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getQuestionId(),
			$keys[2] => $this->getUserId(),
			$keys[3] => $this->getBody(),
			$keys[4] => $this->getHtmlBody(),
			$keys[5] => $this->getRelevancyUp(),
			$keys[6] => $this->getRelevancyDown(),
			$keys[7] => $this->getReports(),
			$keys[8] => $this->getCreatedAt(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = AnswerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setQuestionId($value);
				break;
			case 2:
				$this->setUserId($value);
				break;
			case 3:
				$this->setBody($value);
				break;
			case 4:
				$this->setHtmlBody($value);
				break;
			case 5:
				$this->setRelevancyUp($value);
				break;
			case 6:
				$this->setRelevancyDown($value);
				break;
			case 7:
				$this->setReports($value);
				break;
			case 8:
				$this->setCreatedAt($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = AnswerPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setQuestionId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setUserId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setBody($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setHtmlBody($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setRelevancyUp($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setRelevancyDown($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setReports($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(AnswerPeer::DATABASE_NAME);

		if ($this->isColumnModified(AnswerPeer::ID)) $criteria->add(AnswerPeer::ID, $this->id);
		if ($this->isColumnModified(AnswerPeer::QUESTION_ID)) $criteria->add(AnswerPeer::QUESTION_ID, $this->question_id);
		if ($this->isColumnModified(AnswerPeer::USER_ID)) $criteria->add(AnswerPeer::USER_ID, $this->user_id);
		if ($this->isColumnModified(AnswerPeer::BODY)) $criteria->add(AnswerPeer::BODY, $this->body);
		if ($this->isColumnModified(AnswerPeer::HTML_BODY)) $criteria->add(AnswerPeer::HTML_BODY, $this->html_body);
		if ($this->isColumnModified(AnswerPeer::RELEVANCY_UP)) $criteria->add(AnswerPeer::RELEVANCY_UP, $this->relevancy_up);
		if ($this->isColumnModified(AnswerPeer::RELEVANCY_DOWN)) $criteria->add(AnswerPeer::RELEVANCY_DOWN, $this->relevancy_down);
		if ($this->isColumnModified(AnswerPeer::REPORTS)) $criteria->add(AnswerPeer::REPORTS, $this->reports);
		if ($this->isColumnModified(AnswerPeer::CREATED_AT)) $criteria->add(AnswerPeer::CREATED_AT, $this->created_at);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(AnswerPeer::DATABASE_NAME);

		$criteria->add(AnswerPeer::ID, $this->id);

		return $criteria;
	}

	
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setQuestionId($this->question_id);

		$copyObj->setUserId($this->user_id);

		$copyObj->setBody($this->body);

		$copyObj->setHtmlBody($this->html_body);

		$copyObj->setRelevancyUp($this->relevancy_up);

		$copyObj->setRelevancyDown($this->relevancy_down);

		$copyObj->setReports($this->reports);

		$copyObj->setCreatedAt($this->created_at);


		if ($deepCopy) {
									$copyObj->setNew(false);

			foreach($this->getRelevancys() as $relObj) {
				$copyObj->addRelevancy($relObj->copy($deepCopy));
			}

			foreach($this->getReportAnswers() as $relObj) {
				$copyObj->addReportAnswer($relObj->copy($deepCopy));
			}

		} 

		$copyObj->setNew(true);

		$copyObj->setId(NULL); 
	}

	
	public function copy($deepCopy = false)
	{
				$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AnswerPeer();
		}
		return self::$peer;
	}

	
	public function setQuestion($v)
	{


		if ($v === null) {
			$this->setQuestionId(NULL);
		} else {
			$this->setQuestionId($v->getId());
		}


		$this->aQuestion = $v;
	}


	
	public function getQuestion($con = null)
	{
				include_once 'lib/model/om/BaseQuestionPeer.php';

		if ($this->aQuestion === null && ($this->question_id !== null)) {

			$this->aQuestion = QuestionPeer::retrieveByPK($this->question_id, $con);

			
		}
		return $this->aQuestion;
	}

	
	public function setUser($v)
	{


		if ($v === null) {
			$this->setUserId(NULL);
		} else {
			$this->setUserId($v->getId());
		}


		$this->aUser = $v;
	}


	
	public function getUser($con = null)
	{
				include_once 'lib/model/om/BaseUserPeer.php';

		if ($this->aUser === null && ($this->user_id !== null)) {

			$this->aUser = UserPeer::retrieveByPK($this->user_id, $con);

			
		}
		return $this->aUser;
	}

	
	public function initRelevancys()
	{
		if ($this->collRelevancys === null) {
			$this->collRelevancys = array();
		}
	}

	
	public function getRelevancys($criteria = null, $con = null)
	{
				include_once 'lib/model/om/BaseRelevancyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collRelevancys === null) {
			if ($this->isNew()) {
			   $this->collRelevancys = array();
			} else {

				$criteria->add(RelevancyPeer::ANSWER_ID, $this->getId());

				RelevancyPeer::addSelectColumns($criteria);
				$this->collRelevancys = RelevancyPeer::doSelect($criteria, $con);
			}
		} else {
						if (!$this->isNew()) {
												

				$criteria->add(RelevancyPeer::ANSWER_ID, $this->getId());

				RelevancyPeer::addSelectColumns($criteria);
				if (!isset($this->lastRelevancyCriteria) || !$this->lastRelevancyCriteria->equals($criteria)) {
					$this->collRelevancys = RelevancyPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastRelevancyCriteria = $criteria;
		return $this->collRelevancys;
	}

	
	public function countRelevancys($criteria = null, $distinct = false, $con = null)
	{
				include_once 'lib/model/om/BaseRelevancyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(RelevancyPeer::ANSWER_ID, $this->getId());

		return RelevancyPeer::doCount($criteria, $distinct, $con);
	}

	
	public function addRelevancy(Relevancy $l)
	{
		$this->collRelevancys[] = $l;
		$l->setAnswer($this);
	}


	
	public function getRelevancysJoinUser($criteria = null, $con = null)
	{
				include_once 'lib/model/om/BaseRelevancyPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collRelevancys === null) {
			if ($this->isNew()) {
				$this->collRelevancys = array();
			} else {

				$criteria->add(RelevancyPeer::ANSWER_ID, $this->getId());

				$this->collRelevancys = RelevancyPeer::doSelectJoinUser($criteria, $con);
			}
		} else {
									
			$criteria->add(RelevancyPeer::ANSWER_ID, $this->getId());

			if (!isset($this->lastRelevancyCriteria) || !$this->lastRelevancyCriteria->equals($criteria)) {
				$this->collRelevancys = RelevancyPeer::doSelectJoinUser($criteria, $con);
			}
		}
		$this->lastRelevancyCriteria = $criteria;

		return $this->collRelevancys;
	}

	
	public function initReportAnswers()
	{
		if ($this->collReportAnswers === null) {
			$this->collReportAnswers = array();
		}
	}

	
	public function getReportAnswers($criteria = null, $con = null)
	{
				include_once 'lib/model/om/BaseReportAnswerPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collReportAnswers === null) {
			if ($this->isNew()) {
			   $this->collReportAnswers = array();
			} else {

				$criteria->add(ReportAnswerPeer::ANSWER_ID, $this->getId());

				ReportAnswerPeer::addSelectColumns($criteria);
				$this->collReportAnswers = ReportAnswerPeer::doSelect($criteria, $con);
			}
		} else {
						if (!$this->isNew()) {
												

				$criteria->add(ReportAnswerPeer::ANSWER_ID, $this->getId());

				ReportAnswerPeer::addSelectColumns($criteria);
				if (!isset($this->lastReportAnswerCriteria) || !$this->lastReportAnswerCriteria->equals($criteria)) {
					$this->collReportAnswers = ReportAnswerPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastReportAnswerCriteria = $criteria;
		return $this->collReportAnswers;
	}

	
	public function countReportAnswers($criteria = null, $distinct = false, $con = null)
	{
				include_once 'lib/model/om/BaseReportAnswerPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(ReportAnswerPeer::ANSWER_ID, $this->getId());

		return ReportAnswerPeer::doCount($criteria, $distinct, $con);
	}

	
	public function addReportAnswer(ReportAnswer $l)
	{
		$this->collReportAnswers[] = $l;
		$l->setAnswer($this);
	}


	
	public function getReportAnswersJoinUser($criteria = null, $con = null)
	{
				include_once 'lib/model/om/BaseReportAnswerPeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collReportAnswers === null) {
			if ($this->isNew()) {
				$this->collReportAnswers = array();
			} else {

				$criteria->add(ReportAnswerPeer::ANSWER_ID, $this->getId());

				$this->collReportAnswers = ReportAnswerPeer::doSelectJoinUser($criteria, $con);
			}
		} else {
									
			$criteria->add(ReportAnswerPeer::ANSWER_ID, $this->getId());

			if (!isset($this->lastReportAnswerCriteria) || !$this->lastReportAnswerCriteria->equals($criteria)) {
				$this->collReportAnswers = ReportAnswerPeer::doSelectJoinUser($criteria, $con);
			}
		}
		$this->lastReportAnswerCriteria = $criteria;

		return $this->collReportAnswers;
	}

} 