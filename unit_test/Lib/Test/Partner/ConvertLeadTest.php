<?php
class Lib_Test_Partner_ConvertLeadTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'ConvertLead';
	}
	
	protected function _run()
	{
		// Create lead
		$newLead = new SObject();
		
		$fields = array(
			'Company' => 'test company',
			'FirstName' => 'John',
			'LastName' => 'Smith'
		);
		
		$newLead->fields = $fields;
		$newLead->type = 'Lead';
		
		$createResponse =
			$this->_mySforceConnection->create(
				array($newLead)
		);
		
		echo "**** Created lead:\r\n";
		print_r($createResponse);

		$leadConvert = new stdClass;
		$leadConvert->convertedStatus='Closed - Converted';
		$leadConvert->doNotCreateOpportunity='false';
//		$leadConvert->leadId=$convertLEADID;
		$leadConvert->leadId=$createResponse->id;
		$leadConvert->overwriteLeadSource='true';
		$leadConvert->sendNotificationEmail='true';
		
		$leadConvertArray = array($leadConvert);
		$leadConvertResponse = $this->_mySforceConnection->convertLead($leadConvertArray);

		print_r($leadConvertResponse);
	}
}