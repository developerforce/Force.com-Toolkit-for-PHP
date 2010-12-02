<?php
class Lib_Test_Enterprise_QueryTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'Query';
	}
	
	protected function _run()
	{
		$query = 'SELECT Id,Name from Account limit 5';
		$response = $this->_mySforceConnection->query($query);

		foreach ($response->records as $record) {
			print_r($record);
			print "<br>";
		}
	}
}