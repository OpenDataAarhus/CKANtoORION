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

class Dokk1BookReturnsReader extends BaseFeedReader {
	const FEED_PATH = '/api/3/action/datastore_search?resource_id=5b9b00f9-543e-4ac0-994c-dbbc8b38e7e5';

	public function normalizeForOrganicity() {
		$sensors_array = $this->getPagedData( self::FEED_PATH );
		$assets        = [];

		foreach ( $sensors_array as $record ) {
			if ( isset( $sensors_array[ $record->_id ] ) ) {

				$asset = [
					'id'   => 'urn:oc:entity:aarhus:library:dokk1:returns:' . $record->_id,
					'type' => 'urn:oc:entityType:iotdevice',

					'origin' => [
						'type'     => 'urn:oc:attributeType:origin',
						'value'    => 'Book returns at Dokk1',
						'metadata' => [
							'urls' => [
								'type'  => 'urls',
								'value' => 'https://portal.opendata.dk/dataset/transaktionsdata-fra-aarhus-kommunes-biblioteker',
							],
						],
					],
				];

				// Time
				$time = DateTime::createFromFormat( 'Y-m-d\TH:i:s', $record->time, new DateTimeZone( 'Europe/Copenhagen' ) );

				$asset['TimeInstant'] = [
					'type'  => 'urn:oc:attributeType:ISO8601',
					'value' => gmdate( 'Y-m-d\TH:i:s.000\Z', $time->getTimestamp() ),
				];


				// Numbers
				$asset['returns:past5min'] = [
					'value'    => $record->min5,
					'metadata' => [
						'unit' => [
							'type'  => 'urn:oc:uom:count',
							'value' => 'count',
						],
					],
				];

				$asset['returns:past60min'] = [
					'value'    => $record->min60,
					'metadata' => [
						'unit' => [
							'type'  => 'urn:oc:uom:count',
							'value' => 'count',
						],
					],
				];

        $asset['returns:past24hours'] = [
          'value'    => $record->min1440,
          'metadata' => [
            'unit' => [
              'type'  => 'urn:oc:uom:count',
              'value' => 'count',
            ],
          ],
        ];

        $asset['returns:today'] = [
          'value'    => $record->today,
          'metadata' => [
            'unit' => [
              'type'  => 'urn:oc:uom:count',
              'value' => 'count',
            ],
          ],
        ];

				// Location
				$dokk1_LAT         = 56.153394;
				$dokk1_LNG         = 10.213934;
				$asset['location'] = [
					'type'  => 'geo:point',
					'value' => $dokk1_LAT . ", " . $dokk1_LNG,
				];

				$assets[] = $asset;

			} else {
				throw new Exception( 'Unmapped sensor' );
			}
		}

		return $assets;
	}

}