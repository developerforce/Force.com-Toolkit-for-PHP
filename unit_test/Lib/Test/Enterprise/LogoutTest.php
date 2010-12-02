<?php
class Lib_Test_Enterprise_LogoutTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'Logout';
	}
	
	protected function _run()
	{
		$response = $this->_mySforceConnection->getUserInfo();
		print_r($response);

		$mylogout = $this->_mySforceConnection->logout();
		print_r($mylogout);

		$response = $this->_mySforceConnection->getUserInfo();
		print_r($response);
	}
	
	/**
	 * @param string $rs
	 * @return void
	 * @throws Lib_Exception_InvalidResponse
	 */
	protected function _validateSoapFault($rs)
	{
		if(strpos($rs, 'INVALID_SESSION_ID') === FALSE) {
			throw new Lib_Exception_InvalidResponse();
		}
	}
}