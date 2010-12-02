<?php
class Lib_Test_TestFactory
{
	private $_logger;
	
	private $_soapDir;
	
	/**
	 * 
	 * @param string $soapDir
	 * @param string $logName
	 * @return void
	 */
	public function __construct($soapDir, $logName)
	{
		$this->_logger = new Lib_Utils_FileLogger($logName, 'a+', TRUE);
		$this->_soapDir = $soapDir;
	}
	
	/**
	 * @param int $type
	 * @param string $target
	 * @return void
	 */
	public function run($type, $target)
	{
		$this->_logger->write(__METHOD__ . ' started');
		
		$WsdlTypeEnum = new Lib_Enum_WsdlType();
		$typeName = $WsdlTypeEnum->getById($type);
		
		// create appropriate test instance and run it
		$className = 'Lib_Test_' . ucfirst(strtolower($typeName)) . '_' . $target . 'Test';
		
		/* @var $testCase Lib_Test_TestAbstract  */
		$testCase = new $className($this->_soapDir);
		$testCase->run($this->_logger);
		
		$this->_logger->write('Lib_Test_TestFactory->run finished');
	}
	
	public function runAll()
	{
		// TODO Implement
		// Iterate throw all PARTNER and ENTERPRISE tests
	}
}