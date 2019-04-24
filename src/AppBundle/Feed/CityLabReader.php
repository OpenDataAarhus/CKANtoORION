<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35.
 */

namespace AppBundle\Feed;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CityLabReader extends BaseFeedReader
{
    // Sensor measurements
    private const FEED_PATH_CITYLAB = '/api/action/datastore_search_sql';

    private $entityManager;

    // The 3 sensors have 3 + 6 + 10 = 19 measurements that get logged in individual rows.
    // 570 / 19 = 30 measurements.
    private $query = [
        'sql' => 'SELECT * from "c65b055d-a020-4871-ab51-bdbc3fd73fd8" WHERE _id > %d ORDER BY _id ASC LIMIT 570',
    ];

    // Geo coding
    private const SENSOR_ARRAY_LOCATIONS = [
        '0004A30B001E1694' => [56.1571711, 10.213521700000001],
        '0004A30B001E8EA2' => [56.1571711, 10.213521700000001],
        '0004A30B001E307C' => [56.1571711, 10.213521700000001],
    ];

    public function __construct(Client $client, Client $orionUpdater, AdapterInterface $cache, EntityManager $entityManager)
    {
        parent::__construct($client, $orionUpdater, $cache);
        $this->entityManager = $entityManager;
    }

    public function normalizeForOrganicity()
    {
        $maxId = $this->getMaxPointsId();

        $this->query['sql'] = sprintf($this->query['sql'], $maxId);
        $records = $this->getData(self::FEED_PATH_CITYLAB, $this->query);

        $sensors = [];
        foreach ($records as $record) {
            $sensors[$record->sensor][$record->time][$record->type] = $record;
        }

        $assets = [];

        foreach ($sensors as $id => $timestamps) {
            foreach ($timestamps as $timestamp => $sensor) {
                if (array_key_exists($id, self::SENSOR_ARRAY_LOCATIONS)) {
                    $asset = [
                        'id' => 'urn:oc:entity:aarhus:citylab:'.$id,
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
                        'value' => 'CityLab Sensor '.$id,
                    ];

                    // Location
                    $location = self::SENSOR_ARRAY_LOCATIONS[$id];
                    $asset['location'] = [
                        'type' => 'geo:point',
                        'value' => $location[0].', '.$location[1],
                    ];

                    $this->addReading($asset, $record);
                    $this->setPointId($record->_id, $asset);

                    while ($sensor) {
                        $record = array_pop($sensor);
                        $this->addReading($asset, $record);
                        $this->setPointId($record->_id, $asset);
                    }

                    $assets[] = $asset;
                }
            }
        }

        return $assets;
    }

    private function setPointId(int $pointId, &$asset): void
    {
        if (array_key_exists('pointId', $asset)) {
            if ($asset['pointId'] < $pointId) {
                $asset['pointId'] = $pointId;
            }
        } else {
            $asset['pointId'] = $pointId;
        }
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
                            'value' => 'celsius',
                        ],
                    ],
                ];
                break;
            case 'rain':
                $asset['rainfall'] = [
                    'type' => 'urn:oc:attributeType:rainfall',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:millimetrePerHour',
                            'value' => 'millimetrePerHour',
                        ],
                    ],
                ];
                break;
            case 'charging_power':
                $asset['chargingPower'] = [
                    'type' => 'urn:oc:attributeType:chargingPower',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:milliampere',
                            'value' => 'milliampere',
                        ],
                    ],
                ];
                break;
            case 'wind_vane':
                $asset['windDirection'] = [
                    'type' => 'urn:oc:attributeType:windDirection',
                    'value' => $this->mapWindDirection($record->value),
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:compassPoint',
                            'value' => 'compassPoint',
                        ],
                    ],
                ];
                break;
            case 'wind_speed':
                $asset['windSpeed'] = [
                    'type' => 'urn:oc:attributeType:windSpeed',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:kilometrePerHour',
                            'value' => 'kilometrePerHour',
                        ],
                    ],
                ];
                break;
            case 'battery':
                $asset['batteryLevel'] = [
                    'type' => 'urn:oc:attributeType:batteryLevel',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:percent',
                            'value' => 'percent',
                        ],
                    ],
                ];
                break;
            case 'solar_radiation':
                $asset['sunlightPAR'] = [
                    'type' => 'urn:oc:attributeType:sunlightPAR',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:microMolesPerM2PerSecond',
                            'value' => 'microMolesPerM2PerSecond',
                        ],
                    ],
                ];
                break;
            case 'lux':
                $asset['daylight'] = [
                    'type' => 'urn:oc:attributeType:daylight',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:lux',
                            'value' => 'lux',
                        ],
                    ],
                ];
                break;
            case 'pressure':
                $asset['atmosphericPressure'] = [
                    'type' => 'urn:oc:attributeType:atmosphericPressure',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:pascal',
                            'value' => 'pascal',
                        ],
                    ],
                ];
                break;
            case 'humidity':
                $asset['outsideHumidity'] = [
                    'type' => 'urn:oc:attributeType:relativeHumidity',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:percent',
                            'value' => 'percent',
                        ],
                    ],
                ];
                break;
            case 'air_temperature':
                $asset['outsideTemperature'] = [
                    'type' => 'urn:oc:attributeType:temperature:ambient',
                    'value' => $record->value,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:celsius',
                            'value' => 'celsius',
                        ],
                    ],
                ];
                break;
            case 'distance_to_water':
                $asset['heightAboveMeanSeaLevel'] = [
                    'type' => 'urn:oc:attributeType:temperature:heightAboveMeanSeaLevel',
                    'value' => $record->value - 196,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:centimetre',
                            'value' => 'centimetre',
                        ],
                    ],
                ];
                break;
            default:
                die('Unmapped record');
        }
    }

    private function mapWindDirection($int): string
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

    private function getMaxPointsId(): int
    {
        $maxId = $this->entityManager->createQueryBuilder()
                                     ->select('MAX(p.id)')
                                     ->from('AppBundle:CityLabPoint', 'p')
                                     ->getQuery()
                                     ->getSingleScalarResult();

        return $maxId ?? 0;
    }
}
