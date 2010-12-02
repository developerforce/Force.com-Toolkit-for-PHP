<?php
class Lib_Test_Enterprise_ProcessWorkItemRequestTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'ProcessWorkItemRequest';
	}
	
	/**
	 * TODO Implement
	 */
	protected function _run()
	{
		$WORKITEM = '00TA0000001q1K5';

		$processWorkItemRequest = new ProcessWorkitemRequest();
		$processWorkItemRequest->action = "Approve";
		$processWorkItemRequest->workitemId = $WORKITEM;
		$processWorkItemRequest->comments = "Item has been approved.";

		$response = $this->_mySforceConnection->processWorkitemRequest(array ($processWorkItemRequest));

		print_r($response);
		echo $this->_mySforceConnection->getLastRequest();
		
	}
}