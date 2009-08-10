<?php


	
class AnswerMapBuilder {

	
	const CLASS_NAME = 'lib.model.map.AnswerMapBuilder';	

    
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
		
		$tMap = $this->dbMap->addTable('ask_answer');
		$tMap->setPhpName('Answer');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addForeignKey('QUESTION_ID', 'QuestionId', 'int', CreoleTypes::INTEGER, 'ask_question', 'ID', false, null);

		$tMap->addForeignKey('USER_ID', 'UserId', 'int', CreoleTypes::INTEGER, 'ask_user', 'ID', false, null);

		$tMap->addColumn('BODY', 'Body', 'string', CreoleTypes::LONGVARCHAR, false);

		$tMap->addColumn('HTML_BODY', 'HtmlBody', 'string', CreoleTypes::LONGVARCHAR, false);

		$tMap->addColumn('RELEVANCY_UP', 'RelevancyUp', 'int', CreoleTypes::INTEGER, false);

		$tMap->addColumn('RELEVANCY_DOWN', 'RelevancyDown', 'int', CreoleTypes::INTEGER, false);

		$tMap->addColumn('REPORTS', 'Reports', 'int', CreoleTypes::INTEGER, false);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'int', CreoleTypes::TIMESTAMP, false);
				
    } 
} 