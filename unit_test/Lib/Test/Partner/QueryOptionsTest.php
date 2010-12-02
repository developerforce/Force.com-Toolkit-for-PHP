<?php
class Lib_Test_Partner_QueryOptionsTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'QueryOptions';
	}
	
	protected function _run()
	{
		$query = 'SELECT NumberOfEmployees from Lead order by NumberOfEmployees';
		$options = new QueryOptions(200);
		$this->_mySforceConnection->setQueryOptions($options);
		$response = $this->_mySforceConnection->query($query);
		$queryResult = new QueryResult($response);
		!$done = false;

		echo "Size of records:  ".$queryResult->size;

		if ($queryResult->size > 0) {
			while (!$done) {
				foreach ($queryResult->records as $record) {
					echo $record->fields->NumberOfEmployees."\r\n";
				}
				if ($queryResult->done != true) {
					echo "***** Get Next Chunk *****\n";
					try {
						$response = $this->_mySforceConnection->queryMore($queryResult->queryLocator);
						$queryResult = new QueryResult($response);
					} catch (Exception $e) {
						print_r($this->_mySforceConnection->getLastRequest());
						echo $e->faultstring;
					}
				} else {
					$done = true;
				}
			}
		}

	}
}