<?php
class Lib_Test_Enterprise_DescribeLayoutTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'DescribeLayout';
	}
	
	protected function _run()
	{
		print_r($this->_mySforceConnection->describeLayout('Task'));
	}
}