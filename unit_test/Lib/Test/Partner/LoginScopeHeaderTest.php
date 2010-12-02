<?php
class Lib_Test_Partner_LoginScopeHeaderTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'LoginScopeHeader';
	}
	
	/**
	 * Note: Enable CustomerPortal in developer admin area
	 */
	protected function _run()
	{
		echo "***** Get User Info*****\n";
		$response = $this->_mySforceConnection->getUserInfo();
		print_r($response);
		
		$this->_mySforceConnection->logout();

		$this->_mySforceConnection = new SforcePartnerClient();
		$this->_mySforceConnection->createConnection($this->getWSDL());
		
		// this value should be taken from salesforce developer site.
		
//		$header = new LoginScopeHeader($response->organizationId, $this->_portalId);
		$header = new LoginScopeHeader(NULL, $this->_portalId);

		$this->_mySforceConnection->setLoginScopeHeader($header);
		$mylogin = $this->_mySforceConnection->login($this->_userName, $this->_password);
		print_r($mylogin);
		print_r($this->_mySforceConnection->getServerTimestamp());
	}
}