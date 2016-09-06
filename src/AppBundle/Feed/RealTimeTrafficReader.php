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

      } else {
        die('Unmapped sensor');
      }
    }

    return $assets;
  }

}