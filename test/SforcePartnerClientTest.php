<?php
require_once ('PHPUnit/Framework/TestCase.php');
require_once ('../soapclient/SforcePartnerClient.php');
require_once ('../soapclient/SforceHeaderOptions.php');

/*
 * Copyright (c) 2007, salesforce.com, inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided
 * that the following conditions are met:
 *
 *    Redistributions of source code must retain the above copyright notice, this list of conditions and the
 *    following disclaimer.
 *
 *    Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *    the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *    Neither the name of salesforce.com, inc. nor the names of its contributors may be used to endorse or
 *    promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
 * PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * This file contains one class.
 * @package SalesforceTest
 */
/**
 * SforcePartnerClientTest class.
 *
 * @package SalesforceTest
 */
class SforcePartnerClientTest extends PHPUnit_Framework_TestCase {
  private $wsdl = '../soapclient/partner.wsdl.xml';
  private $username = 'username@sample.com';
  private $password = 'changeme';
  protected $mySforceConnection = null;
  protected $mylogin = null;
  protected $theId = null;

  public function deleteAll($queryResult) {
    $records = $queryResult->records;
    $ids = array ();
    $buckets = array_chunk($records, 200);
    foreach ($buckets as $bucket) {
      $ids = array ();
      foreach ($bucket as $record) {
        $sObject = new SObject($record);
        //var_dump($sObject);
        $ids[] = $sObject->Id;
      }
      try {
        global $mySforceConnection;
        $this->mySforceConnection->delete($ids);
      } catch (Exception $e) {
        print_r($e->faultstring);
      }
    }

    $queryLocator = $queryResult->queryLocator;
    if (isset($queryLocator)) {
      $result = $this->mySforceConnection->queryMore($queryLocator);
      $this->deleteAll($result);
    }
  }

  protected function setUp() {
    echo "Begin Test Setup\r\n";
    try {
      // Create the fixtures.
      $this->mySforceConnection = new SforcePartnerClient();
      $this->mySforceConnection->createConnection($this->wsdl);
      $this->mylogin = $this->mySforceConnection->login($this->username, $this->password);

      // CLEAN
      $queryOptions = new QueryOptions(300);
      $createQuery = 'SELECT Id from Contact where FirstName = \'DELETE_ME\'';
      $leadQuery = 'SELECT Id from Lead where FirstName = \'DELETE_ME\'';
      $createQueryResult = $this->mySforceConnection->query($createQuery, $queryOptions);
      $leadQueryResult = $this->mySforceConnection->query($leadQuery, $queryOptions);
      if ($createQueryResult->size > 0) {
        echo 'Deleting '.$createQueryResult->size." contacts.\r\n";
        $this->deleteAll($createQueryResult);
      }
      if ($leadQueryResult->size > 0) {
        $this->deleteAll($leadQueryResult);
      }
      $createFields = array (
        'FirstName' => 'DELETE_ME',
        'LastName' => 'DELETE_ME',
        'MailingCity' => 'San Diego',
        'MailingState' => 'CA'
        );
        $sObject1 = new SObject();
        $sObject1->fields = $createFields;
        $sObject1->type = 'Contact';
        $createResponse = $this->mySforceConnection->create(array($sObject1));
        $this->assertNotNull($createResponse);
        $this->assertTrue($createResponse->success);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
    $this->theId = $createResponse->id;
    echo "Test setup complete.\r\n";
  }

  public function testLogin() {
    echo "testLogin\r\n";
    try {
      $mylogin = $this->mylogin;
      $this->assertNotNull($mylogin);
      $this->assertNotNull($mylogin->userInfo);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testServerTimestamp() {
    echo "testServerTimestamp\r\n";
    try {
      $timeStamp = $this->mySforceConnection->getServerTimestamp();
      $this->assertNotNull($timeStamp);
      $this->assertNotNull($timeStamp->timestamp);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testUserInfo() {
    echo "testUserInfo\r\n";
    try {
      $userInfo = $this->mySforceConnection->getUserInfo();
      $this->assertNotNull($userInfo);
      $this->assertNotNull($userInfo->userId);
      $this->assertNotNull($userInfo->userFullName);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testDescribeSObject() {
    echo "testDescribeSObject\r\n";
    try {
      $response = $this->mySforceConnection->describeSObject('Contact');
      $this->assertNotNull($response);
      $this->assertEquals('Contact', $response->name);
    } catch (SoapFault $fault) {
      echo $fault;
      $this->fail($fault->faultstring);
    }
  }

  public function testDescribeSObjects() {
    echo "testDescribeSObjects\r\n";
    try {
      $response = $this->mySforceConnection->describeSObjects(array (
        'Account',
        'Contact'
        ));
        $this->assertNotNull($response);
        $this->assertEquals(2, sizeof($response));
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testDescribeGlobal() {
    echo "testDescribeGlobal\r\n";
    try {
      $response = $this->mySforceConnection->describeGlobal();
      $this->assertNotNull($response);
      $this->assertNotNull($response->types);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testDescribeLayout() {
    echo "testDescribeLayout\r\n";
    try {
      $response = $this->mySforceConnection->describeLayout('Contact');
      $this->assertNotNull($response);
      $this->assertNotNull($response->recordTypeMappings);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testDescribeTabs() {
    echo "testDescribeTabs\r\n";
    try {
      $response = $this->mySforceConnection->describeTabs();
      $this->assertNotNull($response);
      $this->assertTrue(sizeof($response) > 0);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testCreate() {
    // now done in setUp so that theId can be accessed by other tests.
  }

  public function testRetrieve() {
    echo "testRetrieve\r\n";
    try {
      $ids = array ($this->theId);
      $this->assertEquals(1, count($ids));

      $retrieveFields = 'LastName';
      $retrieveResponse = $this->mySforceConnection->retrieve($retrieveFields, 'Contact', $ids);
      $sObject = new SObject($retrieveResponse);
      $this->assertEquals($this->theId, $sObject->Id);
      // Fields on SObjects are SimpleXMLElements.
      //$this->assertEquals('DELETE_ME', $sObject->fields->LastName);
      //$this->assertEquals('San Diego', $sObject->MailingCity);
      //$this->assertEquals('CA', $sObject->MailingState);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testUpdate() {
    echo "testUpdate\r\n";
    try {
      $id = $this->theId;
      $this->assertNotNull($id);
      $updateFields = array (
        'Id' => $id,
        'MailingCity' => 'New York',
        'MailingState' => 'NY'
        );
        $sObject1 = new SObject();
        $sObject1->fields = $updateFields;
        $sObject1->type = 'Contact';
        $updateResponse = $this->mySforceConnection->update(array ($sObject1));
        $this->assertNotNull($updateResponse);
        $this->assertTrue($updateResponse->success);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testGetUpdated() {
    echo "testGetUpdated\r\n";
    try {
      $type = 'Contact';
      $currentTime = mktime();
      // assume that create or update occured within the last 5 mins.
      $startTime = $currentTime-(60*5);
      $endTime = $currentTime;
      $response = $this->mySforceConnection->getUpdated($type, $startTime, $endTime);
      $this->assertNotNull($response);

    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testDelete() {
    echo "testDelete\r\n";
    try {
      $ids = array ($this->theId);
      $this->assertEquals(1, count($ids));
      $response = $this->mySforceConnection->delete($ids);
      $this->assertNotNull($response);
      $this->assertTrue($response->success);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testGetDeleted() {
    echo "testGetDeleted\r\n";
    try {
      $type = 'Contact';
      $currentTime = mktime();
      // assume that delete occured within the last 5 mins.
      $startTime = $currentTime-(60*10);
      $endTime = $currentTime;
      $response = $this->mySforceConnection->getDeleted($type, $startTime, $endTime);
      if (isset($response)) {
        $this->assertNotNull(count($response->deletedRecords));
        echo'deleted records = '.count($response->deletedRecords)."\r\n";
      }
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testQuery() {
    echo "testQuery\r\n";
    $query = 'SELECT Id,Name,BillingCity,BillingState,Phone,Fax from Account';
    $queryOptions = new QueryOptions(200);
    try {
      $response = $this->mySforceConnection->query($query, $queryOptions);
      $this->assertNotNull($response);
      $this->assertTrue(sizeof($response) > 0);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }
  /*
   public function testQueryMore() {
   echo "testQueryMore\r\n";
   $createFields = array (
   'FirstName' => 'DELETE_ME',
   'LastName' => 'DELETE_ME',
   'MailingCity' => 'San Diego',
   'MailingState' => 'CA'
   );

   $sObject1 = new SObject();
   $sObject1->fields = $createFields;
   $sObject1->type = 'Contact';

   $createNum = 500;
   try {
   for ($counter = 0; $counter < $createNum; $counter++) {
   $createResponse = $this->mySforceConnection->create(array ($sObject1));
   $this->assertTrue($createResponse->success);
   }

   $query = 'SELECT Id from Contact where FirstName = \'DELETE_ME\'';
   $queryOptions = new QueryOptions(300);

   $queryResponse = $this->mySforceConnection->query($query, $queryOptions);
   $this->assertEquals($createNum+1, $queryResponse->size);
   if ($queryResponse->size) {
   $this->deleteAll($queryResponse);
   }
   $queryResponse = $this->mySforceConnection->query($query, $queryOptions);
   $this->assertEquals(0, $queryResponse->size);
   } catch (SoapFault $fault) {
   $this->fail($fault->faultstring);
   }
   }
   */

  public function testUpsert() {
    // Make the language field an external id field on the contact
    // table before proceeding.
    echo "testUpsert\r\n";
    $createFields = array (
      'FirstName' => 'DELETE_ME',
      'LastName' => 'DELETE_ME',
      'MailingCity' => 'TEST_UPSERT',
      'MailingState' => 'CA',
      'Languages__c' => 'TEST_UPSERT_ID'
      );
      $external_id = 'Languages__c';
      try {
        $sObject1 = new SObject();
        $sObject1->fields = $createFields;
        $sObject1->type = 'Contact';
        $upsertResponse = $this->mySforceConnection->upsert($external_id, array($sObject1));
        $this->assertNotNull($upsertResponse);
        $this->assertTrue($upsertResponse->success);
        $id = $upsertResponse->id;
        $ids = array ($id);
        $retrieveFields = 'Id, FirstName, LastName, MailingCity, MailingState';
        $retrieveResponse = $this->mySforceConnection->retrieve($retrieveFields, 'Contact', $ids);
        $this->assertNotNull($retrieveResponse);
        $sObject = new SObject($retrieveResponse);
        $this->assertEquals($id, $sObject->Id);
      } catch (SoapFault $fault) {
        $this->fail($fault->faultstring);
      }
  }

  public function testAssignmentRuleHeaderId() {
    echo "testAssignmentRuleHeaderId\r\n";
    $header = new AssignmentRuleHeader('01Q300000005lDgEAI', NULL);
    $createFields = array (
      'FirstName' => 'DELETE_ME',
      'LastName' => 'DELETE_ME',
      'Email' => 'deleteme@salesforce.com',
      'Company' => 'DELETE_ME Company',
      'LeadSource' => 'PHPUnit2',
      'City' => 'San Diego',
      'State' => 'CA'
      );
      $sObject1 = new SObject();
      $sObject1->fields = $createFields;
      $sObject1->type = 'Lead';
      try {
        $createResponse = $this->mySforceConnection->create(array ($sObject1), $header, NULL);
      } catch (SoapFault $fault) {
        $this->fail($fault->faultstring);
      }
  }

  public function testAssignmentRuleHeaderFlag() {
    echo "testAssignmentRuleHeaderFlag\r\n";
    $header = new AssignmentRuleHeader(NULL, TRUE);
    $createFields = array (
      'FirstName' => 'DELETE_ME',
      'LastName' => 'DELETE_ME',
      'Email' => 'deleteme@salesforce.com',
      'Company' => 'DELETE_ME Company',
      'LeadSource' => 'PHPUnit2',
      'City' => 'San Diego',
      'State' => 'CA'
      );
      $sObject1 = new SObject();
      $sObject1->fields = $createFields;
      $sObject1->type = 'Lead';
      try {
        $createResponse = $this->mySforceConnection->create(array ($sObject1), $header, NULL);
        $this->assertNotNull($createResponse);
        $this->assertTrue($createResponse->success);
      } catch (SoapFault $fault) {
        $this->fail($fault->faultstring);
      }
  }

  public function testMruHeader() {
    echo "testMruHeader\r\n";
    $header = new MruHeader(TRUE);
    $createFields = array (
      'FirstName' => 'DELETE_ME',
      'LastName' => 'DELETE_ME',
      'MailingCity' => 'San Diego',
      'MailingState' => 'CA'
      );
      $sObject1 = new SObject();
      $sObject1->fields = $createFields;
      $sObject1->type = 'Contact';
      try {
        $createResponse = $this->mySforceConnection->create(array ($sObject1), NULL, $header);
        $this->assertNotNull($createResponse);
        $this->assertTrue($createResponse->success);
      } catch (SoapFault $fault) {
        $this->fail($fault->faultstring);
      }
  }

  public function testSearch() {
    echo "testSearch\r\n";
    try {
      $searchResponse
      = $this->mySforceConnection->search('FIND {DELETE_ME} returning contact(id, phone, firstname, lastname)');
      $this->assertNotNull($searchResponse);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testSendSingleEmail() {
    echo "testSendSingleEmail\r\n";
    try {
      $singleEmail1 = new SingleEmailMessage();
      $singleEmail1->toAddresses = "ntran@salesforce.com";
      $singleEmail1->plainTextBody = "Hello there";
      $singleEmail1->subject = "First Single Email";
      $singleEmail1->saveAsActivity = true;
      $singleEmail1->emailPriority = EMAIL_PRIORITY_LOW;

      $singleEmail2 = new SingleEmailMessage();
      $singleEmail2->toAddresses = "ntran@salesforce.com";
      $singleEmail2->plainTextBody = "Hello there";
      $singleEmail2->subject = "Second Single Email";
      $singleEmail2->saveAsActivity = true;
      $singleEmail2->emailPriority = EMAIL_PRIORITY_LOW;

      $emailResponse = $this->mySforceConnection->sendSingleEmail(array ($singleEmail1, $singleEmail2));
      $this->assertNotNull($emailResponse);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testSendMassEmail() {
    echo "testSendMassEmail\r\n";
    try {
      $massEmail = new MassEmailMessage();
      $massEmail->subject = "Nicks Mass Email Message";
      $massEmail->saveAsActivity = true;
      $massEmail->emailPriority = EMAIL_PRIORITY_LOW;
      $massEmail->templateId = "00X50000000wX9q";
      $massEmail->targetObjectIds = array ("0035000000PiCMd");

      $emailResponse = $this->mySforceConnection->sendMassEmail(array($massEmail));

      $this->assertNotNull($emailResponse);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }

  public function testEmptyRecycleBin() {
    echo "testEmptyRecycleBin\r\n";
    try {
      $fields = array (
      'Type' => 'Electrical'
      );

      $sObject = new SObject();
      $sObject->fields = $fields;
      $sObject->type = 'Case';

      //echo "Creating Case:\n";
      $response = $this->mySforceConnection->create(array ($sObject));
      $this->assertNotNull($response);
      $id = $response->id;
      //echo "Deleted Case:\n";
      $response = $this->mySforceConnection->delete(array($id));
      $this->assertNotNull($response);
      //echo "Empty Recycle Bin:\n";
      $response = $this->mySforceConnection->emptyRecycleBin(array($id));
      $this->assertNotNull($response);
    } catch (SoapFault $fault) {
      $this->fail($fault->faultstring);
    }
  }


}
?>