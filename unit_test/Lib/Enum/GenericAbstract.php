<?php
abstract class Lib_Enum_GenericAbstract
{
	/**
	 * Get style by id.
	 * 
	 * @param int $id
	 * @return mixed or NULL on failure
	 */
	public function getById($id)
	{
		$id = (int)$id;
		if($id <= 0 || $id > sizeof($this->_values)) {
			return NULL;
		}
		
		return $this->_values[$id];
	}
	
	/**
	 * Validates id
	 * 
	 * @param $value
	 * @return boolean
	 */
	public function validateId($id)
	{
		if(!is_numeric($id)){
			return FALSE;
		}
		$id = (int)$id;
		if($id <= 0) {
			return FALSE;
		}
		
		return $id <= sizeof($this->_values);
	}
	
	/**
	 * Validates value
	 * 
	 * @param $value
	 * @return boolean
	 */
	public function validateValue($value)
	{
		if($value == '') {
			return false;
		}
		return in_array($value, $this->_values);
	}

	/**
	 * @return array
	 */
	public function getValues()
	{
		return $this->values;
	}
}