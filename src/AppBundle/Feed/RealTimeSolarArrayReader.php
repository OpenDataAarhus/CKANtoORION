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

class RealTimeSolarArrayReader extends BaseFeedReader
{
    // Sensor measurements
    const FEED_PATH_SOLAR = '/api/action/datastore_search?resource_id=251528ca-8ec9-4b70-9960-83c4d0c4e7b6';

    // Geo coding
    private $SOLAR_ARRAY_LOCATIONS = [
    22224 => [56.2534933, 10.149014099999931],
    15523 => [56.153366, 10.213680],
    16017 => [56.15936499999999, 10.156983999999966],
    16022 => [56.1691346, 10.163163700000041],
    16008 => [56.1161969, 10.178247400000032],
    16018 => [56.11519000000001, 10.200594000000024],
    16019 => [56.21895360000001, 10.158211199999982],
    16010 => [56.068882, 10.156938999999966],
    16009 => [56.0390947, 10.198201899999958],
    16020 => [56.118426, 10.178063899999984],
    16025 => [56.21171700000001, 10.283210899999972],
    16016 => [56.111265, 10.149429999999938],
    16023 => [56.21178399999999, 10.02361099999996],
    16024 => [56.1043446, 10.207472000000053],
    16015 => [56.2266037, 10.299391700000001],
    16011 => [56.27160199999999, 10.308420999999953],
    16021 => [56.174237, 10.114946000000032],
    16014 => [56.042567, 10.084386999999992],
    16012 => [56.1894995, 10.11318540000002],
    16013 => [56.129404, 10.15640600000006],
    22229 => [56.12756109999999, 10.164483399999995],
  ];

    private $SOLAR_ARRAY_NAMES = [
    22224 => 'Bakkegårdsskolen',
    15523 => 'Dokk1',
    16017 => 'Gammelgårdskolen',
    16022 => 'Hasle Skole',
    16008 => 'Holme Skole',
    16018 => 'Kragelundskolen',
    16019 => 'Lisbjergskolen',
    16010 => 'Mårslet Skole',
    16009 => 'Malling Skole',
    16020 => 'Rundhøjskolen',
    16025 => 'Sølystskolen',
    16016 => 'Søndervangskolen',
    16023 => 'Sabro-Korsvejskolen',
    16024 => 'Skåde Skole',
    16015 => 'Skæring Skole',
    16011 => 'Skødstrup Skole',
    16021 => 'Skjoldhøjskolen',
    16014 => 'Solbjergskolen',
    16012 => 'Tilst Skole',
    16013 => 'Vestergårdskolen',
    22229 => 'Viby Skole',
  ];

    public function normalizeForOrganicity()
    {
        $solar_array = $this->getPagedData(self::FEED_PATH_SOLAR);
        $assets = [];

        foreach ($solar_array as $record) {
            if (array_key_exists($record->sid, $this->SOLAR_ARRAY_LOCATIONS)) {
                $asset = [
          'id' => 'urn:oc:entity:aarhus:solar:fixed:'.$record->sid,
          'type' => 'urn:oc:entityType:solararray',

          'origin' => [
            'type' => 'urn:oc:attributeType:origin',
            'value' => 'Solar Array production data from OpenDataDK',
            'metadata' => [
              'urls' => [
                'type' => 'urls',
                'value' => 'https://portal.opendata.dk/dataset/solcelleanlaeg',
              ],
            ],
          ],
        ];

                // Time
                $time = DateTime::createFromFormat('Y-m-d\TH:i:s', $record->time, new DateTimeZone('Europe/Copenhagen'));

                $asset['TimeInstant'] = [
          'type' => 'urn:oc:attributeType:ISO8601',
          'value' => gmdate('Y-m-d\TH:i:s.000\Z', $time->getTimestamp()),
        ];

                // Name
                $asset['name'] = [
          'type' => 'urn:oc:attributeType:name',
          'value' => $this->SOLAR_ARRAY_NAMES[$record->sid].' solcelleanlæg',
        ];

                // Location
                $location = $this->SOLAR_ARRAY_LOCATIONS[$record->sid];
                $asset['location'] = [
          'type' => 'geo:point',
          'value' => $location[0].', '.$location[1],
        ];

                // current - den aktuelle produktion i W
                $asset['currentProduction'] = [
          'type' => 'urn:oc:attributeType:currentProduction',
          'value' => $record->current,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:watt',
              'value' => 'watt',
            ],
          ],
        ];

                // currentmax - dagens maksimum
                $asset['dailyMaxProduction'] = [
          'type' => 'urn:oc:attributeType:dailyMaxProduction',
          'value' => $record->currentmax,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:watt',
              'value' => 'watt',
            ],
          ],
        ];

                // daily - dagens produktion i Wh
                $asset['dailyProduction'] = [
          'type' => 'urn:oc:attributeType:dailyProduction',
          'value' => $record->daily,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:watt-hour',
              'value' => 'watt-hour',
            ],
          ],
        ];

                // total - produktion siden start i Wh
                $asset['totalProduction'] = [
          'type' => 'urn:oc:attributeType:totalProduction',
          'value' => $record->total,
          'metadata' => [
            'unit' => [
              'type' => 'urn:oc:uom:watt-hour',
              'value' => 'watt-hour',
            ],
          ],
        ];

                $assets[] = $asset;
            }
        }

        return $assets;
    }
}
