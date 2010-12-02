<?php
class Lib_Test_Partner_DescribeGlobalTest extends Lib_Test_TestAbstractPartner
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