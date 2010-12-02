<?php
class Lib_Test_Enterprise_FieldsToNullTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'FieldsToNull';
	}
	
	protected function _run()
	{
		$sObject = new stdclass();
		$sObject->FirstName = 'Mary';
		$sObject->LastName = 'Jane';
		$sObject->Phone = '510-555-5555';
		
		$createResponse = $this->_mySforceConnection->create(array($sObject), 'Contact');
		
		$retrieveResult= $this->_mySforceConnection->retrieve("FirstName, LastName, Phone", "Contact", $createResponse->id);
		echo "***** Before fieldsToNull\r\n";
		print_r($retrieveResult);
		
		$sObject = new stdclass();
		$sObject->fieldsToNull = array("Phone");
		$sObject->Id = $createResponse->id;
		
		$updateResult = $this->_mySforceConnection->update(array($sObject), 'Contact');
		
		$queryResult = $this->_mySforceConnection->retrieve("FirstName, LastName, Phone", "Contact", $createResponse->id);
		echo "***** After fieldsToNull\r\n";
		print_r($queryResult);
	}
}