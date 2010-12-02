<?php
class Lib_Test_Enterprise_MergeTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'Merge';
	}
	
	protected function _run()
	{
		$sObject = new stdclass();
		$sObject->LastName = 'Tran';
		$sObject->BirthDate = '1927-01-25';
		$sObject->Phone = '510-555-5555';

		$sObject2 = new stdclass();
		$sObject2->LastName = 'Tran';
		$sObject2->BirthDate = '1957-01-25';
		$sObject2->Phone = '510-486-9969';

		$sObject3 = new stdclass();
		$sObject3->LastName = 'Tran';
		$sObject3->BirthDate = '1957-01-25';

		echo "Create 3 contacts.\n";

		$createResponse = $this->_mySforceConnection->create(array($sObject, $sObject2, $sObject3), 'Contact');

		print_r($createResponse);

		// Merge $sObject2 into $sObject
		$mergeRequest = new stdclass();
		$sObject->Id = $createResponse[0]->id;
		$mergeRequest->masterRecord = $sObject;
		$mergeRequest->comments = 'My merge comments';

		echo "Merge second and third contacts into the first contact.\n";

		$mergeRequest->recordToMergeIds = array($createResponse[1]->id, $createResponse[2]->id);

		$mergeResponse = $this->_mySforceConnection->merge($mergeRequest, 'Contact');

		print_r($mergeResponse);
	}
}