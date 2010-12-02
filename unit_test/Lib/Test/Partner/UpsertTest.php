<?php
class Lib_Test_Partner_UpsertTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'Upsert';
	}
	
	/*
	 * TODO Implement 
	 */
	protected function _run()
	{
		$createFields = array (
			'FirstName' => 'George',
			'LastName' => 'Smith',
			'Phone' => '510-555-5555',
			'BirthDate' => '1927-01-25',
		    'Email' => 'test01@test.com'
		);
		
		$sObject = new SObject();
		$sObject->fields = $createFields;
		$sObject->type = 'Contact';
		
		$createResponse = $this->_mySforceConnection->create(array($sObject));
		echo "Creating New Contact\r\n";
		print_r($createResponse);
		
		$sObject->fields['FirstName'] = 'Bill';
		$sObject->fields['LastName'] = 'Clinton';
		$sObject->fields['Email'] = 'test01@test.com';
		
		$upsertResponse = $this->_mySforceConnection->upsert('Email', array($sObject));
		echo "Upserting Contact (existing)\r\n";
		print_r($upsertResponse);
		
		$sObject->fields['FirstName'] = 'John';
		$sObject->fields['LastName'] = 'Smith';
		$sObject->fields['Email'] = 'test02@test.com';
		
		echo "Upserting Contact (new)\n";
		$upsertResponse = $this->_mySforceConnection->upsert('Email', array ($sObject));
		print_r($upsertResponse);
	}
}