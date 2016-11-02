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

class Dokk1CountersReader extends BaseFeedReader
{
  const FEED_PATH = '/api/3/action/datastore_search?resource_id=b82383a4-97ec-4377-b0ea-94b2e6fe70c0';

  public function normalizeForOrganicity()
  {
    $sensors_array = $this->getPagedData(self::FEED_PATH);
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
          'metadata' => array(
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
          'metadata' => array(
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
          'metadata' => array(
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
          'metadata' => array(
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
          'metadata' => array(
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
        throw new Exception('Unmapped sensor');
      }
    }

    return $assets;
  }

}