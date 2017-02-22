<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35
 */

namespace AppBundle\Feed;

use Exception;
use DateTime;
use DateTimeZone;
use stdClass;

class RealTimeTrafficReader extends BaseFeedReader
{
  // Sensor list/meta data
  const FEED_PATH_SENSORS = '/api/action/datastore_search?resource_id=c3097987-c394-4092-ad1d-ad86a81dbf37';

  // Sensor measurements
  const FEED_PATH_TRAFFIC = '/api/action/datastore_search?resource_id=b3eeb0ff-c8a8-4824-99d6-e0a3747c8b0d';

  public function normalizeForOrganicity()
  {
    $sensors_array = $this->getPagedData(self::FEED_PATH_SENSORS);
    $traffic_array = $this->getPagedData(self::FEED_PATH_TRAFFIC);
    $assets = array();

    foreach ($traffic_array as $record) {
      if (isset($sensors_array[$record->_id])) {

        $record->sensor = $sensors_array[$record->_id];

        $asset = array(
          'id' => 'urn:oc:entity:aarhus:traffic:fixed:BT' . $record->_id,
          'type' => 'urn:oc:entityType:iotdevice:traffic',

          'origin' => array(
            'type' => 'urn:oc:attributeType:origin',
            'value' => 'Traffic flow data from ODAA',
            'metadata' => array(
              'urls' => array(
                'type' => 'urls',
                'value' => 'https://www.odaa.dk/dataset/realtids-trafikdata'
              )
            )
          )
        );

        // Time
        $time = DateTime::createFromFormat('Y-m-d\TH:i:s', $record->TIMESTAMP);
        $time->setTimezone(new DateTimeZone('Europe/Copenhagen'));

        $asset['TimeInstant'] = array(
          'type' => 'urn:oc:attributeType:ISO8601',
          'value' => gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp())
        );

        // Location
        $asset['location'] = array(
          'type' => 'geo:point',
          'value' => $record->sensor->POINT_2_LAT . ", " . $record->sensor->POINT_2_LNG
        );


        //Speed
        $asset['speed:average'] = array(
          'type' => 'urn:oc:attributeType:speed:average',
          'value' => strval($record->avgSpeed),
          'metadata' => array(
            'name' => array(
              'type' => 'urn:oc:dataType:string',
              'value' => 'urn:oc:uom:kilometrePerHour'
            )
          )
        );

        $assets[] = $asset;

      } else {
        die('Unmapped sensor');
      }
    }

    return $assets;
  }

}