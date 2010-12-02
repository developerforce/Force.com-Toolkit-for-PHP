<?php
class Lib_Test_Partner_GetUpdatedTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'GetUpdated';
	}
	
	protected function _run()
	{
		$obj1 = new SObject();
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
		$createResponse = $this->_mySforceConnection->create(array($obj1));
		echo "***** Creating Lead *****\n";
		print_r($createResponse);
		
		$obj1->Id = $createResponse->id;
		$obj1->fields['LastName'] = 'Doe';
		
		$updateResponse = $this->_mySforceConnection->update(array($obj1));
		echo "***** Updating Lead *****\n";
		print_r($updateResponse);
		
		echo "***** Wait 60 seconds *****\n";
		sleep(60);
		
		$currentTime = mktime();
		// assume that update occured within the last 5 mins.
		$startTime = $currentTime-(60*10);
		$endTime = $currentTime;
		
		echo "***** Get Updated Leads from the last 5 minutes *****\n";
		$getUpdatedResponse = $this->_mySforceConnection->getUpdated('Lead', $startTime, $endTime);
		print_r($getUpdatedResponse);
	}
}