<?php
class Lib_Test_Enterprise_UpsertTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'Upsert';
	}
	
	/**
	 * Extract from documentation:
	 * "On standard objects, this call can use the name of any field with 
	 * the idLookup field property instead of the external ID."
	 */
	protected function _run()
	{
	    // Get list of all objects of Contact type
	    /*
	    $query = 'SELECT Id, Name, FirstName, LastName, Email from Contact';
		print_r($this->_mySforceConnection->query($query));
		/**/
	    
	    $objectType = 'Contact';
	    
		$createFields = array (
		);
		
		$sObject = new stdClass();
		$sObject->FirstName = 'George';
		$sObject->LastName = 'Smith';
		$sObject->Phone = '510-555-5555';
		$sObject->BirthDate = '1927-01-25';
		$sObject->Email = 'test@test.com';
		
		$createResponse = $this->_mySforceConnection->create(array($sObject), $objectType);
		echo "Creating New Contact\r\n";
		print_r($createResponse);
		
		$sObject->FirstName = 'Bill';
		$sObject->LastName = 'Clinton';
		
		$upsertResponse = $this->_mySforceConnection->upsert('Email', array ($sObject), $objectType);
		echo "Upserting Contact (existing)\r\n";
		print_r($upsertResponse);
		
		$sObject->FirstName = 'John';
		$sObject->LastName = 'Smith';
		$sObject->Email = 'testNew@test.com';
		
		echo "Upserting Contact (new)\n";
		$upsertResponse = $this->_mySforceConnection->upsert('Email', array ($sObject), $objectType);
		print_r($upsertResponse);
	}
}