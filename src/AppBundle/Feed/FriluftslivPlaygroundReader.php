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

class FriluftslivPlaygroundReader extends BaseFeedReader
{
    protected $feed_path = '/dataset/4c838a0e-c08f-4bdc-ad6e-44e09d4a222d/resource/14572611-44bf-4eee-9cf0-25d7f3c2eeef/download/legepladser.geojson';

    protected $id_string = 'playgrounds';
    protected $type = 'playground';
    protected $origin_value = 'Public Playgrounds in Aarhus';
    protected $origin_url = 'https://www.odaa.dk/dataset/legepladser';

    public function normalizeForOrganicity()
    {
        $odaa_data = $this->getGeoData($this->feed_path);
        $sensors_array = $odaa_data['features'];
        $last_modified = $odaa_data['Last-Modified'];
        $last_modified_timestamp = strtotime($last_modified);

        $assets = [];

        $lastSyncCache = $this->cache->getItem($this->id_string);
        if (!$lastSyncCache->isHit()) {
            $lastSync = null;
        } else {
            $lastSync = $lastSyncCache->get();
        }

        if ($lastSync < $last_modified_timestamp) {

            foreach ($sensors_array as $record) {

                $info = explode('<td>', $record->properties->Description);
                $info = explode('</td>', $info[4]);
                $info = explode('  ', $info[0]);
                $name = array_shift($info);
                $description = implode(', ', $info);

                $name = $this->sanitizeText($name);
                $description = $this->sanitizeText($description);

                $asset = [
                  'id' => 'urn:oc:entity:aarhus:friluftsliv:'.$this->id_string.':'.md5($name),
                  'type' => 'urn:oc:entityType:'.$this->type,

                  'origin' => [
                    'type' => 'urn:oc:attributeType:origin',
                    'value' => $this->origin_value,
                    'metadata' => [
                      'urls' => [
                        'type' => 'urls',
                        'value' => $this->origin_url,
                      ],
                    ],
                  ],
                ];

                // Time
                $asset['TimeInstant'] = [
                  'type' => 'urn:oc:attributeType:ISO8601',
                  'value' => gmdate('Y-m-d\TH:i:s.000\Z', $last_modified_timestamp),
                ];

                $asset['bookable'] = [
                  'type' => 'urn:oc:datatype:boolean',
                  'value' => 'false',
                ];
                $asset['name'] = [
                  'type' => 'urn:oc:attributeType:name',
                  'value' => $name,
                ];
                $asset['description'] = [
                  'type' => 'urn:oc:attributeType:description',
                  'value' => $description,
                ];

                // Location
                $point_LAT = $record->geometry->coordinates[1];
                $point_LNG = $record->geometry->coordinates[0];
                $asset['location'] = [
                  'type' => 'geo:point',
                  'value' => $point_LAT.', '.$point_LNG,
                ];

                $assets[] = $asset;

            }

            $lastSyncCache->set($last_modified_timestamp);
            $this->cache->save($lastSyncCache);
        }

        return $assets;
    }

}