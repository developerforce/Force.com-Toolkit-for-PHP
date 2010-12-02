<?php
class Lib_Test_Enterprise_ResetPasswordTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'ResetPassword';
	}
	
	protected function _run()
	{
		// Note: uncomment and run to get userId value.	
		// Create new user
/*		$newUser = new stdClass();
		$newUser->Alias = 'Johny1';                             
		$newUser->Email = 'test1@example.com';                       
		$newUser->EmailEncodingKey = 'UTF-8';                       
		$newUser->LanguageLocaleKey = 'en_US';                      
		$newUser->LastName = 'Smith1';                      
		$newUser->LocaleSidKey = 'en_US';                           
		$newUser->ProfileId = '00et0000000qoV2AAI'; // standart user
		$newUser->TimeZoneSidKey = 'America/Los_Angeles';           
		$newUser->Username = 'johnysmith1@example.com';           
		$newUser->UserPermissionsCallCenterAutoLogin = 0;           
		$newUser->UserPermissionsMarketingUser = 0;           
		$newUser->UserPermissionsOfflineUser = 0;                 
		$newUser->CommunityNickname = 'johnyexamplecom1';             
		
		$createResponse = $this->_mySforceConnection->create(array($newUser), 'User');
		
		echo "**** Creating the following:\r\n";
		print_r($createResponse);*/
		
		$new_password=$this->_mySforceConnection->resetPassword('005t0000000uonmAAA');
		print_r($new_password);
	}
}