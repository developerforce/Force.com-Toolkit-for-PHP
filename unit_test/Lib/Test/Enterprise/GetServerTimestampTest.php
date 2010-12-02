<?php
class Lib_Test_Enterprise_GetServerTimestampTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'GetServerTimestamp';
	}
	
	protected function _run()
	{
		echo "***** Get Server Timestamp *****\n";
		$response = $this->_mySforceConnection->getServerTimestamp();
		print_r($response);
	}
}