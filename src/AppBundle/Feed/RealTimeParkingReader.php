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

class RealTimeParkingReader extends BaseFeedReader
{
  // Sensor measurements
  const FEED_PATH_PARKING = '/api/action/datastore_search?resource_id=2a82a145-0195-4081-a13c-b0e587e9b89c';

  // Geo coding
  private $PARKING_GARAGE_LOCATIONS = [
    'NORREPORT' => [56.161887, 10.212919],
    'SCANDCENTER' => [56.151753, 10.198338],
    'BRUUNS' => [56.149243, 10.204582],
    'MAGASIN' => [56.156677, 10.204926],
    'KALKVAERKSVEJ' => [56.1491498, 10.210491],
    'SALLING' => [56.154418, 10.208251],
    'Navitas' => [56.158705, 10.215408],
    'NewBusgadehuset' => [56.155099, 10.205934],
    'DOKK1' => [56.153366, 10.213680],
  ];

  private $PARKING_GARAGE_NAMES = [
    'NORREPORT' => 'Parkeringshus Nørreport Aarhus',
    'SCANDCENTER' => 'Scandinavian Center Aarhus Parkeringshus',
    'BRUUNS' => 'Bruuns Galleri parkeringshus',
    'MAGASIN' => 'Q-Park Magasin Aarhus',
    'KALKVAERKSVEJ' => 'Kalkværksvej Parkering Aarhus',
    'SALLING' => 'Salling Parkeringshus',
    'Navitas' => 'Navitas - Aarhus Havn',
    'NewBusgadehuset' => 'Busgaden Parkeringshus',
    'DOKK1' => 'DOKK1 Parkering',
  ];

  public function normalizeForOrganicity()
  {
    $parking_array = $this->getPagedData(self::FEED_PATH_PARKING);

    foreach ($parking_array as $record) {
      $parking_array_keyed[$record->garageCode] = $record;
    }

    // 'DOKK1' contains no real data, 'Urban Level...' combined is current actual data for Dokk1
    $parking_array_keyed['DOKK1']->vehicleCount = $parking_array_keyed['Urban Level 1']->vehicleCount;
    $parking_array_keyed['DOKK1']->totalSpaces = $parking_array_keyed['Urban Level 1']->totalSpaces;

    $parking_array_keyed['DOKK1']->vehicleCount += $parking_array_keyed['Urban Level 2+3']->vehicleCount;
    $parking_array_keyed['DOKK1']->totalSpaces += $parking_array_keyed['Urban Level 2+3']->totalSpaces;

    $assets = [];

    foreach ($parking_array_keyed as $record) {
      if (array_key_exists($record->garageCode, $this->PARKING_GARAGE_LOCATIONS)) {
        $asset = [
          'id' => 'urn:oc:entity:aarhus:parking:garage:' . $record->_id . '-' . $record->garageCode,
          'type' => 'urn:oc:entityType:parkingGarage',

          'origin' => [
            'type' => 'urn:oc:attributeType:origin',
            'value' => 'Parking space data from OpenDataDK',
            'metadata' => [
              'urls' => [
                'type' => 'urls',
                'value' => 'https://portal.opendata.dk/dataset/parkeringshuse-i-aarhus',
              ],
            ],
          ],
        ];

        // Time
        $time = DateTime::createFromFormat('Y/m/d H:i:s', $record->date, new DateTimeZone('Europe/Copenhagen'));

        $asset['TimeInstant'] = [
          'type' => 'urn:oc:attributeType:ISO8601',
          'value' => gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp()),
        ];

        // Name
        $asset['name'] = [
          'type' => 'urn:oc:attributeType:name',
          'value' =>  $this->PARKING_GARAGE_NAMES[$record->garageCode],
        ];

        // Location
        $location = $this->PARKING_GARAGE_LOCATIONS[$record->garageCode];
        $asset['location'] = [
          'type' => 'geo:point',
          'value' => $location[0] . ', ' . $location[1],
        ];

        // Free spots
        $asset['extraSpotNumber'] = [
          'type' => 'urn:oc:attributeType:extraSpotNumber',
          'value' => $record->totalSpaces - $record->vehicleCount,
        ];

        // Total spots
        $asset['totalSpotNumber'] = [
          'type' => 'urn:oc:attributeType:totalSpotNumber',
          'value' => $record->totalSpaces,
        ];

        $assets[] = $asset;

      }
    }

    return $assets;
  }

}