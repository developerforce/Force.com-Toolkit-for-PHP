<?php
class Lib_Test_Partner_MetaDataDeleteTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'MetaDataDelete';
	}
	
	protected function _run()
	{
		$myMetadataConnection = new SforceMetadataClient($this->_getMetaDataWSDL(), $this->_mylogin, $this->_mySforceConnection);

		$customObject = new SforceCustomObject();
		$customObject->fullName = 'MyCustomObjFromPHP__c';

		print_r($myMetadataConnection->delete($customObject));
	}
}