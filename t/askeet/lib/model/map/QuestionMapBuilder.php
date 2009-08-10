<?php


	
class QuestionMapBuilder {

	
	const CLASS_NAME = 'lib.model.map.QuestionMapBuilder';	

    
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
		
		$tMap = $this->dbMap->addTable('ask_question');
		$tMap->setPhpName('Question');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addForeignKey('USER_ID', 'UserId', 'int', CreoleTypes::INTEGER, 'ask_user', 'ID', false, null);

		$tMap->addColumn('TITLE', 'Title', 'string', CreoleTypes::LONGVARCHAR, false);

		$tMap->addColumn('STRIPPED_TITLE', 'StrippedTitle', 'string', CreoleTypes::LONGVARCHAR, false);

		$tMap->addColumn('BODY', 'Body', 'string', CreoleTypes::LONGVARCHAR, false);

		$tMap->addColumn('HTML_BODY', 'HtmlBody', 'string', CreoleTypes::LONGVARCHAR, false);

		$tMap->addColumn('INTERESTED_USERS', 'InterestedUsers', 'int', CreoleTypes::INTEGER, false);

		$tMap->addColumn('REPORTS', 'Reports', 'int', CreoleTypes::INTEGER, false);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'int', CreoleTypes::TIMESTAMP, false);

		$tMap->addColumn('UPDATED_AT', 'UpdatedAt', 'int', CreoleTypes::TIMESTAMP, false);
				
    } 
} 