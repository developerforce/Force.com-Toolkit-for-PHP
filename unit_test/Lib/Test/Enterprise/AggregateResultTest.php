<?php
class Lib_Test_Enterprise_AggregateResultTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'AggregateResult';
	}
	
	protected function _run()
	{
		$query = 'Select Count(Id) IndustryCount, Avg(NumberOfEmployees) AvgEmployees, Industry From Account Group By Industry Limit 2';
		$response = $this->_mySforceConnection->query($query);
		$response = new QueryResult($response);

		echo "\n***** ".$this->getTestName()." *****\n";
		print_r($response);

		echo "\n***** ".$this->getTestName()." response records: *****\n";
		if (isset($response->records) && !empty($response->records)) {
			foreach ($response->records as $key=>$record) {
				echo "\n***** $key *****\n";
				if (isset($record->fields)) {
					print_r($record->fields);
				}
			}
		}

	}
}
