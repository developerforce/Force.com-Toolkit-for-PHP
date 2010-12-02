<?php
class Lib_Test_Partner_QueryMoreTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'QueryMore';
	}
	
	protected function _run()
	{
		$query = 'SELECT NumberOfEmployees from Lead order by NumberOfEmployees';
		$options = new QueryOptions(10);
		$this->_mySforceConnection->setQueryOptions($options);
		$response = $this->_mySforceConnection->query($query);
		
		print_r($response);
		
		$queryResult = new QueryResult($response);
		
//		print_r($queryResult);
		
		!$done = false;

		echo "Size of records:  ".$queryResult->size;

		if ($queryResult->size > 0) {
			while (!$done) {
				foreach ($queryResult->records as $record) {
					echo 'NumberOfEmployees=' .$record->fields->NumberOfEmployees."\r\n";
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