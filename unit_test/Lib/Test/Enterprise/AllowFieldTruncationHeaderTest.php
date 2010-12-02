<?php
/**
 */
class Lib_Test_Enterprise_AllowFieldTruncationHeaderTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'AllowFieldTruncationHeader';
	}
	
	protected function _run()
	{
	    $header = new AllowFieldTruncationHeader(true);
	    print_r($header);
	    $this->_mySforceConnection->setAllowFieldTruncationHeader($header);
	    
	    $sObject = new stdclass();
		$sObject->FirstName = 'Smith';
		$sObject->LastName = 'John';
		$sObject->Phone = '510-555-55551111111111111111111111111111111111111111111111111111111111111111111';
		$sObject->BirthDate = '1927-01-25';
		
		echo "**** Creating the following:\r\n";
		print_r($this->_mySforceConnection->create(array($sObject), 'Contact'));
		
		
		print "**** LastRequestHeaders:\r\n";
	    print_r($this->_mySforceConnection->getLastRequestHeaders());
	    print "**** LastRequest:\r\n";
	    print_r($this->_mySforceConnection->getLastRequest());
	}
}