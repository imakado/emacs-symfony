<?php


	
class SearchIndexMapBuilder {

	
	const CLASS_NAME = 'lib.model.map.SearchIndexMapBuilder';	

    
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
		
		$tMap = $this->dbMap->addTable('ask_search_index');
		$tMap->setPhpName('SearchIndex');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignKey('QUESTION_ID', 'QuestionId', 'int', CreoleTypes::INTEGER, 'ask_question', 'ID', false, null);

		$tMap->addColumn('WORD', 'Word', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addColumn('WEIGHT', 'Weight', 'int', CreoleTypes::INTEGER, false);
				
    } 
} 