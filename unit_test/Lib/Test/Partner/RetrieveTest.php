<?php
class Lib_Test_Partner_RetrieveTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'Retrieve';
	}
	
	protected function _run()
	{
		// run this to obtain id values
		$query = 'SELECT Id, Name from Profile Limit 3';
		$response = $this->_mySforceConnection->query($query);
		
		$ids = array();
		
		foreach ($response->records as $record) {
			print_r($record);
			$ids[] = $record->Id;
		}
		
//		$response = $this->_mySforceConnection->retrieve(
//			"Id, AccountNumber, Name, Website",
//			"Account",
//			array("0018000000Ll0Vm", "0018000000Lkzfz")
//		);
		
		$response = $this->_mySforceConnection->retrieve(
			'Id, Name',
			'Profile',
			$ids
		);
		
		print "**** Retrive response:\r\n";
		print_r($response);
	}
}