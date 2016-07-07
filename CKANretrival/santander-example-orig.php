<?php

$url = file_get_contents("http://datos.santander.es/api/rest/datasets/lineas_bus.json");

$arr = json_decode($url, TRUE);
foreach ($arr[resources] as $element) {


  $mod = $element['dc:modified'];
  $id = $element['ayto:numero'];


  $name = $element['dc:name'];
  $data_attributes = array();
  $data_static_attributes = array();


  $timeinstant = gmdate("Y-m-d\TH:i:s.000\Z", strtotime($mod));
  $data_attributes[] = array(
    "name" => "TimeInstant",
    "type" => "ISO8601",
    "value" => $timeinstant
  );
  $data_attributes[] = array(
    "name" => "bus:line:name",
    "type" => "urn:oc:attributeType:busLine:name",
    "value" => $name
  );


  $entityId = "urn:oc:entity:santander:publictransport:bus:line:" . $id;
  $data_contextElements = array();
  $data_contextElements[] = array(
    "id" => $entityId,
    "type" => "urn:oc:entityType:busLine",
    "isPattern" => "false",
    "attributes" => $data_attributes
  );
  $data = array(
    "contextElements" => $data_contextElements,
    "updateAction" => "APPEND"
  );
  $data_string = json_encode($data);
  //echo $data_string;
  sleep(0.1);
  sendUpdate($data_string);

}

function sendUpdate($jsonfile) {

  echo $jsonfile;

  define('POST_URL', 'http://192.168.50.108:1026/v1/updateContext');

  /**
   * Initialize handle and set options
   */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, POST_URL);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonfile);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json',
    'Content-Type: application/json',
    'Fiware-Service: organicity',
    'Fiware-ServicePath: /',
    'Content-Length: ' . strlen($jsonfile)
  ));

  /**
   * Execute the request and also time the transaction
   */
  $start = array_sum(explode(' ', microtime()));

  $result = curl_exec($ch);
  echo curl_error($ch);

//  echo $result;
  var_dump($result);

//$stop = array_sum(explode(' ', microtime()));
  //   $totalTime = $stop - $start;

  /**
   * Check for errors
   */
  if (curl_errno($ch)) {
    $result = 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
  }
  else {
    $returnCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    switch ($returnCode) {
      case 404:
        $result = 'ERROR -> 404 Not Found';
        break;
      default:
        //echo $returnCode;
        break;
    }
  }

  /**
   * Close the handle
   */
  curl_close($ch);


  /**
   * Output the results and time
   */
  //echo 'Total time for request: ' . $totalTime . "\n";


}