<?php
class Lib_Test_Enterprise_GetUserInfoTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'GetUserInfo';
	}
	
	protected function _run()
	{
		echo "***** Get User Info*****\n";
		$response = $this->_mySforceConnection->getUserInfo();
		print_r($response);
	}
}