<?php
class Lib_Test_Partner_DescribeSObjectTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'DescribeSObject';
	}
	
	protected function _run()
	{
		print_r($this->_mySforceConnection->describeSObject('Contact'));
	}
}