<?php
class Lib_Test_Enterprise_GetDeletedTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'GetDeleted';
	}
	
	protected function _run()
	{
		$sObject = new stdclass();
		$sObject->FirstName = 'Smith';
		$sObject->LastName = 'John';
		$sObject->Phone = '510-555-5555';
		$sObject->BirthDate = '1927-01-25';
		
		echo "**** Creating the following:\n";
		$createResponse = $this->_mySforceConnection->create(array($sObject), 'Contact');
		print_r($createResponse);
		
		$id = $createResponse->id;
		$deleteResponse = $this->_mySforceConnection->delete(array ($id));
		echo "***** Deleting record *****\n";
		print_r($deleteResponse);
		
		echo "***** Wait 60 seconds *****\n";
		sleep(60);
		
		$currentTime = mktime();
		// assume that delete occured within the last 5 mins.
		$startTime = $currentTime - (60*10);
		$endTime = $currentTime;
		
		echo "***** Get Deleted Leads *****\n";
		$getDeletedResponse = $this->_mySforceConnection->getDeleted('Contact', $startTime, $endTime);
		print_r($getDeletedResponse);
	}
}