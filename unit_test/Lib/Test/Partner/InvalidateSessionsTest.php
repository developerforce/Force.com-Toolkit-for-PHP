<?php
class Lib_Test_Partner_InvalidateSessionsTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'InvalidateSessions';
	}
	
	protected function _run()
	{
		$response = $this->_mySforceConnection->getUserInfo();
		print "**** User info:\r\n";
		print_r($response);
		
		$mylogout = $this->_mySforceConnection->invalidateSessions();
		print "**** Invalidate sessions:\r\n";
		print_r($mylogout);
		
		$response = $this->_mySforceConnection->getUserInfo();
		print_r($response);
		print "**** User info:\r\n";
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