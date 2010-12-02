<?php
/**
 */
class Lib_Test_Partner_AllowFieldTruncationHeaderTest extends Lib_Test_TestAbstractPartner
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
	    
	    $fields = array (
		  'FirstName' => 'John',
		  'LastName' => 'Smith',
		  'Phone' => '511111111111111111111111110-555-5555111111111111111111111111111111111111111111111111',
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