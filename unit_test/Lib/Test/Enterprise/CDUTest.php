<?php
/**
 * Create Delete Undelete Sample 
 *
 */
class Lib_Test_Enterprise_CDUTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'CDU';
	}
	
	protected function _run()
	{
		$sObject = new stdclass();
		$sObject->FirstName = 'Smith';
		$sObject->LastName = 'John';
		$sObject->Phone = '510-555-5555';
		$sObject->BirthDate = '1927-01-25';
		
		$sObject2 = new stdclass();
		$sObject2->FirstName = 'Mary';
		$sObject2->LastName = 'Smith';
		$sObject2->Phone = '510-486-9969';
		$sObject2->BirthDate = '1957-01-25';
		
		echo "**** Creating the following:\r\n";
		$createResponse = $this->_mySforceConnection->create(array($sObject, $sObject2), 'Contact');
		
		$ids = array();
		foreach ($createResponse as $createResult) {
		  print_r($createResult);
		  array_push($ids, $createResult->id);
		}
		echo "**** Now for Delete:\r\n";
		$deleteResult = $this->_mySforceConnection->delete($ids);
		print_r($deleteResult);
		
		echo "**** Now for UnDelete:\r\n";
		$deleteResult = $this->_mySforceConnection->undelete($ids);
		print_r($deleteResult);
	}
}