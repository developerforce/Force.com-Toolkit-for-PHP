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
   * @param array $sObjects    Array of one or more sObjects (up to 200) to create.
   * @param AssignmentRuleHeader $assignment_header is optional.  Defaults to NULL
   * @param MruHeader $mru_header is optional.  Defaults to NULL
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
   * @return unknown
   */
  public function merge($mergeRequest) {
    if (isset($mergeRequest->masterRecord)) {
      if (isset ($mergeRequest->masterRecord->fields)) {
        $mergeRequest->masterRecord->any = $this->_convertToAny($mergeRequest->masterRecord->fields);
      }
      //return parent::merge($mergeRequest, $type);
      $arg->request = $mergeRequest;
      return $this->_merge($arg);
    }
  }

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
   * @param AssignmentRuleHeader $assignment_header is optional.  Defaults to NULL
   * @param MruHeader $mru_header is optional.  Defaults to NULL
   * @return UpdateResult
   */
  public function update($sObjects) {
    $arg = new stdClass;
    foreach ($sObjects as $sObject) {
      if (isset ($sObject->fields)) {
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
  
  public function query($query) {
  	return new QueryResult(parent::query($query));
  	
  }
  public function queryMore($queryLocator) {
  	return new QueryResult(parent::queryMore($queryLocator));
  }
  public function retrieve($fieldList, $sObjectType, $ids) {
  	return $this->_retrieveResult(parent::retrieve($fieldList, $sObjectType, $ids));
  }  

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


class QueryResult {
  public $queryLocator;
  public $done;
  public $records;
  public $size;

  public function __construct($response) {
   	
  	$this->queryLocator = $response->queryLocator;
    $this->done = $response->done;
    $this->size = $response->size;	

    
    if($response instanceof QueryResult) {
	    $this->records = $response->records; 		
  	}
  	else {
	    $this->records = array();

	    if (isset($response->records)) {
	      if (is_array($response->records)) {
	        foreach ($response->records as $record) {
	          $sobject = new SObject($record);
	          array_push($this->records, $sobject);
	        };
	      } else {
	        $sobject = new SObject($response->records);
	        array_push($this->records, $sobject);
	      }
	    }
  	}
  }
}
/**
 * Salesforce Object
 *
 * @package SalesforceSoapClient
 */
class SObject {
  public $type;
  public $fields;
  //  public $sobject;

  public function __construct($response=NULL) {
    if (isset($response)) {
      if (isset($response->Id)) $this->Id = $response->Id[0];
      if (isset($response->type)) $this->type = $response->type;
      if (isset($response->any)) {
        try {
          //$this->fields = $this->convertFields($response->any);
          // If ANY is an object, instantiate another SObject
          if ($response->any instanceof stdClass) {
            if ($this->isSObject($response->any)) {
              $anArray = array();
              $sobject = new SObject($response->any);
               $anArray[] = $sobject;
               $this->sobjects = $anArray;
            } else {
              // this is for parent to child relationships
              $this->queryResult = new QueryResult($response->any);
            }

          } else {
            // If ANY is an array
            if (is_array($response->any)) {
              // Loop through each and perform some action.
              $anArray = array();

		// Modify the foreach to have $key=>$value
		// Added on 28th April 2008
              foreach ($response->any as $k=>$item) {
                if ($item instanceof stdClass) {
                  if ($this->isSObject($item)) {
                    $sobject = new SObject($item);
		
		// make an associative array instead of a numeric one
                    $anArray[$k] = $sobject;
                  } else {
                    // this is for parent to child relationships
                    //$this->queryResult = new QueryResult($item);
                    if (!isset($this->queryResult)) {
                      $this->queryResult = array();
                    }
                    array_push($this->queryResult, new QueryResult($item));
                  }
                } else {
                  //$this->fields = $this->convertFields($item);
                  if (!isset($fieldsToConvert)) {
                    $fieldsToConvert = $item;
                  } else {
                    $fieldsToConvert .= $item;
                  }
                }
                if (isset($fieldsToConvert)) {
		
		// If this line is commented, then the fields becomes a stdclass object and does not have the name variable
		// In this case the foreach loop on line 252 runs successfuly
                  $this->fields = $this->convertFields($fieldsToConvert);
                }
              }
              if (sizeof($anArray) > 0) {

		// To add more variables to the the top level sobject
		foreach ($anArray as $k=>$children_sobject) {
			$this->fields->$k = $children_sobject;

		}
		
		//array_push($this->fields, $anArray);
		// Uncommented on 28th April since all the sobjects have now been moved to the fields
                //$this->sobjects = $anArray;
              }

              /*
               $this->fields = $this->convertFields($response->any[0]);
               if (isset($response->any[1]->records)) {
               $anArray = array();
               if ($response->any[1]->size == 1) {
               $records = array (
               $response->any[1]->records
               );
               } else {
               $records = $response->any[1]->records;
               }
               foreach ($records as $record) {
               $sobject = new SObject($record);
               array_push($anArray, $sobject);
               }
               $this->sobjects = $anArray;
               } else {
               $anArray = array();
               $sobject = new SObject($response->any[1]);
               array_push($anArray, $sobject);
               $this->sobjects = $anArray;
               }
               */
            } else {
              $this->fields = $this->convertFields($response->any);
            }
          }
        } catch (Exception $e) {
          var_dump($e);
        }
      }
    }
  }

  /**
   * Parse the "any" string from an sObject.  First strip out the sf: and then
   * enclose string with <Object></Object>.  Load the string using
   * simplexml_load_string and return an array that can be traversed.
   */
  function convertFields($any) {
	// ereg_replace('sf:', '', $any); deprecated in PHP: 5.3.0 (use preg_replace('{sf:}', '', $any);)
	$str = preg_replace('{sf:}', '', $any);

	$array = $this->xml2array('<Object xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.$str.'</Object>',0);

	$xml = new stdClass;
        if (!count($array['Object']))
          return $xml;

	foreach ($array['Object'] as $k=>$v) {
	  $xml->$k = $v;
	}

	
    //$new_string = '<Object xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.$new_string.'</Object>';
     //$new_string = $new_string;
   //$xml = simplexml_load_string($new_string);
    return $xml;
  }

function xml2array($contents, $get_attributes=1) {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array('not found');
    }
    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create();
    xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
    xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
    xml_parse_into_struct( $parser, $contents, $xml_values );
    xml_parser_free( $parser );

    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array;

    //Go through the tags.
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = '';
        if($get_attributes) {//The second argument of the function decides this.
            $result = array();
            if(isset($value)) $result['value'] = $value;

            //Set the attributes too.
            if(isset($attributes)) {
                foreach($attributes as $attr => $val) {
                    if($get_attributes == 1) $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                    /**  :TODO: should we change the key name to '_attr'? Someone may use the tagname 'attr'. Same goes for 'value' too */
                }
            }
        } elseif(isset($value)) {
            $result = $value;
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;

            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                $current = &$current[$tag];

            } else { //There was another element with the same tag name
                if(isset($current[$tag][0])) {
                    array_push($current[$tag], $result);
                } else {
                    $current[$tag] = array($current[$tag],$result);
                }
                $last = count($current[$tag]) - 1;
                $current = &$current[$tag][$last];
            }

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;

            } else { //If taken, put all things inside a list(array)
                if((is_array($current[$tag]) and $get_attributes == 0)//If it is already an array...
                        or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
                    array_push($current[$tag],$result); // ...push the new element into that array.
                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }

    return($xml_array);
} 

  /*
   * If the stdClass has a done, we know it is a QueryResult
   */
  function isQueryResult($param) {
    return isset($param->done);
  }

  /*
   * If the stdClass has a type, we know it is an SObject
   */
  function isSObject($param) {
    return isset($param->type);
  }
}
?>