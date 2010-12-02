<?php
class Lib_Test_Partner_GetServerTimestampTest extends Lib_Test_TestAbstractPartner
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