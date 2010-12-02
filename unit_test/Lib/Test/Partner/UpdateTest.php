<?php
class Lib_Test_Partner_UpdateTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'Update';
	}
	
	/*
	 * TODO Implement 
	 */
	protected function _run()
	{
		$UPDATEOBJECTID1 = '00QA0000001QSQo';
		$UPDATEOBJECTID2 = '00QA0000001QSQn';

		$fieldsToUpdate = array (
			'FirstName' => 'testupdate',
			'City' => 'testupdateCity',
			'Country' => 'US'
		);
		$sObject1 = new SObject();
		$sObject1->fields = $fieldsToUpdate;
		$sObject1->type = 'Lead';
		$sObject1->Id = $UPDATEOBJECTID1;

		$fieldsToUpdate = array (
			'FirstName' => 'testupdate',
			'City' => 'testupdate',
			'State' => 'testupdate',
			'Country' => 'US'
		);
		$sObject2 = new SObject();
		$sObject2->fields = $fieldsToUpdate;
		$sObject2->type = 'Lead';
		$sObject2->Id = $UPDATEOBJECTID2;
		$sObject2->fieldsToNull = array('Fax', 'Email');

		$response = $this->_mySforceConnection->update(array($sObject1, $sObject2));

		print_r($response);
	}
}