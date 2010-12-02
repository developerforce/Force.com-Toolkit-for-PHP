<?php
class Lib_Test_Partner_MergeTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'Merge';
	}
	
	protected function _run()
	{
		$sObject = new SObject();
		$sObject->type = 'Contact';
		$fields = array (
		    'LastName'=>'Tran',
		    'BirthDate'=> '1927-01-25',
		    'Phone' => '510-555-5555',
		);
		$sObject->fields = $fields;

		$sObject2 = new SObject();
		$sObject2->type = 'Contact';
		$fields['LastName'] = 'Smith';
		$fields['FirstName'] = 'Nick';
		$sObject2->fields = $fields;

		$sObject3 = new SObject();
		$sObject3->type = 'Contact';
		$fields['Phone'] = '555-555-5555';
		$sObject3->fields = $fields;

		echo "Create 3 contacts.\n";
		$createResponse = $this->_mySforceConnection->create(array($sObject, $sObject2, $sObject3),'Lead');

		print_r($createResponse);

		// Merge $sObject2 into $sObject
		$mergeRequest = new stdclass();
		$sObject->Id = $createResponse[0]->id;
		$mergeRequest->masterRecord = $sObject;
		$mergeRequest->comments = 'My merge comments';
		$mergeRequest->recordToMergeIds = array($createResponse[1]->id, $createResponse[2]->id);

		echo "Merge second and third contacts into the first contact.\n";
		$mergeResponse = $this->_mySforceConnection->merge($mergeRequest);

		print_r($mergeResponse);
	}
}