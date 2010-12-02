<?php
class Lib_Test_Partner_EmptyRecycleBinTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'EmptyRecycleBin';
	}
	
	protected function _run()
	{
		$fields = array (
		  'Type' => 'Electrical'
		);
		
		$sObject = new SObject();
		$sObject->fields = $fields;
		$sObject->type = 'Case';
		
		$response = $this->_mySforceConnection->create(array ($sObject));
		
		echo "Creating Case:\n";
		print_r($response);
		
		$id = $response->id;
		echo "Deleting Case:\n";
		print_r($this->_mySforceConnection->delete(array($id)));
		echo "Emptying Recycle Bin:\n";
		print_r($this->_mySforceConnection->emptyRecycleBin(array($id)));
	}
}