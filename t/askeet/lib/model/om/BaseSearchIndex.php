<?php


abstract class BaseSearchIndex extends BaseObject  implements Persistent {


	
	const DATABASE_NAME = 'symfony';

	
	protected static $peer;


	
	protected $question_id;


	
	protected $word;


	
	protected $weight;

	
	protected $aQuestion;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

	
	public function getQuestionId()
	{

		return $this->question_id;
	}

	
	public function getWord()
	{

		return $this->word;
	}

	
	public function getWeight()
	{

		return $this->weight;
	}

	
	public function setQuestionId($v)
	{

		if ($this->question_id !== $v) {
			$this->question_id = $v;
			$this->modifiedColumns[] = SearchIndexPeer::QUESTION_ID;
		}

		if ($this->aQuestion !== null && $this->aQuestion->getId() !== $v) {
			$this->aQuestion = null;
		}

	} 
	
	public function setWord($v)
	{

		if ($this->word !== $v) {
			$this->word = $v;
			$this->modifiedColumns[] = SearchIndexPeer::WORD;
		}

	} 
	
	public function setWeight($v)
	{

		if ($this->weight !== $v) {
			$this->weight = $v;
			$this->modifiedColumns[] = SearchIndexPeer::WEIGHT;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->question_id = $rs->getInt($startcol + 0);

			$this->word = $rs->getString($startcol + 1);

			$this->weight = $rs->getInt($startcol + 2);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 3; 
		} catch (Exception $e) {
			throw new PropelException("Error populating SearchIndex object", $e);
		}
	}

	
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(SearchIndexPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			SearchIndexPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	public function save($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(SearchIndexPeer::DATABASE_NAME);
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


						if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = SearchIndexPeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setNew(false);
				} else {
					$affectedRows += SearchIndexPeer::doUpdate($this, $con);
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


			if (($retval = SearchIndexPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = SearchIndexPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getQuestionId();
				break;
			case 1:
				return $this->getWord();
				break;
			case 2:
				return $this->getWeight();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = SearchIndexPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getQuestionId(),
			$keys[1] => $this->getWord(),
			$keys[2] => $this->getWeight(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = SearchIndexPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setQuestionId($value);
				break;
			case 1:
				$this->setWord($value);
				break;
			case 2:
				$this->setWeight($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = SearchIndexPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setQuestionId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setWord($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setWeight($arr[$keys[2]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(SearchIndexPeer::DATABASE_NAME);

		if ($this->isColumnModified(SearchIndexPeer::QUESTION_ID)) $criteria->add(SearchIndexPeer::QUESTION_ID, $this->question_id);
		if ($this->isColumnModified(SearchIndexPeer::WORD)) $criteria->add(SearchIndexPeer::WORD, $this->word);
		if ($this->isColumnModified(SearchIndexPeer::WEIGHT)) $criteria->add(SearchIndexPeer::WEIGHT, $this->weight);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(SearchIndexPeer::DATABASE_NAME);


		return $criteria;
	}

	
	public function getPrimaryKey()
	{
		return null;
	}

	
	 public function setPrimaryKey($pk)
	 {
		 	 }

	
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setQuestionId($this->question_id);

		$copyObj->setWord($this->word);

		$copyObj->setWeight($this->weight);


		$copyObj->setNew(true);

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
			self::$peer = new SearchIndexPeer();
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

} 