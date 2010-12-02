<?php
class Lib_Test_Enterprise_LoginTest extends Lib_Test_TestAbstractEnterprise
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