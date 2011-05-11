<?php
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
require_once ('SforceBaseClient.php');
//require_once ('SforceEmail.php');


/**
 * This file contains two classes.
 * @package SalesforceSoapClient
 */
/**
 * SforcePartnerClient class.
 *
 * @package SalesforceSoapClient
 */
class SforcePartnerClient extends SforceBaseClient {
  const PARTNER_NAMESPACE = 'urn:partner.soap.sforce.com';
	
  function SforcePartnerClient() {
    $this->namespace = self::PARTNER_NAMESPACE;
  }

  /**
   * Adds one or more new individual objects to your organization's data.
   * @param array $sObjects Array of one or more sObjects (up to 200) to create.
   * @return SaveResult
   */
  public function create($sObjects) {
    $arg = new stdClass;
    foreach ($sObjects as $sObject) {
      if (isset ($sObject->fields)) {
        $sObject->any = $this->_convertToAny($sObject->fields);
      }
    }
    $arg->sObjects = $sObjects;
    return parent::_create($arg);
  }

  /**
   * Merge records
   *
   * @param stdclass $mergeRequest
   * @param String $type
   * @return mixed
   */
  public function merge($mergeRequest) {
    if (isset($mergeRequest->masterRecord)) {
      if (isset($mergeRequest->masterRecord->fields)) {
        $mergeRequest->masterRecord->any = $this->_convertToAny($mergeRequest->masterRecord->fields);
      }
      //return parent::merge($mergeRequest, $type);
      $arg->request = $mergeRequest;
      return $this->_merge($arg);
    }
  }

  /**
   * 
   * @param array $request
   */
  public function sendSingleEmail($request) {
    if (is_array($request)) {
      $messages = array();
      foreach ($request as $r) {
        $email = new SoapVar($r, SOAP_ENC_OBJECT, 'SingleEmailMessage', $this->namespace);
        array_push($messages, $email);
      }
      $arg->messages = $messages;
      return parent::_sendEmail($arg);
    } else {
      $backtrace = debug_backtrace();
      die('Please pass in array to this function:  '.$backtrace[0]['function']);
    }
  }

  /**
   *
   * @param array $request
   */
  public function sendMassEmail($request) {
    if (is_array($request)) {
      $messages = array();
      foreach ($request as $r) {
        $email = new SoapVar($r, SOAP_ENC_OBJECT, 'MassEmailMessage', $this->namespace);
        array_push($messages, $email);
      }
      $arg->messages = $messages;
      return parent::_sendEmail($arg);
    } else {
      $backtrace = debug_backtrace();
      die('Please pass in array to this function:  '.$backtrace[0]['function']);
    }
  }

  /**
   * Updates one or more new individual objects to your organization's data.
   * @param array sObjects    Array of sObjects
   * @return UpdateResult
   */
  public function update($sObjects) {
    $arg = new stdClass;
    foreach ($sObjects as $sObject) {
      if (isset($sObject->fields)) {
        $sObject->any = $this->_convertToAny($sObject->fields);
      }
    }
    $arg->sObjects = $sObjects;
    return parent::_update($arg);
  }

  /**
   * Creates new objects and updates existing objects; uses a custom field to
   * determine the presence of existing objects. In most cases, we recommend
   * that you use upsert instead of create because upsert is idempotent.
   * Available in the API version 7.0 and later.
   *
   * @param string $ext_Id        External Id
   * @param array  $sObjects  Array of sObjects
   * @return UpsertResult
   */
  public function upsert($ext_Id, $sObjects) {
    //		$this->_setSessionHeader();
    $arg = new stdClass;
    $arg->externalIDFieldName = new SoapVar($ext_Id, XSD_STRING, 'string', 'http://www.w3.org/2001/XMLSchema');
    foreach ($sObjects as $sObject) {
      if (isset ($sObject->fields)) {
        $sObject->any = $this->_convertToAny($sObject->fields);
      }
    }
    $arg->sObjects = $sObjects;
    return parent::_upsert($arg);
  }
  
  /**
   * @param string $fieldList
   * @param string $sObjectType
   * @param array $ids
   * @return string
   */
  public function retrieve($fieldList, $sObjectType, $ids) {
  	return $this->_retrieveResult(parent::retrieve($fieldList, $sObjectType, $ids));
  }  

  /**
   *
   * @param mixed $response
   * @return array
   */
  private function _retrieveResult($response) {
  	$arr = array();
  	if(is_array($response)) {
  		foreach($response as $r) {
  			$sobject = new SObject($r);
  			array_push($arr,$sobject);
  		};
  	}else {
  		$sobject = new SObject($response);
        array_push($arr, $sobject);
  	}
  	return $arr;
  }
  
}