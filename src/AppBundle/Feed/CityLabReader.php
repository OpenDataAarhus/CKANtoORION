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

class CityLabReader extends BaseFeedReader
{
  // Sensor measurements
  const FEED_PATH_CITYLAB = '/api/action/datastore_search_sql';
  private $query = [
    'sql' => 'SELECT * from "c65b055d-a020-4871-ab51-bdbc3fd73fd8" ORDER BY time DESC LIMIT 19'
  ];

  // Geo coding
  private $SENSOR_ARRAY_LOCATIONS = [
    '0004A30B001E1694' => [56.1571711, 10.213521700000001],
    '0004A30B001E8EA2' => [56.1571711, 10.213521700000001],
    '0004A30B001E307C' => [56.1571711, 10.213521700000001],
  ];

  public function normalizeForOrganicity()
  {
    $result = $this->getData(self::FEED_PATH_CITYLAB, $this->query);

    foreach ($result as $sensor) {
      $sensors[$sensor->sensor][$sensor->type] = $sensor;
    }

    $assets = [];

    foreach ($sensors as $id => $sensor) {
      if (array_key_exists($id, $this->SENSOR_ARRAY_LOCATIONS)) {
        $asset = [
          'id' => 'urn:oc:entity:aarhus:citylab:' . $id,
          'type' => 'urn:oc:entityType:iotdevice',

          'origin' => [
            'type' => 'urn:oc:attributeType:origin',
            'value' => 'Aarhus CityLab data from OpenDataDK',
            'metadata' => [
              'urls' => [
                'type' => 'urls',
                'value' => 'https://portal.opendata.dk/dataset/sensordata',
              ],
            ],
          ],
        ];

        $record = array_pop($sensor);

        // Time
        $time = DateTime::createFromFormat('Y-m-d\TH:i:s.u', $record->time, new DateTimeZone('UTC'));

        $asset['TimeInstant'] = [
          'type' => 'urn:oc:attributeType:ISO8601',
          'value' => gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp()),
        ];

        // Name
        $asset['name'] = [
          'type' => 'urn:oc:attributeType:name',
          'value' => 'CityLab Sensor ' . $id,
        ];

        // Location
        $location = $this->SENSOR_ARRAY_LOCATIONS[$id];
        $asset['location'] = [
          'type' => 'geo:point',
          'value' => $location[0] . ', ' . $location[1],
        ];

        $this->addReading($asset, $record);

        while ($sensor) {
          $this->addReading($asset, array_pop($sensor));
        }

        $assets[] = $asset;

      }
    }

    return $assets;
  }


  private function addReading(&$asset, $record)
  {
    switch ($record->type) {
      case 'water_temperature':
        $asset['waterTemperature'] = [
          'type' => 'urn:oc:attributeType:waterTemperature',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:celsius',
              'value' => 'celsius'
            ]
          ]
        ];
        break;
      case 'rain':
        $asset['rainfall'] = [
          'type' => 'urn:oc:attributeType:rainfall',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:millimetrePerHour',
              'value' => 'millimetrePerHour'
            ]
          ]
        ];
        break;
      case 'charging_power':
        $asset['chargingPower'] = [
          'type' => 'urn:oc:attributeType:chargingPower',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:milliampere',
              'value' => 'milliampere'
            ]
          ]
        ];
        break;
      case 'wind_vane':
        $asset['windDirection'] = [
          'type' => 'urn:oc:attributeType:windDirection',
          'value' => $this->mapWindDirection($record->value),
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:compassPoint',
              'value' => 'compassPoint'
            ]
          ]
        ];
        break;
      case 'wind_speed':
        $asset['windSpeed'] = [
          'type' => 'urn:oc:attributeType:windSpeed',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:kilometrePerHour',
              'value' => 'kilometrePerHour'
            ]
          ]
        ];
        break;
      case 'battery':
        $asset['batteryLevel'] = [
          'type' => 'urn:oc:attributeType:batteryLevel',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:percent',
              'value' => 'percent'
            ]
          ]
        ];
        break;
      case 'solar_radiation':
        $asset['sunlightPAR'] = [
          'type' => 'urn:oc:attributeType:sunlightPAR',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:microMolesPerM2PerSecond',
              'value' => 'microMolesPerM2PerSecond'
            ]
          ]
        ];
        break;
      case 'lux':
        $asset['daylight'] = [
          'type' => 'urn:oc:attributeType:daylight',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:lux',
              'value' => 'lux'
            ]
          ]
        ];
        break;
      case 'pressure':
        $asset['atmosphericPressure'] = [
          'type' => 'urn:oc:attributeType:atmosphericPressure',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:pascal',
              'value' => 'pascal'
            ]
          ]
        ];
        break;
      case 'humidity':
        $asset['outsideHumidity'] = [
          'type' => 'urn:oc:attributeType:relativeHumidity',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:percent',
              'value' => 'percent'
            ]
          ]
        ];
        break;
      case 'air_temperature':
        $asset['outsideTemperature'] = [
          'type' => 'urn:oc:attributeType:temperature:ambient',
          'value' => $record->value,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:celsius',
              'value' => 'celsius'
            ]
          ]
        ];
        break;
      case 'distance_to_water':
        $asset['heightAboveMeanSeaLevel'] = [
          'type' => 'urn:oc:attributeType:temperature:heightAboveMeanSeaLevel',
          'value' => $record->value - 196,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:centimetre',
              'value' => 'centimetre'
            ]
          ]
        ];
        break;
      default:
        die( 'Unmapped record' );
    }
  }

  private function mapWindDirection($int)
  {
    switch ($int) {
      case 0:
        return 'W';
      case 1:
        return 'WSW';
      case 2:
        return 'SW';
      case 3:
        return 'SSW';
      case 4:
        return 'S';
      case 5:
        return 'SSE';
      case 6:
        return 'SE';
      case 7:
        return 'ESE';
      case 8:
        return 'E';
      case 9:
        return 'ENE';
      case 10:
        return 'NE';
      case 11:
        return 'NNE';
      case 12:
        return 'N';
      case 13:
        return 'NNW';
      case 14:
        return 'NW';
      case 15:
        return 'WNW';
      default:
        return 'UNKNOWN';
    }
  }

}