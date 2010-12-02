<?php
class Lib_Test_Enterprise_DescribeSObjectTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'DescribeSObject';
	}
	
	protected function _run()
	{
		print_r($this->_mySforceConnection->describeSObject('Task'));
	}
}