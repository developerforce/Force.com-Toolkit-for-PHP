<?php
/*
	To enable data categories groups you must enable Answers or Knowledge Articles module in
	admin panel, after adding category group and assign it to Answers or Knowledge Articles
	
	Availible types: "KnowledgeArticleVersion", "Question"
*/

class Lib_Test_Enterprise_DescribeDataCategoryGroupsTest extends Lib_Test_TestAbstractPartner
{
	public function getTestName()
	{
		return 'DescribeDataCategoryGroups';
	}
	
	protected function _run()
	{
		$response = $this->_mySforceConnection->describeDataCategoryGroups('Question');
		echo "***** ".$this->getTestName()." # Question *****\n";
		print_r($response);
	}
}
