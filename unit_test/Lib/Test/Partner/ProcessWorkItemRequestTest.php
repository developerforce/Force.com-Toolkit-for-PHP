<?php
class Lib_Test_Partner_ProcessWorkItemRequestTest extends Lib_Test_TestAbstractPartner
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
		// ProcessInstanceWorkitem
		$WORKITEM = '00TA0000001q1K5';
		
		$processWorkItem1 = new ProcessWorkitemRequest();
		$processWorkItem1->action = 'Approve';
		$processWorkItem1->workitemId = $WORKITEM;
		$processWorkItem1->comments = "Request approved";

		$processWorkItemRequestResponse =
			$this->_mySforceConnection->processWorkitemRequest(array($processWorkItem1));

		print_r($processWorkItemRequestResponse);
	}
}