<?php
class Lib_Test_Enterprise_EmptyRecycleBinTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'EmptyRecycleBin';
	}
	
	protected function _run()
	{
		$sObject = new stdclass();
		$sObject->Type='Electrical';
		$sObject->Status='New';
		
		$response = $this->_mySforceConnection->create(array ($sObject), 'Case');
		echo "***** Creating Case *****\n";
		print_r($response);
		$id = $response->id;
		echo "***** Deleting Case *****\n";
		print_r($this->_mySforceConnection->delete(array($id)));
		echo "***** Emptying Recycle Bin *****\n";
		print_r($this->_mySforceConnection->emptyRecycleBin(array($id)));
	}
}