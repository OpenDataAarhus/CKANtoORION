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

class FriluftslivFitnessGymReader extends BaseFeedReader
{
  const FEED_PATH = '/dataset/ca1b668e-71d6-4890-b1d2-b222c89ea762/resource/194e7fad-907c-4271-9a55-55fe8f296104/download/FitnessidetfriWGS84.json';

  public function normalizeForOrganicity()
  {
    $odaa_data = $this->getGeoData(self::FEED_PATH);
    $sensors_array = $odaa_data['features'];
    $last_modified = $odaa_data['Last-Modified'];
    $last_modified_timestamp = strtotime($last_modified);

    $assets = array();

    foreach ($sensors_array as $record) {

      $asset = array(
        'id' => 'urn:oc:entity:aarhus:friluftsliv:fitness:' . md5($record->properties->Navn),
        'type' => 'urn:oc:entityType:fitnessspot',

        'origin' => array(
          'type' => 'urn:oc:attributeType:origin',
          'value' => 'Public Firepits from Friluftliv Aarhus',
          'metadata' => array(
            'urls' => array(
              'type' => 'urls',
              'value' => 'https://www.odaa.dk/dataset/balpladser-i-aarhus'
            )
          )
        )
      );

      // Time
      $asset['TimeInstant'] = array(
        'type' => 'urn:oc:attributeType:ISO8601',
        'value' => gmdate('Y-m-d\TH:i:s.000\Z', $last_modified_timestamp)
      );

      // Booking
      $asset['bookable'] = array(
        'type' => 'urn:oc:datatype:boolean',
        'value' => $record->properties->Bookbar === 'Ja' ? 'true' : 'false'
      );

      // Name
      $asset['name'] = array(
        'type' => 'urn:oc:attributeType:name',
        'value' => $record->properties->Navn
      );

      // Location
      $point_LAT = $record->geometry->coordinates[1];
      $point_LNG = $record->geometry->coordinates[0];
      $asset['location'] = array(
        'type' => 'geo:point',
        'value' => $point_LNG . ', ' . $point_LAT
      );

      $assets[] = $asset;

    }

    return $assets;
  }

}