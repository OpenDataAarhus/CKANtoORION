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

class Dokk1CountersReader extends BaseFeedReader {
	const FEED_PATH = '/api/3/action/datastore_search?resource_id=b82383a4-97ec-4377-b0ea-94b2e6fe70c0';

	public function normalizeForOrganicity() {
		$sensors_array = $this->getPagedData( self::FEED_PATH );
		$assets        = [];

		foreach ( $sensors_array as $record ) {
			if ( isset( $sensors_array[ $record->_id ] ) ) {

				$asset = [
					'id'   => 'urn:oc:entity:aarhus:visitors:dokk1:fixed:camera' . $record->_id,
					'type' => 'urn:oc:entityType:iotdevice:records',

					'origin' => [
						'type'     => 'urn:oc:attributeType:origin',
						'value'    => 'Visitors at Dokk1',
						'metadata' => [
							'urls' => [
								'type'  => 'urls',
								'value' => 'https://portal.opendata.dk/dataset/taellekamera-pa-dokk1',
							],
						],
					],
				];

				// attributes
				$attributes = [];

				// Time
				$time = DateTime::createFromFormat( 'Y-m-d\TH:i:s', $record->time, new DateTimeZone( 'Europe/Copenhagen' ) );

				$asset['TimeInstant'] = [
					'type'  => 'urn:oc:attributeType:ISO8601',
					'value' => gmdate( 'Y-m-d\TH:i:s.000\Z', $time->getTimestamp() ),
				];


				// Numbers
				$asset['visitors:in'] = [
					'value'    => $record->in,
					'metadata' => [
						'unit' => [
							'type'  => 'urn:oc:dataType:string',
							'value' => 'urn:oc:uom:peoplePerHour',
						],
					],
				];

				$asset['visitors:out'] = [
					'value'    => $record->out,
					'metadata' => [
						'unit' => [
							'type'  => 'urn:oc:dataType:string',
							'value' => 'urn:oc:uom:peoplePerHour',
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