<?php
class Lib_Test_Partner_ResetPasswordTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'ResetPassword';
	}
	
	protected function _run()
	{
		// Note: uncomment and run to get userId value.	
/*		// Create new user
		$values = array(
			'Alias' => 'Johny',
			'Email' => 'test@example.com',
			'EmailEncodingKey' => 'UTF-8',
			'LanguageLocaleKey' => 'en_US',
			'LastName' => 'Smith',
			'LocaleSidKey' => 'en_US',
			'ProfileId' => '00et0000000qoV2AAI', // standart user
			'TimeZoneSidKey' => 'America/Los_Angeles',
			'Username' => 'johnysmith@example.com',
			'UserPermissionsCallCenterAutoLogin' => 0,
			'UserPermissionsMarketingUser' => 0,
			'UserPermissionsOfflineUser' => 0,
//			'UserPermissionsWirelessUser' => 0,
			'CommunityNickname' => 'johnyexamplecom'
		);
		
		$newUser = new SObject();
		$newUser->fields = $values;
		$newUser->type = 'User';
		
		$createResponse = $this->_mySforceConnection->create(array($newUser));
		
		echo "**** Creating the following:\r\n";
		print_r($createResponse);
*/
		
		$new_password=$this->_mySforceConnection->resetPassword('005t0000000uonmAAA');
		print_r($new_password);
	}
	
}