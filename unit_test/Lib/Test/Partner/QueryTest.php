<?php
class Lib_Test_Partner_QueryTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'Query';
	}
	
	protected function _run()
	{
//		$query = 'SELECT Id,Name,BillingStreet,BillingCity,BillingState,Phone,Fax from Account Limit 1';
		$query = 'SELECT Id, Name from Profile Limit 10';
		
		$response = $this->_mySforceConnection->query($query);
		$queryResult = new QueryResult($response);

		foreach ($queryResult->records as $record) {
			print_r($record);
		}
	}
}