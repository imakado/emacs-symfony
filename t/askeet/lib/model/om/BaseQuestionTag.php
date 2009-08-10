<?php


abstract class BaseQuestionTag extends BaseObject  implements Persistent {


	
	const DATABASE_NAME = 'symfony';

	
	protected static $peer;


	
	protected $question_id;


	
	protected $user_id;


	
	protected $created_at;


	
	protected $tag;


	
	protected $normalized_tag;

	
	protected $aQuestion;

	
	protected $aUser;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

	
	public function getQuestionId()
	{

		return $this->question_id;
	}

	
	public function getUserId()
	{

		return $this->user_id;
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

	
	public function getTag()
	{

		return $this->tag;
	}

	
	public function getNormalizedTag()
	{

		return $this->normalized_tag;
	}

	
	public function setQuestionId($v)
	{

		if ($this->question_id !== $v) {
			$this->question_id = $v;
			$this->modifiedColumns[] = QuestionTagPeer::QUESTION_ID;
		}

		if ($this->aQuestion !== null && $this->aQuestion->getId() !== $v) {
			$this->aQuestion = null;
		}

	} 
	
	public function setUserId($v)
	{

		if ($this->user_id !== $v) {
			$this->user_id = $v;
			$this->modifiedColumns[] = QuestionTagPeer::USER_ID;
		}

		if ($this->aUser !== null && $this->aUser->getId() !== $v) {
			$this->aUser = null;
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
			$this->modifiedColumns[] = QuestionTagPeer::CREATED_AT;
		}

	} 
	
	public function setTag($v)
	{

		if ($this->tag !== $v) {
			$this->tag = $v;
			$this->modifiedColumns[] = QuestionTagPeer::TAG;
		}

	} 
	
	public function setNormalizedTag($v)
	{

		if ($this->normalized_tag !== $v) {
			$this->normalized_tag = $v;
			$this->modifiedColumns[] = QuestionTagPeer::NORMALIZED_TAG;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->question_id = $rs->getInt($startcol + 0);

			$this->user_id = $rs->getInt($startcol + 1);

			$this->created_at = $rs->getTimestamp($startcol + 2, null);

			$this->tag = $rs->getString($startcol + 3);

			$this->normalized_tag = $rs->getString($startcol + 4);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 5; 
		} catch (Exception $e) {
			throw new PropelException("Error populating QuestionTag object", $e);
		}
	}

	
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(QuestionTagPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			QuestionTagPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	public function save($con = null)
	{
    if ($this->isNew() && !$this->isColumnModified(QuestionTagPeer::CREATED_AT))
    {
      $this->setCreatedAt(time());
    }

		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(QuestionTagPeer::DATABASE_NAME);
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
					$pk = QuestionTagPeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setNew(false);
				} else {
					$affectedRows += QuestionTagPeer::doUpdate($this, $con);
				}
				$this->resetModified(); 			}

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


			if (($retval = QuestionTagPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = QuestionTagPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getQuestionId();
				break;
			case 1:
				return $this->getUserId();
				break;
			case 2:
				return $this->getCreatedAt();
				break;
			case 3:
				return $this->getTag();
				break;
			case 4:
				return $this->getNormalizedTag();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = QuestionTagPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getQuestionId(),
			$keys[1] => $this->getUserId(),
			$keys[2] => $this->getCreatedAt(),
			$keys[3] => $this->getTag(),
			$keys[4] => $this->getNormalizedTag(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = QuestionTagPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setQuestionId($value);
				break;
			case 1:
				$this->setUserId($value);
				break;
			case 2:
				$this->setCreatedAt($value);
				break;
			case 3:
				$this->setTag($value);
				break;
			case 4:
				$this->setNormalizedTag($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = QuestionTagPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setQuestionId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedAt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTag($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setNormalizedTag($arr[$keys[4]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(QuestionTagPeer::DATABASE_NAME);

		if ($this->isColumnModified(QuestionTagPeer::QUESTION_ID)) $criteria->add(QuestionTagPeer::QUESTION_ID, $this->question_id);
		if ($this->isColumnModified(QuestionTagPeer::USER_ID)) $criteria->add(QuestionTagPeer::USER_ID, $this->user_id);
		if ($this->isColumnModified(QuestionTagPeer::CREATED_AT)) $criteria->add(QuestionTagPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(QuestionTagPeer::TAG)) $criteria->add(QuestionTagPeer::TAG, $this->tag);
		if ($this->isColumnModified(QuestionTagPeer::NORMALIZED_TAG)) $criteria->add(QuestionTagPeer::NORMALIZED_TAG, $this->normalized_tag);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(QuestionTagPeer::DATABASE_NAME);

		$criteria->add(QuestionTagPeer::QUESTION_ID, $this->question_id);
		$criteria->add(QuestionTagPeer::USER_ID, $this->user_id);
		$criteria->add(QuestionTagPeer::NORMALIZED_TAG, $this->normalized_tag);

		return $criteria;
	}

	
	public function getPrimaryKey()
	{
		$pks = array();

		$pks[0] = $this->getQuestionId();

		$pks[1] = $this->getUserId();

		$pks[2] = $this->getNormalizedTag();

		return $pks;
	}

	
	public function setPrimaryKey($keys)
	{

		$this->setQuestionId($keys[0]);

		$this->setUserId($keys[1]);

		$this->setNormalizedTag($keys[2]);

	}

	
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setTag($this->tag);


		$copyObj->setNew(true);

		$copyObj->setQuestionId(NULL); 
		$copyObj->setUserId(NULL); 
		$copyObj->setNormalizedTag(NULL); 
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
			self::$peer = new QuestionTagPeer();
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

} 