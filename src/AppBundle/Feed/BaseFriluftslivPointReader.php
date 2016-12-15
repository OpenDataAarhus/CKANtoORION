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

abstract class BaseFriluftslivPointReader extends BaseFeedReader
{

  public function normalizeForOrganicity()
  {
    $odaa_data = $this->getGeoData($this->feed_path);
    $sensors_array = $odaa_data['features'];
    $last_modified = $odaa_data['Last-Modified'];
    $last_modified_timestamp = strtotime($last_modified);

    $assets = array();

    foreach ($sensors_array as $record) {

      $asset = array(
        'id' => 'urn:oc:entity:aarhus:friluftsliv:'.$this->id_string.':' . md5($record->properties->Navn),
        'type' => 'urn:oc:entityType:'.$this->type,

        'origin' => array(
          'type' => 'urn:oc:attributeType:origin',
          'value' => $this->origin_value,
          'metadata' => array(
            'urls' => array(
              'type' => 'urls',
              'value' => $this->origin_url
            )
          )
        )
      );

      // Time
      $asset['TimeInstant'] = array(
        'type' => 'urn:oc:attributeType:ISO8601',
        'value' => gmdate('Y-m-d\TH:i:s.000\Z', $last_modified_timestamp)
      );

      $asset['bookable'] = array(
        'type' => 'urn:oc:datatype:boolean',
        'value' => $record->properties->Bookbar === 'Ja' ? 'true' : 'false'
      );
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