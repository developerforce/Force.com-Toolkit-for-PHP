<?php
class Lib_Test_Enterprise_QueryMoreTest extends Lib_Test_TestAbstractEnterprise
{
	public function getTestName()
	{
		return 'QueryMore';
	}
	
	protected function _run()
	{

		$query = 'SELECT NumberOfEmployees from Lead where NumberOfEmployees != null order by NumberOfEmployees';
		$options = new QueryOptions(200);
		$this->_mySforceConnection->setQueryOptions($options);
		$response = $this->_mySforceConnection->query($query);

		!$done = false;
		echo "Size of records:  ".$response ->size."\n";

		if ($response->size > 0) {
			while (!$done) {
				foreach ($response->records as $record) {
					echo $record->NumberOfEmployees."\r\n";
				}
				if ($response->done != true) {
					echo "***** Get Next Chunk *****\n";
					try {
						$response = $this->_mySforceConnection->queryMore($response->queryLocator);
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