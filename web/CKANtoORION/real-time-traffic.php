<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

$sensors_array = getTrafficSensors();
$traffic_array = getTrafficData();

$assets = array();

foreach ($traffic_array as $record) {
  if (isset($sensors_array[$record->_id])) {

    $record->sensor = $sensors_array[$record->_id];

    $contextElement = new stdClass();
    $entityId = 'urn:oc:entity:aarhus:traffic:fixed:' . $record->_id;
    $contextElement->id = $entityId;

    $contextElement->isPattern = 'false';
    $contextElement->type = 'urn:oc:entityType:iotdevice:traffic';

    // attributes
    $attributes = array();

    // Time
    $time = DateTime::createFromFormat('Y-m-d\TH:i:s', $record->TIMESTAMP);
    //2016-06-28T09:05:00
    $time->setTimezone(new DateTimeZone('Europe/Copenhagen'));

    $timeinstant = gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp());
    $attributes[] = array(
      'name' => 'TimeInstant',
      'type' => 'ISO8601',
      'value' => $timeinstant
    );

    // Location
    $location = isset($record->sensor->POINT_2_STREET) ? $record->sensor->POINT_2_STREET : '';
    $attributes[] = array(
      'name' => 'position',
      'type' => 'coords',
      'value' => $record->sensor->POINT_2_LAT . ',' . $record->sensor->POINT_2_LNG,
      'metadatas' => array(
        array(
          'name' => 'location',
          'type' => 'string',
          'value' => 'WGS84'
        )
      )
    );

    // Speed
    $attributes[] = array(
      'name' => 'speed:average',
      'type' => 'urn:oc:attributeType:speed:average',
      'value' => strval($record->avgSpeed),
      'metadatas' => array(
        array(
          'name' => 'unit',
          'type' => 'urn:oc:dataType:string',
          'value' => 'urn:oc:uom:kilometrePerHour'
        ),
        array(
          'name' => 'description',
          'type' => 'urn:oc:dataType:string',
          // @TODO: Add proper description for how data i measured
          'value' => '@TODO'
        )
      )
    );

    // Datasource
    $attributes[] = array(
      'name' => 'datasource',
      'type' => 'urn:oc:attributeType:datasource',
      'value' => 'https://www.odaa.dk/dataset/realtids-trafikdata',
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

function getTrafficData($traffic = array(), $next_url = NULL)
{
  $start_url = '/api/action/datastore_search?resource_id=b3eeb0ff-c8a8-4824-99d6-e0a3747c8b0d';
  $next_url = $next_url ? $next_url : $start_url;

  $client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'https://www.odaa.dk',
    // You can set any number of default request options.
    'timeout' => 2.0,
  ]);

  try {
    $responce = $client->get($next_url);
  } catch (RequestException $e) {
    echo Psr7\str($e->getRequest());
    if ($e->hasResponse()) {
      echo Psr7\str($e->getResponse());
    }
    die('Network Error retrieving: https://www.odaa.dk' . $next_url);
  }

  $content = json_decode($responce->getBody()->getContents());
  $records = $content->result->records;
  $next_url = $content->result->_links->next;

  if (empty($records)) {
    return $traffic;
  } else {
    foreach ($records as $record) {
      $traffic[$record->_id] = $record;
    }
    return getTrafficData($traffic, $next_url);
  }
}

function getTrafficSensors($sensors = array(), $next_url = NULL)
{
  $start_url = '/api/action/datastore_search?resource_id=c3097987-c394-4092-ad1d-ad86a81dbf37';
  $next_url = $next_url ? $next_url : $start_url;

  $client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'https://www.odaa.dk',
    // You can set any number of default request options.
    'timeout' => 2.0,
  ]);

  try {
    $responce = $client->get($next_url);
  } catch (RequestException $e) {
    echo Psr7\str($e->getRequest());
    if ($e->hasResponse()) {
      echo Psr7\str($e->getResponse());
    }
    die('Network Error retrieving: https://www.odaa.dk' . $next_url);
  }

  $content = json_decode($responce->getBody()->getContents());
  $records = $content->result->records;
  $next_url = $content->result->_links->next;

  if (empty($records)) {
    return $sensors;
  } else {
    foreach ($records as $record) {
      $sensors[$record->_id] = $record;
    }
    return getTrafficSensors($sensors, $next_url);
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