<?php
class Lib_Test_Partner_LocaleOptionsTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'LocaleOptions';
	}
	
	protected function _run()
	{
	    $header = new LocaleOptions('en_US');
	    $this->_mySforceConnection->setLocaleOptions($header);
	    
	    print "**** DescribeSObject result: \r\n";
	    print_r(
				$this->_mySforceConnection->describeSObject('Account')
	    );
	    
	    print "**** LastRequestHeaders:\r\n";
	    print_r($this->_mySforceConnection->getLastRequestHeaders());
	    print "**** LastRequest:\r\n";
	    print_r($this->_mySforceConnection->getLastRequest());
	    
	}
}
