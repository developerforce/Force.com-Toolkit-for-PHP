<?php
class Lib_Test_Partner_DescribeLayoutTest extends Lib_Test_TestAbstractPartner
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