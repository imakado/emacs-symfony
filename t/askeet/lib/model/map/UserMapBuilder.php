<?php


	
class UserMapBuilder {

	
	const CLASS_NAME = 'lib.model.map.UserMapBuilder';	

    
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
		
		$tMap = $this->dbMap->addTable('ask_user');
		$tMap->setPhpName('User');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('NICKNAME', 'Nickname', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addColumn('FIRST_NAME', 'FirstName', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addColumn('LAST_NAME', 'LastName', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addColumn('EMAIL', 'Email', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addColumn('SHA1_PASSWORD', 'Sha1Password', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addColumn('SALT', 'Salt', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addColumn('HAS_PAYPAL', 'HasPaypal', 'boolean', CreoleTypes::BOOLEAN, false);

		$tMap->addColumn('WANT_TO_BE_MODERATOR', 'WantToBeModerator', 'boolean', CreoleTypes::BOOLEAN, false);

		$tMap->addColumn('IS_MODERATOR', 'IsModerator', 'boolean', CreoleTypes::BOOLEAN, false);

		$tMap->addColumn('IS_ADMINISTRATOR', 'IsAdministrator', 'boolean', CreoleTypes::BOOLEAN, false);

		$tMap->addColumn('DELETIONS', 'Deletions', 'int', CreoleTypes::INTEGER, false);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'int', CreoleTypes::TIMESTAMP, false);
				
    } 
} 