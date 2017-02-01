<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

$sensors_array = getCounterData();

$assets = array();

foreach ($sensors_array as $record) {
  if (isset($sensors_array[$record->_id])) {

    $contextElement = new stdClass();
    $entityId = 'urn:oc:entity:aarhus:visitors:dokk1:fixed:' . $record->_id;
    $contextElement->id = $entityId;

    $contextElement->isPattern = 'false';
    $contextElement->type = 'urn:oc:entityType:iotdevice:records';

    // attributes
    $attributes = array();

    // Time
    $time = DateTime::createFromFormat('Y-m-d\TH:i:s', $record->time);
    //2016-06-28T09:05:00
    $time->setTimezone(new DateTimeZone('Europe/Copenhagen'));

    $timeinstant = gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp());
    $attributes[] = array(
      'name' => 'TimeInstant',
      'type' => 'ISO8601',
      'value' => $timeinstant
    );

    // Numbers
    $attributes[] = array(
      'name' => 'visitors:in',
      'value' => $record->in,
      'metadatas' => array(
        array(
          'name' => 'unit',
          'type' => 'urn:oc:dataType:string',
          'value' => 'urn:oc:uom:peoplePerHour'
        ),
        array(
          'name' => 'description',
          'type' => 'urn:oc:dataType:string',
          // @TODO: Add proper description for how data i measured
          'value' => '@TODO'
        )
      )
    );

    $attributes[] = array(
      'name' => 'visitors:out',
      'value' => $record->out,
      'metadatas' => array(
        array(
          'name' => 'unit',
          'type' => 'urn:oc:dataType:string',
          'value' => 'urn:oc:uom:peoplePerHour'
        ),
        array(
          'name' => 'description',
          'type' => 'urn:oc:dataType:string',
          // @TODO: Add proper description for how data i measured
          'value' => '@TODO'
        )
      )
    );

    // Location
    $dokk1_LAT = 56.153394;
    $dokk1_LNG = 10.213934;
    $attributes[] = array(
      'name' => 'position',
      'type' => 'coords',
      'value' => $dokk1_LAT . ',' . $dokk1_LNG,
      'metadatas' => array(
        array(
          'name' => 'location',
          'type' => 'string',
          'value' => 'WGS84'
        )
      )
    );

    // Datasource
    $attributes[] = array(
      'name' => 'datasource',
      'type' => 'urn:oc:attributeType:datasource',
      'value' => 'https://www.odaa.dk/dataset/taellekamera-pa-dokk1',
      'metadatas' => array(
        array(
          'name' => 'datasourceExternal',
          'type' => 'urn:oc:dataType:boolean',
          'value' => 'true'
        )
      )
    );

    // Reputation
    $attributes[] = array(
      'name' => 'reputation',
      'type' => 'urn:oc:attributeType:reputation',
      'value' => '-1',
      'metadatas' => array(
        array(
          'name' => 'description',
          'type' => 'urn:oc:dataType:string',
          'value' => 'The reputation scores vary from 0 to 1. -1 means that there is not scores already calculated'
        )
      )
    );

    // Origin
    $attributes[] = array(
      'name' => 'origin',
      'type' => 'urn:oc:attributeType:origin',
      'value' => 'ODAA'
    );

    $contextElement->attributes = $attributes;
    $asset = new stdClass();
    $asset->contextElements = array($contextElement);

    $asset->updateAction = 'APPEND';

    $assets[] = $asset;

    $json = json_encode($asset);

    sendUpdate($json);
  } else {
    die('Unmapped sensor');
  }
}

$selection = array_slice($assets, 0, 5, true);
$assets_json = json_encode($selection);

header('Content-type: application/json');
echo $assets_json;

// Support Functions

function getCounterData($records = array(), $next_url = NULL)
{
  $start_url = '/api/3/action/datastore_search?resource_id=b82383a4-97ec-4377-b0ea-94b2e6fe70c0';
  $next_url = $next_url ? $next_url : $start_url;

  $client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'https://www.odaa.dk',
    // You can set any number of default request options.
    'timeout' => 2.0,
  ]);

  try {
    $response = $client->get($next_url);
  } catch (RequestException $e) {
    echo Psr7\str($e->getRequest());
    if ($e->hasResponse()) {
      echo Psr7\str($e->getResponse());
    }
    die('Network Error retrieving: https://www.odaa.dk' . $next_url);
  }

//  $body = $response->getBody();
//  $content2 = $body->getContents();

  $content = json_decode($response->getBody()->getContents());
  $next_records = $content->result->records;
  $next_url = $content->result->_links->next;

  if (empty($next_records)) {
    return $records;
  } else {
    foreach ($next_records as $record) {
      $records[$record->_id] = $record;
    }
    return getCounterData($records, $next_url);
  }
}

function sendUpdate($json)
{

  /**
   * Initialize handle and set options
   */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://192.168.50.108:1026/v1/updateContext');
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json',
    'Content-Type: application/json',
    'Fiware-Service: organicity',
    'Fiware-ServicePath: /',
    'Content-Length: ' . strlen($json)
  ));

  $result = curl_exec($ch);

  /**
   * Check for errors
   */
  if (curl_errno($ch)) {
    $result = 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
    die($result);
  } else {
    $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    switch ($returnCode) {
      case 404:
        $result = 'ERROR -> 404 Not Found';
        die($result);
        break;
      default:
        break;
    }
  }

  /**
   * Close the handle
   */
  curl_close($ch);

}

?>