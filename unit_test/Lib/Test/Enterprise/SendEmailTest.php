<?php
class Lib_Test_Enterprise_SendEmailTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'SendEmail';
	}
	
	protected function _run()
	{
		$EMAILID = 'hunter@barefootsolutions.com';

		$singleEmail1 = new SingleEmailMessage();
		$singleEmail1->toAddresses = $EMAILID;
		$singleEmail1->plainTextBody = "Hello there";
		$singleEmail1->subject = "First Single Email";
		$singleEmail1->saveAsActivity = true;
		$singleEmail1->emailPriority = EMAIL_PRIORITY_LOW;

		$singleEmail2 = new SingleEmailMessage();
		$singleEmail2->toAddresses = $EMAILID;
		$singleEmail2->plainTextBody = "Hello there";
		$singleEmail2->subject = "Second Single Email";
		$singleEmail2->saveAsActivity = true;
		$singleEmail2->emailPriority = EMAIL_PRIORITY_LOW;

		echo "***** Send Emails *****\n";
		$emailResponse = $this->_mySforceConnection->sendSingleEmail(array ($singleEmail1, $singleEmail2));

		print_r($emailResponse);
	}
}