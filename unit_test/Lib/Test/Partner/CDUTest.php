<?php
/**
 * Create Delete Undelete Sample 
 *
 */
class Lib_Test_Partner_CDUTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'CDU';
	}
	
	protected function _run()
	{
		$fields = array (
		  'FirstName' => 'John',
		  'LastName' => 'Smith',
		  'Phone' => '510-555-5555',
		  'BirthDate' => '1950-01-01'
		);
		
		$sObject = new SObject();
		$sObject->fields = $fields;
		$sObject->type = 'Contact';
		
		$sObject2 = new SObject();
		$sObject2->fields = $fields;
		$sObject2->type = 'Contact';
		
		$createResponse = $this->_mySforceConnection->create(array($sObject, $sObject2));
		
		echo "**** Creating the following:\r\n";
		print_r($createResponse);
		
		$ids = array();
		foreach ($createResponse as $createResult) {
		  print_r($createResult);
		  array_push($ids, $createResult->id);
		}
		echo "**** Now for Delete:\r\n";
		$deleteResult = $this->_mySforceConnection->delete($ids);
		print_r($deleteResult);
		
		echo "**** Now for UnDelete:\r\n";
		$unDeleteResult = $this->_mySforceConnection->undelete($ids);
		print_r($unDeleteResult);
	}
}