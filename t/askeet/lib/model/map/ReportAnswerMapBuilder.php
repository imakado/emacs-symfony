<?php


	
class ReportAnswerMapBuilder {

	
	const CLASS_NAME = 'lib.model.map.ReportAnswerMapBuilder';	

    
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
		
		$tMap = $this->dbMap->addTable('ask_report_answer');
		$tMap->setPhpName('ReportAnswer');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('ANSWER_ID', 'AnswerId', 'int' , CreoleTypes::INTEGER, 'ask_answer', 'ID', true, null);

		$tMap->addForeignPrimaryKey('USER_ID', 'UserId', 'int' , CreoleTypes::INTEGER, 'ask_user', 'ID', true, null);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'int', CreoleTypes::TIMESTAMP, false);
				
    } 
} 