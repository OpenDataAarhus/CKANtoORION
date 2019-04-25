<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35.
 */

namespace AppBundle\Feed;

abstract class BaseFriluftslivGeoJsonReader extends BaseFeedReader
{
    public function normalizeForOrganicity()
    {
        $open_data_dk_data = $this->getGeoData($this->feed_path);
        $sensors_array = $open_data_dk_data['features'];
        $last_modified = $open_data_dk_data['Last-Modified'];
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
                $asset = [
                    'id' => 'urn:oc:entity:aarhus:friluftsliv:'.$this->id_string.':'.md5($record->properties->Navn),
                    'type' => 'urn:oc:entityType:'.$this->sanitizeText($this->type),

                    'origin' => [
                        'type' => 'urn:oc:attributeType:origin',
                        'value' => $this->sanitizeText($this->origin_value),
                        'metadata' => [
                            'urls' => [
                                'type' => 'urls',
                                'value' => $this->sanitizeUrl($this->origin_url),
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
                    'value' => 'Ja' === $record->properties->Bookbar ? 'true' : 'false',
                ];
                $asset['name'] = [
                    'type' => 'urn:oc:attributeType:name',
                    'value' => $this->sanitizeText($record->properties->Navn),
                ];

                // Location

                $asset['location'] = [
                    'type' => 'geo:json',
                    'value' => $record->geometry,
                ];

                $assets[] = $asset;
            }

            $lastSyncCache->set($last_modified_timestamp);
            $this->cache->save($lastSyncCache);
        }

        return $assets;
    }
}
