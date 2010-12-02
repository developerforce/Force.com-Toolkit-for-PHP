<?php
class Lib_Test_Partner_ProcessSubmitRequestTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'ProcessSubmitRequest';
	}
	
	/**
	 * TODO Implement
	 */
	protected function _run()
	{
		// Create Contact
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
		
		$userInfoResponse = $this->_mySforceConnection->getUserInfo();
		echo "***** Get User Info*****\n";
		print_r($userInfoResponse);
		
		$processSubmitRequest1 = new ProcessSubmitRequest();
		$processSubmitRequest1->objectId = $createResponse['0']->id;
		$processSubmitRequest1->comments = "Please approve this request.";
		$processSubmitRequest1->nextApproverIds = array($userInfoResponse->userId);

		$processSubmitRequest2 = new ProcessSubmitRequest();
		$processSubmitRequest2->objectId = $createResponse['1']->id;
		$processSubmitRequest2->comments = "Please approve this request.";
		$processSubmitRequest2->nextApproverIds = array($userInfoResponse->userId);

		$processSubmitRequestResponse = $this->_mySforceConnection->processSubmitRequest(
				array($processSubmitRequest1, $processSubmitRequest2)
		);
		print_r($processSubmitRequestResponse);
	}
}