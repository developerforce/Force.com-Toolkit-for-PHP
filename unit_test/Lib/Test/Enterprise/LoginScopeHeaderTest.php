<?php
class Lib_Test_Enterprise_LoginScopeHeaderTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'LoginScopeHeader';
	}
	
	protected function _run()
	{
		echo "***** Get User Info*****\n";
		$response = $this->_mySforceConnection->getUserInfo();
		print_r($response);

		$this->_mySforceConnection = new SforceEnterpriseClient();
		$mySoapClient = $this->_mySforceConnection->createConnection($this->getWSDL());
//		$header = new LoginScopeHeader($response->organizationId, $this->_portalId);
		
		$header = new LoginScopeHeader(NULL, $this->_portalId);
		$this->_mySforceConnection->setLoginScopeHeader($header);
		$mylogin = $this->_mySforceConnection->login($this->_userName, $this->_password);

		print_r($mylogin);
		print_r($this->_mySforceConnection->getServerTimestamp());
	}
}