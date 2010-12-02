<?php
class Lib_Test_Partner_EmailHeaderTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'EmailHeader';
	}
	
	protected function _run()
	{
		// TODO test
		$EMAILIDFORHEADER = 'myw.php.andrey@gmail.com';

		$header = new EmailHeader(true, false, false);
		$this->_mySforceConnection->setEmailHeader($header);
		
		$createFields = array (
			'FirstName' => 'Nick',
			'LastName' => 'Tran',
			'Email' => $EMAILIDFORHEADER,
			'Company' => 'DELETE_ME Company',
			'LeadSource' => 'PHPUnit',
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