<?php
class Lib_Test_Partner_MetaDataCheckStatusTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'MetaDataCheckStatus';
	}
	
	protected function _run()
	{
		$myMetadataConnection = new SforceMetadataClient($this->_getMetaDataWSDL(), $this->_mylogin, $this->_mySforceConnection);
		
		$customObject = new SforceCustomObject();
		$customObject->fullName = 'CustomObjFromPHP__c';
		$customObject->deploymentStatus = DEPLOYMENT_STATUS_DEPLOYED;
		
		$customObject->setDescription("A description");
		$customObject->setEnableActivities(true);
		$customObject->setEnableDivisions(false);
		$customObject->setEnableHistory(true);
		$customObject->setEnableReports(true);
		$customObject->setHousehold(false);
		$customObject->setLabel("My Custom Obj from PHP");
		$customField = new SforceCustomField();
		$customField->setFullName('MyCustomFieldb__c');
		$customField->setDescription('Description of New Field');
		$customField->setLabel('My Custom Field Label');
		$customField->setType('Text');
		
		$customObject->nameField = $customField;
		
		$customObject->pluralLabel = 'My Custom Objs from PHP';
		$customObject->sharingModel = SHARING_MODEL_READWRITE;
		
		print "**** Create:\r\n";
		$createResult = $myMetadataConnection->create($customObject);
		print_r($createResult);
		
		$ids = array($createResult->result->id);
		
		print_r($myMetadataConnection->checkStatus($ids));
	}
}