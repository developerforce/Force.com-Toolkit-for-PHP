<?php
class Lib_Test_Partner_GetDeletedTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'GetDeleted';
	}
	
	protected function _run()
	{
		$obj1->type = 'Lead';
		
		$fields = array (
			'Company' => 'XYZ Company',
			'FirstName' => 'John',
			'LastName' => 'Smith',
			'LeadSource' => 'Other',
			'NumberOfEmployees' => 1,
			'Status' => 'Open'
		);
		$obj1->fields = $fields;
		$createResponse = $this->_mySforceConnection->create(array ($obj1));
		
		echo "***** Creating Lead *****\n";
		print_r($createResponse);
		
		$id = $createResponse->id;
		$deleteResponse = $this->_mySforceConnection->delete(array ($id));
		echo "***** Deleting Lead *****\n";
		print_r($deleteResponse);
		
		echo "***** Wait 60 seconds *****\n";
		sleep(60);
		
		$currentTime = mktime();
		// assume that delete occured within the last 5 mins.
		$startTime = $currentTime - (60*10);
		$endTime = $currentTime;
		
		echo "***** Get Deleted Leads *****\n";
		$getDeletedResponse = $this->_mySforceConnection->getDeleted('Lead', $startTime, $endTime);
		print_r($getDeletedResponse);
	}
}