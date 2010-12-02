<?php
class Lib_Test_Partner_GetUserInfoTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'GetUserInfo';
	}
	
	protected function _run()
	{
		$response = $this->_mySforceConnection->getUserInfo();
		echo "***** Get User Info*****\n";
		print_r($response);
	}
}