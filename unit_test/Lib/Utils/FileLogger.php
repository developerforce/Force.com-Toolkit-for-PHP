<?php
/**
 * @author andrey
 *
 */
class Lib_Utils_FileLogger {
	
	private $_filename;
	private $_hanlder;
	private $_withTimeStamps;
	
	function __construct($filename, $mode = 'a+', $withTimeStamps = FALSE) {
		$this->_filename = $filename;
		$this->_hanlder = fopen($filename, $mode);
		$this->_withTimeStamps = $withTimeStamps;
	}
	
	function __destruct() {
		fclose($this->_hanlder);
	}
	
	public function write($message) {
		fwrite($this->_hanlder,
				($this->_withTimeStamps ? date(DateTime::ATOM) . ' ' : '') . $message . "\n");
	}
}