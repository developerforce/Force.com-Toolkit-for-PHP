<?php
/*
 * Copyright (c) 2007, salesforce.com, inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided
 * that the following conditions are met:
 *
 *    Redistributions of source code must retain the above copyright notice, this list of conditions and the
 *    following disclaimer.
 *
 *    Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *    the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *    Neither the name of salesforce.com, inc. nor the names of its contributors may be used to endorse or
 *    promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
 * PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */
require_once ('SforceEmail.php');
require_once ('SforceProcessRequest.php');
require_once ('ProxySettings.php');
require_once ('SforceHeaderOptions.php');

/**
 * This file contains one class.
 * @package SalesforceSoapClient
 */
/**
 * SalesforceSoapClient
 * @package SalesforceSoapClient
 */
class SforceBaseClient {
	protected $sforce;
	protected $sessionId;
	protected $location;
	protected $version = '27.0';

	protected $namespace;

	// Header Options
	protected $callOptions;
	protected $assignmentRuleHeader;
	protected $emailHeader;
	protected $loginScopeHeader;
	protected $mruHeader;
	protected $queryHeader;
	protected $userTerritoryDeleteHeader;
	protected $sessionHeader;
	
	// new headers
	protected $allowFieldTruncationHeader;
	protected $localeOptions;
	protected $packageVersionHeader;
	
  protected function getSoapClient($wsdl, $options) {
		return new SoapClient($wsdl, $options);      
  }
	
	public function getNamespace() {
		return $this->namespace;
	}


	// clientId specifies which application or toolkit is accessing the
	// salesforce.com API. For applications that are certified salesforce.com
	// solutions, replace this with the value provided by salesforce.com.
	// Otherwise, leave this value as 'phpClient/1.0'.
	protected $client_id;

	public function printDebugInfo() {
		echo "PHP Toolkit Version: $this->version\r\n";
		echo 'Current PHP version: ' . phpversion();
		echo "\r\n";
		echo 'SOAP enabled: ';
		if (extension_loaded('soap')) {
			echo 'True';
		} else {
			echo 'False';
		}
		echo "\r\n";
		echo 'OpenSSL enabled: ';
		if (extension_loaded('openssl')) {
			echo 'True';
		} else {
			echo 'False';
		}
	}
	
	/**
	 * Connect method to www.salesforce.com
	 *
	 * @param string $wsdl   Salesforce.com Partner WSDL
   * @param object $proxy  (optional) proxy settings with properties host, port,
   *                       login and password
   * @param array $soap_options (optional) Additional options to send to the
   *                       SoapClient constructor. @see
   *                       http://php.net/manual/en/soapclient.soapclient.php
	 */
	public function createConnection($wsdl, $proxy=null, $soap_options=array()) {
		$phpversion = substr(phpversion(), 0, strpos(phpversion(), '-'));
		
		$soapClientArray = array_merge(array (
			'user_agent' => 'salesforce-toolkit-php/'.$this->version,
			'encoding' => 'utf-8',
			'trace' => 1,
			'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
			'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP
		), $soap_options);

		// We don't need to parse out any subversion suffix - e.g. "-01" since
		// PHP type conversion will ignore it
		if (phpversion() < 5.2) {
			die("PHP versions older than 5.2 are no longer supported. Please upgrade!");
		}

		if ($proxy != null) {
            $proxySettings = array();
            $proxySettings['proxy_host'] = $proxy->host;
            $proxySettings['proxy_port'] = $proxy->port; // Use an integer, not a string
            $proxySettings['proxy_login'] = $proxy->login; 
            $proxySettings['proxy_password'] = $proxy->password;
            $soapClientArray = array_merge($soapClientArray, $proxySettings);
		}

  	$this->sforce = $this->getSoapClient($wsdl, $soapClientArray);

		return $this->sforce;
	}

	public function setCallOptions($header) {
		if ($header != NULL) {
			$this->callOptions = new SoapHeader($this->namespace, 'CallOptions', array (
		  'client' => $header->client,
		  'defaultNamespace' => $header->defaultNamespace
			));
		} else {
			$this->callOptions = NULL;
		}
	}

	/**
	 * Login to Salesforce.com and starts a client session.
	 *
	 * @param string $username   Username
	 * @param string $password   Password
	 *
	 * @return LoginResult
	 */
	public function login($username, $password) {
		$this->sforce->__setSoapHeaders(NULL);
		if ($this->callOptions != NULL) {
			$this->sforce->__setSoapHeaders(array($this->callOptions));
		}
		if ($this->loginScopeHeader != NULL) {
			$this->sforce->__setSoapHeaders(array($this->loginScopeHeader));
		}
		$result = $this->sforce->login(array (
		 'username' => $username,
		 'password' => $password
		));
		$result = $result->result;
		$this->_setLoginHeader($result);
		
		return $result;
	}

	/**
	 * log outs from the salseforce system`
	 *
	 * @return LogoutResult
	 */
	public function logout() {
        $this->setHeaders("logout");
		$arg = new stdClass();
		return $this->sforce->logout();
	}
 
	/**
	 *invalidate Sessions from the salseforce system`
	 *
	 * @return invalidateSessionsResult
	 */
	public function invalidateSessions() {
        $this->setHeaders("invalidateSessions");
		$arg = new stdClass();
        $this->logout();
		return $this->sforce->invalidateSessions();
	} 
 
	/**
	 * Specifies the session ID returned from the login server after a successful
	 * login.
	 */
	protected function _setLoginHeader($loginResult) {
		$this->sessionId = $loginResult->sessionId;
		$this->setSessionHeader($this->sessionId);
		$serverURL = $loginResult->serverUrl;
		$this->setEndPoint($serverURL);
	}

	/**
	 * Set the endpoint.
	 *
	 * @param string $location   Location
	 */
	public function setEndpoint($location) {
		$this->location = $location;
		$this->sforce->__setLocation($location);
	}

	private function setHeaders($call=NULL) {
		$this->sforce->__setSoapHeaders(NULL);
		
		$header_array = array (
			$this->sessionHeader
		);

		$header = $this->callOptions;
		if ($header != NULL) {
			array_push($header_array, $header);
		}

		if ($call == "create" ||
		$call == "merge" ||
		$call == "update" ||
		$call == "upsert"
		) {
			$header = $this->assignmentRuleHeader;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}

		if ($call == "login") {
			$header = $this->loginScopeHeader;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}

		if ($call == "create" ||
		$call == "resetPassword" ||
		$call == "update" ||
		$call == "upsert"
		) {
			$header = $this->emailHeader;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}

		if ($call == "create" ||
		$call == "merge" ||
		$call == "query" ||
		$call == "retrieve" ||
		$call == "update" ||
		$call == "upsert"
		) {
			$header = $this->mruHeader;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}

		if ($call == "delete") {
			$header = $this->userTerritoryDeleteHeader;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}

		if ($call == "query" ||
		$call == "queryMore" ||
		$call == "retrieve") {
			$header = $this->queryHeader;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}
		
		// try to add allowFieldTruncationHeader
		$allowFieldTruncationHeaderCalls = array(
			'convertLead', 'create', 'merge',
			'process', 'undelete', 'update',
			'upsert',
		);
		if (in_array($call, $allowFieldTruncationHeaderCalls)) {
			$header = $this->allowFieldTruncationHeader;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}
		
		// try to add localeOptions
		if ($call == 'describeSObject' || $call == 'describeSObjects') {
			$header = $this->localeOptions;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}
		
		// try to add PackageVersionHeader
		$packageVersionHeaderCalls = array(
			'convertLead', 'create', 'delete', 'describeGlobal',
			'describeLayout', 'describeSObject', 'describeSObjects',
			'describeSoftphoneLayout', 'describeTabs', 'merge',
			'process', 'query', 'retrieve', 'search', 'undelete',
			'update', 'upsert',
		);
		if(in_array($call, $packageVersionHeaderCalls)) {
			$header = $this->packageVersionHeader;
			if ($header != NULL) {
				array_push($header_array, $header);
			}
		}
		
		
		$this->sforce->__setSoapHeaders($header_array);
	}

	public function setAssignmentRuleHeader($header) {
		if ($header != NULL) {
			$this->assignmentRuleHeader = new SoapHeader($this->namespace, 'AssignmentRuleHeader', array (
			 'assignmentRuleId' => $header->assignmentRuleId,
			 'useDefaultRule' => $header->useDefaultRuleFlag
			));
		} else {
			$this->assignmentRuleHeader = NULL;
		}
	}

	public function setEmailHeader($header) {
		if ($header != NULL) {
			$this->emailHeader = new SoapHeader($this->namespace, 'EmailHeader', array (
			 'triggerAutoResponseEmail' => $header->triggerAutoResponseEmail,
			 'triggerOtherEmail' => $header->triggerOtherEmail,
			 'triggerUserEmail' => $header->triggerUserEmail
			));
		} else {
			$this->emailHeader = NULL;
		}
	}

	public function setLoginScopeHeader($header) {
		if ($header != NULL) {
			$this->loginScopeHeader = new SoapHeader($this->namespace, 'LoginScopeHeader', array (
		'organizationId' => $header->organizationId,
		'portalId' => $header->portalId
			));
		} else {
			$this->loginScopeHeader = NULL;
		}
		//$this->setHeaders('login');
	}

	public function setMruHeader($header) {
		if ($header != NULL) {
			$this->mruHeader = new SoapHeader($this->namespace, 'MruHeader', array (
			 'updateMru' => $header->updateMruFlag
			));
		} else {
			$this->mruHeader = NULL;
		}
	}

	public function setSessionHeader($id) {
		if ($id != NULL) {
			$this->sessionHeader = new SoapHeader($this->namespace, 'SessionHeader', array (
			 'sessionId' => $id
			));
			$this->sessionId = $id;
		} else {
			$this->sessionHeader = NULL;
			$this->sessionId = NULL;
		}
	}

	public function setUserTerritoryDeleteHeader($header) {
		if ($header != NULL) {
			$this->userTerritoryDeleteHeader = new SoapHeader($this->namespace, 'UserTerritoryDeleteHeader  ', array (
			 'transferToUserId' => $header->transferToUserId
			));
		} else {
			$this->userTerritoryDeleteHeader = NULL;
		}
	}

	public function setQueryOptions($header) {
		if ($header != NULL) {
			$this->queryHeader = new SoapHeader($this->namespace, 'QueryOptions', array (
			 'batchSize' => $header->batchSize
			));
		} else {
			$this->queryHeader = NULL;
		}
	}
	
	public function setAllowFieldTruncationHeader($header) {
		if ($header != NULL) {
			$this->allowFieldTruncationHeader = new SoapHeader($this->namespace, 'AllowFieldTruncationHeader', array (
					'allowFieldTruncation' => $header->allowFieldTruncation
				)
			);
		} else {
			$this->allowFieldTruncationHeader = NULL;
		}
	}
	
	public function setLocaleOptions($header) {
		if ($header != NULL) {
			$this->localeOptions = new SoapHeader($this->namespace, 'LocaleOptions',
				array (
					'language' => $header->language
				)
			);
		} else {
			$this->localeOptions = NULL;
		}
	}
	
	/**
	 * @param $header
	 */
	public function setPackageVersionHeader($header) {
		if ($header != NULL) {
			$headerData = array('packageVersions' => array());
			
			foreach ($header->packageVersions as $key => $hdrElem) {
				$headerData['packageVersions'][] = array(
					'majorNumber' => $hdrElem->majorNumber,
					'minorNumber' => $hdrElem->minorNumber,
					'namespace' => $hdrElem->namespace,
				);
			}
			
			$this->packageVersionHeader = new SoapHeader($this->namespace,
				'PackageVersionHeader',
				$headerData
			);
		} else {
			$this->packageVersionHeader = NULL;
		}
	}

	public function getSessionId() {
		return $this->sessionId;
	}

	public function getLocation() {
		return $this->location;
	}

	public function getConnection() {
		return $this->sforce;
	}

	public function getFunctions() {
		return $this->sforce->__getFunctions();
	}

	public function getTypes() {
		return $this->sforce->__getTypes();
	}

	public function getLastRequest() {
		return $this->sforce->__getLastRequest();
	}

	public function getLastRequestHeaders() {
		return $this->sforce->__getLastRequestHeaders();
	}

	public function getLastResponse() {
		return $this->sforce->__getLastResponse();
	}

	public function getLastResponseHeaders() {
		return $this->sforce->__getLastResponseHeaders();
	}

	protected function _convertToAny($fields) {
		$anyString = '';
		foreach ($fields as $key => $value) {
			$anyString = $anyString . '<' . $key . '>' . $value . '</' . $key . '>';
		}
		return $anyString;
	}

	protected function _create($arg) {
		$this->setHeaders("create");
		return $this->sforce->create($arg)->result;
	}

	protected function _merge($arg) {
		$this->setHeaders("merge");
		return $this->sforce->merge($arg)->result;
	}

	protected function _process($arg) {
		$this->setHeaders();
		return $this->sforce->process($arg)->result;
	}

	protected function _update($arg) {
		$this->setHeaders("update");
		return $this->sforce->update($arg)->result;
	}

	protected function _upsert($arg) {
		$this->setHeaders("upsert");
		return $this->sforce->upsert($arg)->result;
	}

  public function sendSingleEmail($request) {
	if (is_array($request)) {
	  $messages = array();
	  foreach ($request as $r) {
		$email = new SoapVar($r, SOAP_ENC_OBJECT, 'SingleEmailMessage', $this->namespace);
		array_push($messages, $email);
	  }
	  $arg = new stdClass();
	  $arg->messages = $messages;
	  return $this->_sendEmail($arg);
	} else {
	  $backtrace = debug_backtrace();
	  die('Please pass in array to this function:  '.$backtrace[0]['function']);
	}
  }

  public function sendMassEmail($request) {
	if (is_array($request)) {
	  $messages = array();
	  foreach ($request as $r) {
		$email = new SoapVar($r, SOAP_ENC_OBJECT, 'MassEmailMessage', $this->namespace);
		array_push($messages, $email);
	  }
	  $arg = new stdClass();
	  $arg->messages = $messages;
	  return $this->_sendEmail($arg);
	} else {
	  $backtrace = debug_backtrace();
	  die('Please pass in array to this function:  '.$backtrace[0]['function']);
	}
  } 
	
	protected function _sendEmail($arg) {
		$this->setHeaders();
		return $this->sforce->sendEmail($arg)->result;
	}

	/**
	 * Converts a Lead into an Account, Contact, or (optionally) an Opportunity.
	 *
	 * @param array $leadConverts    Array of LeadConvert
	 *
	 * @return LeadConvertResult
	 */
	public function convertLead($leadConverts) {
		$this->setHeaders("convertLead");
		$arg = new stdClass();
		$arg->leadConverts = $leadConverts;
		return $this->sforce->convertLead($arg);
	}

	/**
	 * Deletes one or more new individual objects to your organization's data.
	 *
	 * @param array $ids    Array of fields
	 * @return DeleteResult
	 */
	public function delete($ids) {
		$this->setHeaders("delete");
		if(count($ids) > 200) {
			$result = array();
			$chunked_ids = array_chunk($ids, 200);
			foreach($chunked_ids as $cids) {
				$arg = new stdClass;
				$arg->ids = $cids;
				$result = array_merge($result, $this->sforce->delete($arg)->result);
			}
		} else {
			$arg = new stdClass;
			$arg->ids = $ids;
			$result = $this->sforce->delete($arg)->result;
		}
		return $result;
	}

	/**
	 * Deletes one or more new individual objects to your organization's data.
	 *
	 * @param array $ids    Array of fields
	 * @return DeleteResult
	 */
	public function undelete($ids) {
		$this->setHeaders("undelete");
		$arg = new stdClass();
		$arg->ids = $ids;
		return $this->sforce->undelete($arg)->result;
	}

	/**
	 * Deletes one or more new individual objects to your organization's data.
	 *
	 * @param array $ids    Array of fields
	 * @return DeleteResult
	 */
	public function emptyRecycleBin($ids) {
		$this->setHeaders();
		$arg = new stdClass();
		$arg->ids = $ids;
		return $this->sforce->emptyRecycleBin($arg)->result;
	}

	/**
	 * Process Submit Request for Approval
	 *
	 * @param array $processRequestArray
	 * @return ProcessResult
	 */
	public function processSubmitRequest($processRequestArray) {
		if (is_array($processRequestArray)) {
			foreach ($processRequestArray as &$process) {
				$process = new SoapVar($process, SOAP_ENC_OBJECT, 'ProcessSubmitRequest', $this->namespace);
			}
			$arg = new stdClass();
			$arg->actions = $processRequestArray;
			return $this->_process($arg);
		} else {
			$backtrace = debug_backtrace();
			die('Please pass in array to this function:  '.$backtrace[0]['function']);
		}
	}

	/**
	 * Process Work Item Request for Approval
	 *
	 * @param array $processRequestArray
	 * @return ProcessResult
	 */
	public function processWorkitemRequest($processRequestArray) {
		if (is_array($processRequestArray)) {
			foreach ($processRequestArray as &$process) {
				$process = new SoapVar($process, SOAP_ENC_OBJECT, 'ProcessWorkitemRequest', $this->namespace);
			}
			$arg = new stdClass();
			$arg->actions = $processRequestArray;
			return $this->_process($arg);
		} else {
			$backtrace = debug_backtrace();
			die('Please pass in array to this function:  '.$backtrace[0]['function']);
		}
	}

	/**
	 * Retrieves a list of available objects for your organization's data.
	 *
	 * @return DescribeGlobalResult
	 */
	public function describeGlobal() {
		$this->setHeaders("describeGlobal");
		return $this->sforce->describeGlobal()->result;
	}

	/**
	 * Use describeLayout to retrieve information about the layout (presentation
	 * of data to users) for a given object type. The describeLayout call returns
	 * metadata about a given page layout, including layouts for edit and
	 * display-only views and record type mappings. Note that field-level security
	 * and layout editability affects which fields appear in a layout.
	 *
	 * @param string Type   Object Type
	 * @return DescribeLayoutResult
	 */
	public function describeLayout($type, array $recordTypeIds=null) {
		$this->setHeaders("describeLayout");
		$arg = new stdClass();
		$arg->sObjectType = new SoapVar($type, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		if (isset($recordTypeIds) && count($recordTypeIds)) 
			$arg->recordTypeIds = $recordTypeIds;
		return $this->sforce->describeLayout($arg)->result;
	}

	/**
	 * Describes metadata (field list and object properties) for the specified
	 * object.
	 *
	 * @param string $type    Object type
	 * @return DescribsSObjectResult
	 */
	public function describeSObject($type) {
		$this->setHeaders("describeSObject");
		$arg = new stdClass();
		$arg->sObjectType = new SoapVar($type, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		return $this->sforce->describeSObject($arg)->result;
	}

	/**
	 * An array-based version of describeSObject; describes metadata (field list
	 * and object properties) for the specified object or array of objects.
	 *
	 * @param array $arrayOfTypes    Array of object types.
	 * @return DescribsSObjectResult
	 */
	public function describeSObjects($arrayOfTypes) {
		$this->setHeaders("describeSObjects");
		return $this->sforce->describeSObjects($arrayOfTypes)->result;
	}

	/**
	 * The describeTabs call returns information about the standard apps and
	 * custom apps, if any, available for the user who sends the call, including
	 * the list of tabs defined for each app.
	 *
	 * @return DescribeTabSetResult
	 */
	public function describeTabs() {
		$this->setHeaders("describeTabs");
		return $this->sforce->describeTabs()->result;
	}

	/**
	 * To enable data categories groups you must enable Answers or Knowledge Articles module in
	 * admin panel, after adding category group and assign it to Answers or Knowledge Articles
	 *
	 * @param string $sObjectType   sObject Type
	 * @return DescribeDataCategoryGroupResult
	 */
	public function describeDataCategoryGroups($sObjectType) {
		$this->setHeaders('describeDataCategoryGroups');
		$arg = new stdClass();
		$arg->sObjectType = new SoapVar($sObjectType, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		return $this->sforce->describeDataCategoryGroups($arg)->result;
	}

	/**
	 * Retrieves available category groups along with their data category structure for objects specified in the request.
	 *
	 * @param DataCategoryGroupSobjectTypePair $pairs 
	 * @param bool $topCategoriesOnly   Object Type
	 * @return DescribeLayoutResult
	 */
	public function describeDataCategoryGroupStructures(array $pairs, $topCategoriesOnly) {
		$this->setHeaders('describeDataCategoryGroupStructures');
		$arg = new stdClass();
		$arg->pairs = $pairs;
		$arg->topCategoriesOnly = new SoapVar($topCategoriesOnly, XSD_BOOLEAN, 'boolean', 'http://www.w3.org/2001/XMLSchema');

		return $this->sforce->describeDataCategoryGroupStructures($arg)->result;
	}

	/**
	 * Retrieves the list of individual objects that have been deleted within the
	 * given timespan for the specified object.
	 *
	 * @param string $type    Ojbect type
	 * @param date $startDate  Start date
	 * @param date $endDate   End Date
	 * @return GetDeletedResult
	 */
	public function getDeleted($type, $startDate, $endDate) {
		$this->setHeaders("getDeleted");
		$arg = new stdClass();
		$arg->sObjectType = new SoapVar($type, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		$arg->startDate = $startDate;
		$arg->endDate = $endDate;
		return $this->sforce->getDeleted($arg)->result;
	}

	/**
	 * Retrieves the list of individual objects that have been updated (added or
	 * changed) within the given timespan for the specified object.
	 *
	 * @param string $type    Ojbect type
	 * @param date $startDate  Start date
	 * @param date $endDate   End Date
	 * @return GetUpdatedResult
	 */
	public function getUpdated($type, $startDate, $endDate) {
		$this->setHeaders("getUpdated");
		$arg = new stdClass();
		$arg->sObjectType = new SoapVar($type, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		$arg->startDate = $startDate;
		$arg->endDate = $endDate;
		return $this->sforce->getUpdated($arg)->result;
	}

	/**
	 * Executes a query against the specified object and returns data that matches
	 * the specified criteria.
	 *
	 * @param String $query Query String
	 * @param QueryOptions $queryOptions  Batch size limit.  OPTIONAL
	 * @return QueryResult
	 */
	public function query($query) {
		$this->setHeaders("query");
		$raw = $this->sforce->query(array (
					  'queryString' => $query
		))->result;
		$QueryResult = new QueryResult($raw);
		$QueryResult->setSf($this); // Dependency Injection
		return $QueryResult;
	}

	/**
	 * Retrieves the next batch of objects from a query.
	 *
	 * @param QueryLocator $queryLocator Represents the server-side cursor that tracks the current processing location in the query result set.
	 * @param QueryOptions $queryOptions  Batch size limit.  OPTIONAL
	 * @return QueryResult
	 */
	public function queryMore($queryLocator) {
		$this->setHeaders("queryMore");
		$arg = new stdClass();
		$arg->queryLocator = $queryLocator;
		$raw = $this->sforce->queryMore($arg)->result;
		$QueryResult = new QueryResult($raw);
		$QueryResult->setSf($this); // Dependency Injection
		return $QueryResult;
	}

	/**
	 * Retrieves data from specified objects, whether or not they have been deleted.
	 *
	 * @param String $query Query String
	 * @param QueryOptions $queryOptions  Batch size limit.  OPTIONAL
	 * @return QueryResult
	 */
	public function queryAll($query, $queryOptions = NULL) {
		$this->setHeaders("queryAll");
		$raw = $this->sforce->queryAll(array (
						'queryString' => $query
		))->result;
		$QueryResult = new QueryResult($raw);
		$QueryResult->setSf($this); // Dependency Injection
		return $QueryResult;
	}


	/**
	 * Retrieves one or more objects based on the specified object IDs.
	 *
	 * @param string $fieldList      One or more fields separated by commas.
	 * @param string $sObjectType    Object from which to retrieve data.
	 * @param array $ids            Array of one or more IDs of the objects to retrieve.
	 * @return sObject[]
	 */
	public function retrieve($fieldList, $sObjectType, $ids) {
		$this->setHeaders("retrieve");
		$arg = new stdClass();
		$arg->fieldList = $fieldList;
		$arg->sObjectType = new SoapVar($sObjectType, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		$arg->ids = $ids;
		return $this->sforce->retrieve($arg)->result;
	}

	/**
	 * Executes a text search in your organization's data.
	 *
	 * @param string $searchString   Search string that specifies the text expression to search for.
	 * @return SearchResult
	 */
	public function search($searchString) {
		$this->setHeaders("search");
		$arg = new stdClass();
		$arg->searchString = new SoapVar($searchString, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		return new SforceSearchResult($this->sforce->search($arg)->result);
	}

	/**
	 * Retrieves the current system timestamp (GMT) from the Web service.
	 *
	 * @return timestamp
	 */
	public function getServerTimestamp() {
		$this->setHeaders("getServerTimestamp");
		return $this->sforce->getServerTimestamp()->result;
	}

	public function getUserInfo() {
		$this->setHeaders("getUserInfo");
		return $this->sforce->getUserInfo()->result;
	}

	/**
	 * Sets the specified user's password to the specified value.
	 *
	 * @param string $userId    ID of the User.
	 * @param string $password  New password
	 */
	public function setPassword($userId, $password) {
		$this->setHeaders("setPassword");
		$arg = new stdClass();
		$arg->userId = new SoapVar($userId, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		$arg->password = $password;
		return $this->sforce->setPassword($arg);
	}

	/**
	 * Changes a user's password to a system-generated value.
	 *
	 * @param string $userId    Id of the User
	 * @return password
	 */
	public function resetPassword($userId) {
		$this->setHeaders("resetPassword");
		$arg = new stdClass();
		$arg->userId = new SoapVar($userId, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
		return $this->sforce->resetPassword($arg)->result;
	}
}

class SforceSearchResult {
	public $searchRecords;

	public function __construct($response) {

		if($response instanceof SforceSearchResult) {
			$this->searchRecords = $response->searchRecords;
		} else {
			$this->searchRecords = array();
			if (isset($response->searchRecords)) {
				if (is_array($response->searchRecords)) {
					foreach ($response->searchRecords as $record) {
						$sobject = new SObject($record->record);
						array_push($this->searchRecords, $sobject);
					};
				} else {
					$sobject = new SObject($response->searchRecords->record);
					array_push($this->records, $sobject);
				}
			}
		}
	}
}

class QueryResult implements Iterator{
	public $queryLocator;
	public $done;
	public $records;
	public $size;

	public $pointer; // Current iterator location
	private $sf; // SOAP Client
	
	public function __construct($response) {
		$this->queryLocator = $response->queryLocator;
		$this->done = $response->done;
		$this->size = $response->size;
		
		$this->pointer = 0;
		$this->sf = false;

		if($response instanceof QueryResult) {
			$this->records = $response->records;
		} else {
			$this->records = array();
			if (isset($response->records)) {
				if (is_array($response->records)) {
					foreach ($response->records as $record) {
						array_push($this->records, $record);
					};
				} else {
					array_push($this->records, $record);
				}
			}
		}
	}
	
	public function setSf(SforceBaseClient $sf) { $this->sf = $sf; } // Dependency Injection
	
	// Basic Iterator implementation functions
	public function rewind() { $this->pointer = 0; }
	public function next() { ++$this->pointer; }
	public function key() { return $this->pointer; }
	public function current() { return new SObject($this->records[$this->pointer]); }
	
	public function valid() {
		while ($this->pointer >= count($this->records)) {
			// Pointer is larger than (current) result set; see if we can fetch more
			if ($this->done === false) {
				if ($this->sf === false) throw new Exception("Dependency not met!");
				$response = $this->sf->queryMore($this->queryLocator);
				$this->records = array_merge($this->records, $response->records); // Append more results
				$this->done = $response->done;
				$this->queryLocator = $response->queryLocator;
			} else {
				return false; // No more records to fetch
			}
		}
		if (isset($this->records[$this->pointer])) return true;
		
		throw new Exception("QueryResult has gaps in the record data?");
	}
}

class SObject {
	public $type;
	public $fields;
//	public $sobject;

	public function __construct($response=NULL) {
		if (!isset($response) && !$response) {
			return;
		}

		foreach ($response as $responseKey => $responseValue) {
			if (in_array(strval($responseKey), array('Id', 'type', 'any'))) {
				continue;
			}
			$this->$responseKey = $responseValue;
		}

		if (isset($response->Id)) {
			$this->Id = is_array($response->Id) ? $response->Id[0] : $response->Id;
		}

		if (isset($response->type)) {
			$this->type = $response->type;
		}

		if (isset($response->any)) {
			try {
				//$this->fields = $this->convertFields($response->any);
				// If ANY is an object, instantiate another SObject
				if ($response->any instanceof stdClass) {
					if ($this->isSObject($response->any)) {
						$anArray = array();
						$sobject = new SObject($response->any);
						$anArray[] = $sobject;
						$this->sobjects = $anArray;
					} else {
						// this is for parent to child relationships
						$this->queryResult = new QueryResult($response->any);
					}

				} else {
					// If ANY is an array
					if (is_array($response->any)) {
						// Loop through each and perform some action.
						$anArray = array();

						// Modify the foreach to have $key=>$value
						// Added on 28th April 2008
						foreach ($response->any as $key=>$item) {
							if ($item instanceof stdClass) {
								if ($this->isSObject($item)) {
									$sobject = new SObject($item);
									// make an associative array instead of a numeric one
									$anArray[$key] = $sobject;
								} else {
									// this is for parent to child relationships
									//$this->queryResult = new QueryResult($item);
									if (!isset($this->queryResult)) {
										$this->queryResult = array();
									}
									array_push($this->queryResult, new QueryResult($item));
								}
							} else {
								//$this->fields = $this->convertFields($item);

								if (strpos($item, 'sf:') === false) {
									$currentXmlValue = sprintf('<sf:%s>%s</sf:%s>', $key, $item, $key);
								} else {
									$currentXmlValue = $item;
								}

								if (!isset($fieldsToConvert)) {
									$fieldsToConvert = $currentXmlValue;
								} else {
									$fieldsToConvert .= $currentXmlValue;
								}
							}
						}

						if (isset($fieldsToConvert)) {
							// If this line is commented, then the fields becomes a stdclass object and does not have the name variable
							// In this case the foreach loop on line 252 runs successfuly
							$this->fields = $this->convertFields($fieldsToConvert);
						}

						if (sizeof($anArray) > 0) {
							// To add more variables to the the top level sobject
							foreach ($anArray as $key=>$children_sobject) {
								$this->fields->$key = $children_sobject;
							}
							//array_push($this->fields, $anArray);
							// Uncommented on 28th April since all the sobjects have now been moved to the fields
							//$this->sobjects = $anArray;
						}

						/*
						   $this->fields = $this->convertFields($response->any[0]);
						   if (isset($response->any[1]->records)) {
						   $anArray = array();
						   if ($response->any[1]->size == 1) {
						   $records = array (
						   $response->any[1]->records
						   );
						   } else {
						   $records = $response->any[1]->records;
						   }
						   foreach ($records as $record) {
						   $sobject = new SObject($record);
						   array_push($anArray, $sobject);
						   }
						   $this->sobjects = $anArray;
						   } else {
						   $anArray = array();
						   $sobject = new SObject($response->any[1]);
						   array_push($anArray, $sobject);
						   $this->sobjects = $anArray;
						   }
						 */
					} else {
						$this->fields = $this->convertFields($response->any);
					}
				}
			} catch (Exception $e) {
				var_dump('exception: ', $e);
			}
		}
	}
	
	function __get($name) {	return (isset($this->fields->$name))? $this->fields->$name : false; }
	function __isset($name) { return isset($this->fields->$name); }

	/**
	 * Parse the "any" string from an sObject.  First strip out the sf: and then
	 * enclose string with <Object></Object>.  Load the string using
	 * simplexml_load_string and return an array that can be traversed.
	 */
	function convertFields($any) {
		$str = preg_replace('{sf:}', '', $any);

		$array = $this->xml2array('<Object xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.$str.'</Object>', 2);

		$xml = new stdClass();
		if (!count($array['Object']))
			return $xml;

		foreach ($array['Object'] as $k=>$v) {
			$xml->$k = $v;
		}

		//$new_string = '<Object xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.$new_string.'</Object>';
		//$new_string = $new_string;
		//$xml = simplexml_load_string($new_string);
		return $xml;
	}

	/**
	 * 
	 * @param string $contents
	 * @return array
	 */
	function xml2array($contents, $get_attributes=1) {
		if(!$contents) return array();

		if(!function_exists('xml_parser_create')) {
			//print "'xml_parser_create()' function not found!";
			return array('not found');
		}
		//Get the XML parser of PHP - PHP must have this module for the parser to work
		$parser = xml_parser_create();
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parse_into_struct( $parser, $contents, $xml_values );
		xml_parser_free( $parser );

		if(!$xml_values) return;//Hmm...

		//Initializations
		$xml_array = array();
		$parents = array();
		$opened_tags = array();
		$arr = array();

		$current = &$xml_array;

		//Go through the tags.
		foreach($xml_values as $data) {
			unset($attributes,$value);//Remove existing values, or there will be trouble

			//This command will extract these variables into the foreach scope
			// tag(string), type(string), level(int), attributes(array).
			extract($data);//We could use the array by itself, but this cooler.

			$result = '';
			if ($get_attributes) {
				switch ($get_attributes) {
					case 1:
						$result = array();
						if(isset($value)) $result['value'] = $value;

						//Set the attributes too.
						if(isset($attributes)) {
							foreach($attributes as $attr => $val) {
								if($get_attributes == 1) $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
								/**  :TODO: should we change the key name to '_attr'? Someone may use the tagname 'attr'. Same goes for 'value' too */
							}
						}
						break;

					case 2:
						$result = array();
						if (isset($value)) {
							$result = $value;
						}

						//Check for nil and ignore other attributes.
						if (isset($attributes) && isset($attributes['xsi:nil']) && !strcasecmp($attributes['xsi:nil'], 'true')) {
							$result = null;
						}
						break;
				}
			} elseif (isset($value)) {
				$result = $value;
			}

			//See tag status and do the needed.
			if($type == "open") {//The starting of the tag '<tag>'
				$parent[$level-1] = &$current;

				if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
					$current[$tag] = $result;
					$current = &$current[$tag];

				} else { //There was another element with the same tag name
					if(isset($current[$tag][0])) {
						array_push($current[$tag], $result);
					} else {
						$current[$tag] = array($current[$tag],$result);
					}
					$last = count($current[$tag]) - 1;
					$current = &$current[$tag][$last];
				}

			} elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
				//See if the key is already taken.
				if(!isset($current[$tag])) { //New Key
					$current[$tag] = $result;

				} else { //If taken, put all things inside a list(array)
					if((is_array($current[$tag]) and $get_attributes == 0)//If it is already an array...
							or (isset($current[$tag][0]) and is_array($current[$tag][0]) and ($get_attributes == 1 || $get_attributes == 2))) {
						array_push($current[$tag],$result); // ...push the new element into that array.
					} else { //If it is not an array...
						$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
					}
				}

			} elseif($type == 'close') { //End of tag '</tag>'
				$current = &$parent[$level-1];
			}
		}

		return($xml_array);
	}

	/*
	 * If the stdClass has a done, we know it is a QueryResult
	 */
	function isQueryResult($param) {
		return isset($param->done);
	}

	/*
	 * If the stdClass has a type, we know it is an SObject
	 */
	function isSObject($param) {
		return isset($param->type);
	}
}
