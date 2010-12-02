<?php
/**
 */
class Lib_Test_Enterprise_PackageVersionHeaderTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'PackageVersionHeader';
	}
	
	protected function _run()
	{
	    $pv = new stdClass();
	    $pv->majorNumber = 2;
	    $pv->minorNumber = 1;
	    $pv->namespace = SforceEnterpriseClient::ENTERPRISE_NAMESPACE;
	    $header = new PackageVersionHeader(
	        array($pv)
	    );
	    print_r($header);
	    $this->_mySforceConnection->setPackageVersionHeader($header);
	    
	    $sObject = new stdclass();
		$sObject->FirstName = 'Smith';
		$sObject->LastName = 'John';
		$sObject->Phone = '510-555-5555';
		$sObject->BirthDate = '1927-01-25';
		
		$createResponse = $this->_mySforceConnection->create(array($sObject), 'Contact');
		echo "**** Creating the following:\r\n";
		print_r($createResponse);
		
		print "**** LastRequestHeaders:\r\n";
	    print_r($this->_mySforceConnection->getLastRequestHeaders());
	    print "**** LastRequest:\r\n";
	    print_r($this->_mySforceConnection->getLastRequest());
	}
}