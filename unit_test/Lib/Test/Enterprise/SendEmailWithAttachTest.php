<?php
class Lib_Test_Enterprise_SendEmailWithAttachTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'SendEmailWithAttach';
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
		//  $singleEmail1->inReplyTo = "First Single Email";

		$filename = $this->_soapDir . '/earth.png';
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		$attachment = new stdclass();
		$attachment->body = $contents;
		$attachment->contentType = 'image/png';
		$attachment->fileName = $filename;
		$attachment->inline = TRUE;

		$singleEmail1->setFileAttachments(array($attachment));

		echo "***** Send Emails *****\n";
		$emailResponse = $this->_mySforceConnection->sendSingleEmail(array($singleEmail1));

		print_r($emailResponse);
	}
}