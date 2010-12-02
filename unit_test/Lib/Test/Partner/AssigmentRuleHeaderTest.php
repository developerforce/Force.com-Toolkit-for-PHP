<?php
class Lib_Test_Partner_AssigmentRuleHeaderTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'AssigmentRuleHeader';
	}
	
	protected function _run()
	{
		$header = new AssignmentRuleHeader('01Q300000005lDg', false);
		$this->_mySforceConnection->setAssignmentRuleHeader($header);
	
		$createFields = array (
			'FirstName' => 'John',
			'LastName' => 'Doe',
			'Email' => 'johndoe@salesforce.com',
			'Company' => 'Some Company',
			'LeadSource' => 'PHPUnit2',
			'City' => 'Tokyo',
			'Country' => 'Japan'
		);
		$sObject1 = new SObject();
		$sObject1->fields = $createFields;
		$sObject1->type = 'Lead';
	
		$createResponse = $this->_mySforceConnection->create(array($sObject1));
		print_r($createResponse);
	}
}