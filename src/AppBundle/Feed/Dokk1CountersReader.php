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

        $asset = array(
          'id' => 'urn:oc:entity:aarhus:visitors:dokk1:fixed:camera' . $record->_id,
          'type' => 'urn:oc:entityType:iotdevice:records',

          'origin' => array(
            'type' => 'urn:oc:attributeType:origin',
            'value' => 'Visitors at Dokk1',
            'metadata' => array(
              'urls' => array(
                'type' => 'urls',
                'value' => 'https://www.odaa.dk/dataset/taellekamera-pa-dokk1'
              )
            )
          )
        );

        // attributes
        $attributes = array();

        // Time
        $time = DateTime::createFromFormat('Y-m-d\TH:i:s', $record->time);
        $time->setTimezone(new DateTimeZone('Europe/Copenhagen'));

        $asset['TimeInstant'] = array(
          'type' => 'urn:oc:attributeType:ISO8601',
          'value' => gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp())
        );



        // Numbers
        $asset['visitors:in'] = array(
          'value' => $record->in,
          'metadata' => array(
            'unit' => array(
              'type' => 'urn:oc:dataType:string',
              'value' => 'urn:oc:uom:peoplePerHour'
            )
          )
        );

        $asset['visitors:out'] = array(
          'value' => $record->out,
          'metadata' => array(
            'unit' => array(
              'type' => 'urn:oc:dataType:string',
              'value' => 'urn:oc:uom:peoplePerHour'
            )
          )
        );

        // Location
        $dokk1_LAT = 56.153394;
        $dokk1_LNG = 10.213934;
        $asset['location'] = array(
          'type' => 'geo:point',
          'value' => $dokk1_LNG . ", " . $dokk1_LAT
        );

        $assets[] = $asset;

      } else {
        throw new Exception('Unmapped sensor');
      }
    }

    return $assets;
  }

}