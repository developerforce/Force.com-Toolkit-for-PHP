<?php
/**
 */
class Lib_Test_Partner_PackageVersionHeaderTest extends Lib_Test_TestAbstractPartner
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
	    $pv->namespace = SforcePartnerClient::PARTNER_NAMESPACE;
	    $header = new PackageVersionHeader(
	        array($pv)
	    );
	    print_r($header);
	    $this->_mySforceConnection->setPackageVersionHeader($header);
	    
	    $fields = array (
		  'FirstName' => 'John',
		  'LastName' => 'Smith',
		  'Phone' => '510-555-5555',
		  'BirthDate' => '1950-01-01'
		);
		
		$sObject = new SObject();
		$sObject->fields = $fields;
		$sObject->type = 'Contact';
		
		$createResponse = $this->_mySforceConnection->create(array($sObject));
	    echo "**** Creating the following:\r\n";
		print_r($createResponse);
		
		print "**** LastRequestHeaders:\r\n";
	    print_r($this->_mySforceConnection->getLastRequestHeaders());
	    print "**** LastRequest:\r\n";
	    print_r($this->_mySforceConnection->getLastRequest());
	}
}