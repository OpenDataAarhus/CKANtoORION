<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35.
 */

namespace AppBundle\Feed;

use Doctrine\ORM\EntityManager;
use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CityProbeReader extends BaseFeedReader
{
    // Sensor measurements
    private const FEED_PATH_CITY_PROBE = 'api/3/action/datastore_search_sql';

    private $entityManager;

    private $deviceQuery = [
        'sql' => 'SELECT * from "2b503ea0-caec-4966-a293-7c2a56e6f5ce" ORDER BY _id ASC',
    ];

    private $query = [
        'sql' => 'SELECT * from "7e85ea85-3bde-4dbf-944b-0360c6c47e3b" WHERE _id > %d ORDER BY _id ASC LIMIT 500',
    ];

    public function __construct(Client $client, Client $orionUpdater, AdapterInterface $cache, EntityManager $entityManager)
    {
        parent::__construct($client, $orionUpdater, $cache);
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeForOrganicity(): array
    {
        $maxId = $this->getMaxPointsId();

        $devices = $this->getData(self::FEED_PATH_CITY_PROBE, $this->deviceQuery);
        foreach ($devices as $sensor) {
            $sensors[$sensor->deviceid] = $sensor;
        }

        $this->query['sql'] = sprintf($this->query['sql'], $maxId);
        $records = $this->getData(self::FEED_PATH_CITY_PROBE, $this->query);

        $assets = [];

        foreach ($records as $record) {
            $asset = [
                'id' => 'urn:oc:entity:aarhus:cityprobe:'.$record->deviceid,
                'type' => 'urn:oc:entityType:iotdevice',

                'origin' => [
                    'type' => 'urn:oc:attributeType:origin',
                    'value' => 'Aarhus CityProbe data from OpenDataDK',
                    'metadata' => [
                        'urls' => [
                            'type' => 'urls',
                            'value' => 'https://portal.opendata.dk/dataset/bymiljo-aarhus-cityprobe',
                        ],
                    ],
                ],
            ];

            $asset['pointId'] = $record->_id;

            // Time
            $time = DateTime::createFromFormat('Y-m-d\TH:i:s.u', substr($record->published_at, 0, -1), new DateTimeZone('UTC'));

            $asset['TimeInstant'] = [
                'type' => 'urn:oc:attributeType:ISO8601',
                'value' => gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp()),
            ];

            // Name
            $asset['name'] = [
                'type' => 'urn:oc:attributeType:name',
                'value' => 'CityProbe Sensor '.$record->deviceid,
            ];

            // Location
            if (array_key_exists($record->deviceid, $sensors)) {
                $sensor = $sensors[$record->deviceid];
                $asset['location'] = [
                    'type' => 'geo:point',
                    'value' => $sensor->latitude.', '.$sensor->longitude,
                ];
            } else {
                $asset['location'] = [
                    'type' => 'geo:point',
                    'value' => 'null',
                ];
            }

            $asset['noise'] = [
                'type' => 'urn:oc:attributeType:noise',
                'value' => $record->noise,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:dB_SPL',
                        'value' => 'dB SPL',
                    ],
                ],
            ];

            $asset['CO'] = [
                'type' => 'urn:oc:attributeType:carbonmonoxid',
                'value' => $record->CO,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:mV',
                        'value' => 'mV',
                    ],
                ],
            ];

            $asset['temperature'] = [
                'type' => 'urn:oc:attributeType:temperature',
                'value' => $record->temperature,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:celcius',
                        'value' => 'Celcius',
                    ],
                ],
            ];

            $asset['PM10'] = [
                'type' => 'urn:oc:attributeType:PM10',
                'value' => $record->PM10,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:microgramPerCubicMeter',
                        'value' => 'μg/m3',
                    ],
                ],
            ];

            $asset['battery'] = [
                'type' => 'urn:oc:attributeType:battery',
                'value' => $record->battery,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:percent',
                        'value' => 'percent',
                    ],
                ],
            ];

            $asset['rain'] = [
                'type' => 'urn:oc:attributeType:rain',
                'value' => $record->rain,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:dB_SPL',
                        'value' => 'dB SPL',
                    ],
                ],
            ];

            $asset['humidity'] = [
                'type' => 'urn:oc:attributeType:relativeHumidity',
                'value' => $record->humidity,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:percent',
                        'value' => 'percent',
                    ],
                ],
            ];

            $asset['illuminance'] = [
                'type' => 'urn:oc:attributeType:illuminance',
                'value' => $record->illuminance,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:lux',
                        'value' => 'lux',
                    ],
                ],
            ];

            $asset['atmosphericPressure'] = [
                'type' => 'urn:oc:attributeType:atmosphericPressure',
                'value' => $record->pressure,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:hectopascal',
                        'value' => 'hectopascal',
                    ],
                ],
            ];

            $asset['PM2.5'] = [
                'type' => 'urn:oc:attributeType:PM2.5',
                'value' => $record->{'PM2.5'},
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:microgramPerCubicMeter',
                        'value' => 'μg/m3',
                    ],
                ],
            ];

            $asset['NO2'] = [
                'type' => 'urn:oc:attributeType:NO2',
                'value' => $record->NO2,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:mV',
                        'value' => 'mV',
                    ],
                ],
            ];

            $asset['firmware_version'] = [
                'type' => 'urn:oc:attributeType:firmware_version',
                'value' => $record->firmware_version,
                'metadata' => [
                    'unit' => [
                        'type' => 'urn:oc:uom:firmware_version',
                        'value' => 'firmware version',
                    ],
                ],
            ];

            $assets[] = $asset;
        }

        return $assets;
    }

    private function getMaxPointsId(): int
    {
        $maxId = $this->entityManager->createQueryBuilder()
                                     ->select('MAX(p.id)')
                                     ->from('AppBundle:CityProbePoint', 'p')
                                     ->getQuery()
                                     ->getSingleScalarResult();

        return $maxId ?? 0;
    }
}
