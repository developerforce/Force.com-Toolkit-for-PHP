<?php
class Lib_Test_Enterprise_DescribeGlobalTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'DescribeGlobal';
	}
	
	protected function _run()
	{
		print_r($this->_mySforceConnection->describeGlobal());
	}
}