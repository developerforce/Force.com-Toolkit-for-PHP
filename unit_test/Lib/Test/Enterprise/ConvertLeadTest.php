<?php
class Lib_Test_Enterprise_ConvertLeadTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'ConvertLead';
	}
	
	protected function _run()
	{
		// TODO Need to login on account then create the lead
		// Assign that id here and check the sample
		$newLead = new stdClass();
		$newLead->Company = 'test enterprise company';
		$newLead->FirstName = 'John';
		$newLead->LastName = 'Smith';

		$createResponse =
			$this->_mySforceConnection->create(
				array($newLead), 'Lead'
		);
		
		echo "**** Created lead:\r\n";
		print_r($createResponse);

		$leadConvert = new stdClass;
		$leadConvert->convertedStatus = 'Closed - Converted';
		$leadConvert->doNotCreateOpportunity = 'false';
		$leadConvert->leadId = $createResponse->id;
		$leadConvert->overwriteLeadSource = 'true';
		$leadConvert->sendNotificationEmail = 'true';
		
		$leadConvertArray = array($leadConvert);
		$leadConvertResponse = $this->_mySforceConnection->convertLead($leadConvertArray);
		
		print_r($leadConvertResponse);
	}
}