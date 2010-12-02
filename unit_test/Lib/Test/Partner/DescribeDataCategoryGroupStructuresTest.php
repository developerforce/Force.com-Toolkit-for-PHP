<?php
/*
   Retrieves available category groups along with their data category structure for objects specified in the request.
*/

class Lib_Test_Partner_DescribeDataCategoryGroupStructuresTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'DescribeDataCategoryGroupStructure';
	}
	
	protected function _run()
	{
		/*
			Get info only about top categories
		*/
		$pairs = array();
		
		$pair = new stdClass;
		$pair->sobject = new SoapVar('Question', XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		$pair->dataCategoryGroupName = new SoapVar('Products', XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');

		$pairs[] = new SoapVar($pair, SOAP_ENC_OBJECT, 'DataCategoryGroupSobjectTypePair', SforcePartnerClient::PARTNER_NAMESPACE);
		$response = $this->_mySforceConnection->describeDataCategoryGroupStructures($pairs, true);

		echo "***** ".$this->getTestName()." Get information only about top categories *****\n";
		print_r($response);

		/*
			Get info about two categories
		*/
		$pairs = array();
		
		// First category
		$pair = new stdClass;
		$pair->sobject = new SoapVar('Question', XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		$pair->dataCategoryGroupName = new SoapVar('Products', XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');

		// Second category
		$pair = new stdClass;
		$pair->sobject = new SoapVar('Question', XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		$pair->dataCategoryGroupName = new SoapVar('Products', XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');

		$pairs[] = new SoapVar($pair, SOAP_ENC_OBJECT, 'DataCategoryGroupSobjectTypePair', SforcePartnerClient::PARTNER_NAMESPACE);
		$response = $this->_mySforceConnection->describeDataCategoryGroupStructures($pairs, false);

		echo "\n\n***** ".$this->getTestName()." Get information about two categories *****\n";
		print_r($response);
	}
}
