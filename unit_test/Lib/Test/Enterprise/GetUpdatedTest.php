<?php
class Lib_Test_Enterprise_GetUpdatedTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'GetUpdated';
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
		echo "***** Updating record *****\n";
		$sObject->Id = $id;
		$sObject->Phone = '999-999-9999';
		$updateResponse = $this->_mySforceConnection->update(array ($sObject), "Contact");
		print_r($updateResponse);
		
		echo "***** Wait 60 seconds *****\n";
		sleep(60);
		
		$currentTime = mktime();
		// assume that update occured within the last 5 mins.
		$startTime = $currentTime-(60*10);
		$endTime = $currentTime;
		
		echo "***** Get Updated Leads *****\n";
		$getUpdateddResponse = $this->_mySforceConnection->getUpdated('Contact', $startTime, $endTime);
		print_r($getUpdateddResponse);
	}
}