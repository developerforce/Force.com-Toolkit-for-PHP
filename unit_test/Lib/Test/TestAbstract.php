<?php
/**
 * TODO Add logging of original xml's (request and response)
 * 
 * result file name format: TEST_NAME _ WSDL_NAME _ PHP_VERSION
 */
abstract class Lib_Test_TestAbstract
{
	protected $_wsdl;

	protected $_userName = 'hunter@barefootsolutions.com';
	//protected $_password = 'qwqDbM3q'+token;
	protected $_password = 'qwqDbM3qD26eh0xIZhFmf7poWeYcTvrHq';
	protected $_token = 'D26eh0xIZhFmf7poWeYcTvrHq';

	protected $_portalId = NULL;

	protected $_mySforceConnection;
	protected $_mylogin;
	protected $_mySoapClient;
	
	protected $_logger;
	protected $_soapDir;
	protected $_phpVersion;
	
	protected $_medaDataWsdlName = 'metadata';
	
	public function __construct($soapDir)
	{
		$this->_soapDir = $soapDir;
		$this->_logger = new Lib_Utils_FileLogger(
							$this->getLogName(),
							'w');
		
		$this->_phpVersion = phpversion();
		header('Content-Type: text/plain');
	}
	
	/**
	 * @param Lib_Utils_FileLogger $metaInfoLogger
	 * @return void
	 */
	public function run(Lib_Utils_FileLogger $metaInfoLogger)
	{
		try {
			ob_start();
			$start_time = microtime(TRUE);
			
			try {
				$this->_run();
			} catch (SoapFault $fault) {
				$end_time = microtime(TRUE);
				// some time SoapFaul is sign of positive test result
				$resultStr = ob_get_clean();
				$this->_logger->write($resultStr);
				print '<pre>' . $resultStr . '</pre>';
				
				$this->_logger->write($fault->faultstring);
				print '<pre>SoapFault:</pre>';
				print '<pre>' . $fault->faultstring . '</pre>';
				
				try {
//					$this->_validateSoapFault($resultStr . $fault->faultstring);
					$this->_validateSoapFault($fault->faultstring);
					$metaInfoLogger->write(basename($this->getLogName()) . ' - test is ok!');
				} catch (Lib_Exception_InvalidResponse $e) {
					$metaInfoLogger->write(basename($this->getLogName()) . ' - test failed!');
				}

				return;
			}	
			
			$end_time = microtime(TRUE);
			$resultStr = ob_get_clean();
			$this->_logger->write($resultStr);
			print $resultStr;
			
			try {
				$this->_validate($resultStr);
				$metaInfoLogger->write(basename($this->getLogName()) . ' - test is ok!');
			} catch (Lib_Exception_InvalidResponse $e) {
				$metaInfoLogger->write(basename($this->getLogName()) . ' - test failed!');
			}
			
			$metaInfoLogger->write('Total time: '. ($end_time - $start_time));
			$metaInfoLogger->write('Max allocated memory: '. memory_get_peak_usage(TRUE));			
		} catch (Exception $e) {
			$this->_logger->write($this->_mySforceConnection->getLastRequest());
			ob_start();
			print_r($e);
			$eStr = ob_get_clean();
			$this->_logger->write($eStr);
			
			print $this->_mySforceConnection->getLastRequest();
			echo "\n\n\n";
			print $eStr;
			
			$metaInfoLogger->write('Test failed!');
		}
	}
	
	public function getLogName()
	{
		return $this->_soapDir . '/results/' . $this->getTestName() . '_' . $this->getWSDLName() . '_' . 
					$this->getWSDLVersion()	. '_' . phpversion();
	}
	
	protected function _getMetaDataWSDL()
	{
		return $this->_soapDir . '/' . $this->_medaDataWsdlName . '.wsdl.xml';
	}
	
	/**
	 * @return void
	 * @throws SoapFault
	 */
	abstract protected function _run();
	
	/**
	 * NOTE: implement in sub-classes.
	 * 
	 * @param string $rs
	 * @return void
	 * @throws Lib_Exception_InvalidResponse
	 */
	protected function _validate($rs)
	{
		// generic check
		if(strpos($rs, 'errors') !== FALSE) {
			throw new Lib_Exception_InvalidResponse();
		}
	}
	
	/**
	 * NOTE: implement in sub-classes.
	 * 
	 * @param string $rs
	 * @return void
	 * @throws Lib_Exception_InvalidResponse
	 */
	protected function _validateSoapFault($rs)
	{
		throw new Lib_Exception_InvalidResponse();
	}

	/**
	 * 
	 * @return string location of local wsdl file
	 */
	abstract public function getWSDL();

	abstract public function getWSDLName();
	
	abstract public function getWSDLVersion();

	abstract public function getTestName();
}
