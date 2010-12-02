<pre>
<?php
// SOAP_CLIENT_BASEDIR - folder that contains the PHP Toolkit and your WSDL
// $USERNAME - variable that contains your Salesforce.com username (must be in the form of an email)
// $PASSWORD - variable that contains your Salesforce.ocm password


define("SOAP_CLIENT_BASEDIR", "../soapclient");
$USERNAME='trannicholas@yahoo.com';
$PASSWORD='changeme';
require_once (SOAP_CLIENT_BASEDIR.'/SforcePartnerClient.php');
require_once (SOAP_CLIENT_BASEDIR.'/SforceHeaderOptions.php');



$query="Select o.OrganizationType, o.Id From Organization o where id = '00D300000007gjdEAA'";


try {
	$mySforceConnection = new SforcePartnerClient();
	$mySoapClient = $mySforceConnection->createConnection(SOAP_CLIENT_BASEDIR.'/partner.wsdl.xml');
	$mylogin = $mySforceConnection->login($USERNAME, $PASSWORD);
print_r($mySforceConnection->getUserInfo());
	//print_r($mylogin->userInfo);
  echo "***** Get Server Timestamp *****\n";
  //$response = $mySforceConnection->getServerTimestamp();
//	print_r($response);
 //print_r($mySforceConnection->describeSObject('User'));  
  //$result = $mySforceConnection->query($query);
  //print_r($result);
} catch (Exception $e) {
	print_r($e);
}
?>
</pre>