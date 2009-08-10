<?php


	
class RelevancyMapBuilder {

	
	const CLASS_NAME = 'lib.model.map.RelevancyMapBuilder';	

    
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
		
		$tMap = $this->dbMap->addTable('ask_relevancy');
		$tMap->setPhpName('Relevancy');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('ANSWER_ID', 'AnswerId', 'int' , CreoleTypes::INTEGER, 'ask_answer', 'ID', true, null);

		$tMap->addForeignPrimaryKey('USER_ID', 'UserId', 'int' , CreoleTypes::INTEGER, 'ask_user', 'ID', true, null);

		$tMap->addColumn('SCORE', 'Score', 'int', CreoleTypes::INTEGER, false);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'int', CreoleTypes::TIMESTAMP, false);
				
    } 
} 