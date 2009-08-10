<?php


	
class QuestionTagMapBuilder {

	
	const CLASS_NAME = 'lib.model.map.QuestionTagMapBuilder';	

    
    private $dbMap;

	
    public function isBuilt()
    {
        return ($this->dbMap !== null);
    }

	
    public function getDatabaseMap()
    {
        return $this->dbMap;
    }

    
    public function doBuild()
    {
		$this->dbMap = Propel::getDatabaseMap('symfony');
		
		$tMap = $this->dbMap->addTable('ask_question_tag');
		$tMap->setPhpName('QuestionTag');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('QUESTION_ID', 'QuestionId', 'int' , CreoleTypes::INTEGER, 'ask_question', 'ID', true, null);

		$tMap->addForeignPrimaryKey('USER_ID', 'UserId', 'int' , CreoleTypes::INTEGER, 'ask_user', 'ID', true, null);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'int', CreoleTypes::TIMESTAMP, false);

		$tMap->addColumn('TAG', 'Tag', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addPrimaryKey('NORMALIZED_TAG', 'NormalizedTag', 'string', CreoleTypes::VARCHAR, true, 100);
				
    } 
} 