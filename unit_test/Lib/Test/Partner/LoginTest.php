<?php
class Lib_Test_Partner_LoginTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'Login';
	}
	
	protected function _run()
	{
		echo "***** Login Info*****\n";
		print_r($this->_mylogin);
	}
}