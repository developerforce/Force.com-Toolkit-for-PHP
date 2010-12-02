<?php
/**
 * This is PARTNER specific test. 
 *
 */
class Lib_Test_Partner_CallOptionsTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'CallOptions';
	}
	
	protected function _run()
	{	
		$YOURCLIENTID = 'YourClientId';
		$NAMESPACE = 'aNamespace';

		$callOptionsHeader = new CallOptions($YOURCLIENTID, $NAMESPACE);
		$this->_mySforceConnection->setCallOptions($callOptionsHeader);
		$loginResult = $this->_mySforceConnection->login($this->_userName, $this->_password);

		print_r($loginResult);
	}
}