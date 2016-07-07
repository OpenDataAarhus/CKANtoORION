<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

$sensors_array = getGeoData();

$assets = array();

foreach ($sensors_array as $record) {

  $contextElement = new stdClass();
  $entityId = 'urn:oc:entity:aarhus:friluftsliv:shelters:' . md5($record->properties->Navn);
  $contextElement->id = $entityId;

  $contextElement->isPattern = 'false';
  $contextElement->type = 'urn:oc:entityType:shelter';

  // attributes
  $attributes = array();

  // Time
  $time = new DateTime();
  //2016-06-28T09:05:00
  $time->setTimezone(new DateTimeZone('Europe/Copenhagen'));

  $timeinstant = gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp());
  $attributes[] = array(
    'name' => 'TimeInstant',
    'type' => 'ISO8601',
    'value' => $timeinstant
  );

  // Booking
  $bookable = $record->properties->Bookbar === 'Ja' ? 'true' : 'false';
  $attributes[] = array(
    'name' => 'bookable',
    'value' => $bookable,
    'metadatas' => array(
      array(
        'name' => 'unit',
        'type' => 'urn:oc:dataType:string',
        'value' => 'urn:oc:uom:boolean'
      )
    )
  );

  // Description
  $attributes[] = array(
    'name' => 'name',
    'value' => $record->properties->Navn,
  );

  // Location
  $dokk1_LAT = $record->geometry->coordinates[1];
  $dokk1_LNG = $record->geometry->coordinates[0];
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
    'value' => 'https://www.odaa.dk/dataset/shelters-i-aarhus',
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

}

$selection = array_slice($assets, 0, 5, true);
$assets_json = json_encode($selection);

header('Content-type: application/json');
echo $assets_json;

// Support Functions

function getGeoData()
{
  $start_url = '/dataset/dc7ca516-90a3-4bea-8ceb-4bc58407d8bc/resource/4757ccaa-247f-4016-8a2b-9ca41f569db1/download/SheltersWGS84.json';

  $client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'https://www.odaa.dk',
    // You can set any number of default request options.
    'timeout' => 2.0,
  ]);

  try {
    $responce = $client->get($start_url);
  } catch (RequestException $e) {
    echo Psr7\str($e->getRequest());
    if ($e->hasResponse()) {
      echo Psr7\str($e->getResponse());
    }
    die('Network Error retrieving: https://www.odaa.dk' . $start_url);
  }

//  $content = json_decode($responce->getBody()->getContents());
  $content = $responce->getBody()->getContents();
  $content = mb_detect_encoding($content) === 'UTF-8' ? $content : utf8_encode($content);
  $content = json_decode($content);

  return $content->features;
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