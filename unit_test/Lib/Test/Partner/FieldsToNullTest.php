<?php
class Lib_Test_Partner_FieldsToNullTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'FieldsToNull';
	}
	
	protected function _run()
	{
//		//Used for sample fieldsToNull from file fieldsToNull.php
//		$LEADID = '00QA0000001QSQl';
//
//		// TODO Create lead
//		$leadid = $LEADID;
//		$query = "select id, title, firstname, lastname from lead where id = '$leadid'";
//		
//		$queryResponse = $this->_mySforceConnection->query($query);
//		$queryResult = new QueryResult($queryResponse);
//		echo "***** Initial query response *****\n";
//		print_r($queryResult);
//		
//		$sObject = new SObject();
//		$sObject->fieldsToNull = array (
//			"Title", "FirstName"
//		);
//		$sObject->type = 'Lead';
//		$sObject->Id = $leadid;
//		$updateResponse = $this->_mySforceConnection->update(array ($sObject));
//		
//		echo "***** Updated object with fields to null *****\n";
//		print_r($updateResponse);
//		
//		$queryResponse = $this->_mySforceConnection->query($query);
//		$queryResult = new QueryResult($queryResponse);
//		echo "***** Final query response *****\n";
//		print_r($queryResult);
		$sObject = new SObject();
		
		$fields = array(
			'FirstName' => 'Mary',
			'LastName' => 'Jane',
			'Phone' => '510-555-5555'
		);
		
		$sObject->fields = $fields;
		$sObject->type = 'Contact';
		
		$createResponse = $this->_mySforceConnection->create(array($sObject));
		
		$retrieveResult= $this->_mySforceConnection->retrieve("FirstName, LastName, Phone", "Contact", $createResponse->id);
		echo "***** Before fieldsToNull\r\n";
		print_r($retrieveResult);
		
		$sObject = new SObject();
		$fields = array(
			'fieldsToNull' => 'Phone',
			'Id' => $createResponse->id
		);
		$sObject->fields = $fields;
		$sObject->type = 'Contact';
		
		$updateResult = $this->_mySforceConnection->update(array($sObject));
		
		$queryResult = $this->_mySforceConnection->retrieve("FirstName, LastName, Phone", "Contact", $createResponse->id);
		echo "***** After fieldsToNull\r\n";
		print_r($queryResult);
	}
}