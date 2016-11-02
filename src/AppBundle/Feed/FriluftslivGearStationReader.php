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

class FriluftslivGearStationReader extends BaseFeedReader
{
  const FEED_PATH = '/dataset/cb4df027-acb2-4cbe-928a-73e58ae6caf3/resource/dbe736be-5851-4281-96c8-308602ed4250/download/GrejbaserWGS84.json';

  public function normalizeForOrganicity()
  {
    $sensors_array = $this->getGeoData(self::FEED_PATH);

    $assets = array();

    foreach ($sensors_array as $record) {

      $contextElement = new stdClass();
      $entityId = 'urn:oc:entity:aarhus:friluftsliv:gearstations:' . md5($record->properties->Navn);
      $contextElement->id = $entityId;

      $contextElement->isPattern = 'false';
      $contextElement->type = 'urn:oc:entityType:gearstation';

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
        'metadata' => array(
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
      $point_LAT = $record->geometry->coordinates[1];
      $point_LNG = $record->geometry->coordinates[0];
      $attributes[] = array(
        'name' => 'position',
        'type' => 'coords',
        'value' => $point_LAT . ',' . $point_LNG,
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
        'value' => 'https://www.odaa.dk/dataset/grejbaser-ved-aarhus-kommune',
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

    }

    return $assets;
  }

}