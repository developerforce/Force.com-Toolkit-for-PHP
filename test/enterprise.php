<?php
// SOAP_CLIENT_BASEDIR - folder that contains the PHP Toolkit and your WSDL
// $USERNAME - variable that contains your Salesforce.com username (must be in the form of an email)
// $PASSWORD - variable that contains your Salesforce.ocm password


define("SOAP_CLIENT_BASEDIR", "../soapclient");
$USERNAME='mrusso@salesforce.com';
$PASSWORD="sa13sf0rc3";
require_once (SOAP_CLIENT_BASEDIR.'/SforcePartnerClient.php');
require_once (SOAP_CLIENT_BASEDIR.'/SforceHeaderOptions.php');

try {
	$mySforceConnection = new SforcePartnerClient();
	$mySoapClient = $mySforceConnection->createConnection(SOAP_CLIENT_BASEDIR.'/enterprise.wsdl.xml');
	$mylogin = $mySforceConnection->login($USERNAME, $PASSWORD);

  echo "***** Get Server Timestamp *****\n";
  $response = $mySforceConnection->getServerTimestamp();
  print_r($response);
} catch (Exception $e) {
	print_r($e);
}
?>
