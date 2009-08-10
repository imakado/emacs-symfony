<?php


abstract class BaseRelevancy extends BaseObject  implements Persistent {


	
	const DATABASE_NAME = 'symfony';

	
	protected static $peer;


	
	protected $answer_id;


	
	protected $user_id;


	
	protected $score;


	
	protected $created_at;

	
	protected $aAnswer;

	
	protected $aUser;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

	
	public function getAnswerId()
	{

		return $this->answer_id;
	}

	
	public function getUserId()
	{

		return $this->user_id;
	}

	
	public function getScore()
	{

		return $this->score;
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

	
	public function setAnswerId($v)
	{

		if ($this->answer_id !== $v) {
			$this->answer_id = $v;
			$this->modifiedColumns[] = RelevancyPeer::ANSWER_ID;
		}

		if ($this->aAnswer !== null && $this->aAnswer->getId() !== $v) {
			$this->aAnswer = null;
		}

	} 
	
	public function setUserId($v)
	{

		if ($this->user_id !== $v) {
			$this->user_id = $v;
			$this->modifiedColumns[] = RelevancyPeer::USER_ID;
		}

		if ($this->aUser !== null && $this->aUser->getId() !== $v) {
			$this->aUser = null;
		}

	} 
	
	public function setScore($v)
	{

		if ($this->score !== $v) {
			$this->score = $v;
			$this->modifiedColumns[] = RelevancyPeer::SCORE;
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
			$this->modifiedColumns[] = RelevancyPeer::CREATED_AT;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->answer_id = $rs->getInt($startcol + 0);

			$this->user_id = $rs->getInt($startcol + 1);

			$this->score = $rs->getInt($startcol + 2);

			$this->created_at = $rs->getTimestamp($startcol + 3, null);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 4; 
		} catch (Exception $e) {
			throw new PropelException("Error populating Relevancy object", $e);
		}
	}

	
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(RelevancyPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			RelevancyPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	public function save($con = null)
	{
    if ($this->isNew() && !$this->isColumnModified(RelevancyPeer::CREATED_AT))
    {
      $this->setCreatedAt(time());
    }

		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(RelevancyPeer::DATABASE_NAME);
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


												
			if ($this->aAnswer !== null) {
				if ($this->aAnswer->isModified()) {
					$affectedRows += $this->aAnswer->save($con);
				}
				$this->setAnswer($this->aAnswer);
			}

			if ($this->aUser !== null) {
				if ($this->aUser->isModified()) {
					$affectedRows += $this->aUser->save($con);
				}
				$this->setUser($this->aUser);
			}


						if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = RelevancyPeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setNew(false);
				} else {
					$affectedRows += RelevancyPeer::doUpdate($this, $con);
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


												
			if ($this->aAnswer !== null) {
				if (!$this->aAnswer->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAnswer->getValidationFailures());
				}
			}

			if ($this->aUser !== null) {
				if (!$this->aUser->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aUser->getValidationFailures());
				}
			}


			if (($retval = RelevancyPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = RelevancyPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getAnswerId();
				break;
			case 1:
				return $this->getUserId();
				break;
			case 2:
				return $this->getScore();
				break;
			case 3:
				return $this->getCreatedAt();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = RelevancyPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getAnswerId(),
			$keys[1] => $this->getUserId(),
			$keys[2] => $this->getScore(),
			$keys[3] => $this->getCreatedAt(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = RelevancyPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setAnswerId($value);
				break;
			case 1:
				$this->setUserId($value);
				break;
			case 2:
				$this->setScore($value);
				break;
			case 3:
				$this->setCreatedAt($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = RelevancyPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setAnswerId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setScore($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCreatedAt($arr[$keys[3]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(RelevancyPeer::DATABASE_NAME);

		if ($this->isColumnModified(RelevancyPeer::ANSWER_ID)) $criteria->add(RelevancyPeer::ANSWER_ID, $this->answer_id);
		if ($this->isColumnModified(RelevancyPeer::USER_ID)) $criteria->add(RelevancyPeer::USER_ID, $this->user_id);
		if ($this->isColumnModified(RelevancyPeer::SCORE)) $criteria->add(RelevancyPeer::SCORE, $this->score);
		if ($this->isColumnModified(RelevancyPeer::CREATED_AT)) $criteria->add(RelevancyPeer::CREATED_AT, $this->created_at);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(RelevancyPeer::DATABASE_NAME);

		$criteria->add(RelevancyPeer::ANSWER_ID, $this->answer_id);
		$criteria->add(RelevancyPeer::USER_ID, $this->user_id);

		return $criteria;
	}

	
	public function getPrimaryKey()
	{
		$pks = array();

		$pks[0] = $this->getAnswerId();

		$pks[1] = $this->getUserId();

		return $pks;
	}

	
	public function setPrimaryKey($keys)
	{

		$this->setAnswerId($keys[0]);

		$this->setUserId($keys[1]);

	}

	
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setScore($this->score);

		$copyObj->setCreatedAt($this->created_at);


		$copyObj->setNew(true);

		$copyObj->setAnswerId(NULL); 
		$copyObj->setUserId(NULL); 
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
			self::$peer = new RelevancyPeer();
		}
		return self::$peer;
	}

	
	public function setAnswer($v)
	{


		if ($v === null) {
			$this->setAnswerId(NULL);
		} else {
			$this->setAnswerId($v->getId());
		}


		$this->aAnswer = $v;
	}


	
	public function getAnswer($con = null)
	{
				include_once 'lib/model/om/BaseAnswerPeer.php';

		if ($this->aAnswer === null && ($this->answer_id !== null)) {

			$this->aAnswer = AnswerPeer::retrieveByPK($this->answer_id, $con);

			
		}
		return $this->aAnswer;
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