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

class RealTimeTrafficReader extends BaseFeedReader
{
    // Sensor list/meta data
    private const FEED_PATH_SENSORS = '/api/action/datastore_search?resource_id=c3097987-c394-4092-ad1d-ad86a81dbf37';

    // Sensor measurements
    private const FEED_PATH_TRAFFIC = '/api/action/datastore_search?resource_id=b3eeb0ff-c8a8-4824-99d6-e0a3747c8b0d';

    public function normalizeForOrganicity(): array
    {
        $sensors_array = $this->getPagedData(self::FEED_PATH_SENSORS);
        $traffic_array = $this->getPagedData(self::FEED_PATH_TRAFFIC);
        $assets = [];

        foreach ($traffic_array as $record) {
            if (isset($sensors_array[$record->_id])) {
                $record->sensor = $sensors_array[$record->_id];

                $asset = [
                    'id' => 'urn:oc:entity:aarhus:traffic:fixed:BT'.$record->_id,
                    'type' => 'urn:oc:entityType:iotdevice:traffic',

                    'origin' => [
                        'type' => 'urn:oc:attributeType:origin',
                        'value' => 'Traffic flow data from OpenDataDK',
                        'metadata' => [
                            'urls' => [
                                'type' => 'urls',
                                'value' => 'https://portal.opendata.dk/dataset/realtids-trafikdata',
                            ],
                        ],
                    ],
                ];

                // Time
                $time = DateTime::createFromFormat('Y-m-d\TH:i:s', $record->TIMESTAMP, new DateTimeZone('UTC'));

                $asset['TimeInstant'] = [
                    'type' => 'urn:oc:attributeType:ISO8601',
                    'value' => gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp()),
                ];

                // Location
                $asset['location'] = [
                    'type' => 'geo:point',
                    'value' => $record->sensor->POINT_2_LAT.', '.$record->sensor->POINT_2_LNG,
                ];

                //Speed
                $asset['speed:average'] = [
                    'type' => 'urn:oc:attributeType:speed:average',
                    'value' => (string) $record->avgSpeed,
                    'metadata' => [
                        'name' => [
                            'type' => 'urn:oc:uom:kilometrePerHour',
                            'value' => 'kilometrePerHour',
                        ],
                    ],
                ];

                $asset['vehicle:count'] = [
                    'type' => 'urn:oc:attributeType:vehicle:count',
                    'value' => (string) $record->vehicleCount,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:count',
                            'value' => 'count',
                        ],
                    ],
                ];

                $asset['time:avgMeasured'] = [
                    'type' => 'urn:oc:attributeType:time:avgMeasured',
                    'value' => (string) $record->avgMeasuredTime,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:seconds',
                            'value' => 'seconds',
                        ],
                    ],
                ];

                $asset['time:medianMeasured'] = [
                    'type' => 'urn:oc:attributeType:time:medianMeasured',
                    'value' => (string) $record->medianMeasuredTime,
                    'metadata' => [
                        'unit' => [
                            'type' => 'urn:oc:uom:seconds',
                            'value' => 'seconds',
                        ],
                    ],
                ];

                $assets[] = $asset;
            } else {
                die('Unmapped sensor');
            }
        }

        return $assets;
    }
}
