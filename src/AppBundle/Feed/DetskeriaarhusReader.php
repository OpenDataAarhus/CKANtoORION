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
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use ForceUTF8\Encoding;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TraceableAdapter;

class DetskeriaarhusReader extends BaseFeedReader {
	const FEED_PATH = '/api/events';

	protected function getPagedData( $next_url, $records = [] ) {
		if ( ! empty( $next_url ) ) {

			try {
				$response = $this->client->get( $next_url );
			} catch ( RequestException $e ) {
				echo Psr7\str( $e->getRequest() );
				if ( $e->hasResponse() ) {
					echo Psr7\str( $e->getResponse() );
				}
				throw new Exception( 'Network Error retrieving: ' . $next_url );
			}

			// https://github.com/8p/GuzzleBundle/issues/48
			$response->getBody()->rewind();
			$content = $response->getBody()->getContents();

			$decoded = json_decode( $content );
			$member  = $decoded->{'hydra:member'};
			$view    = $decoded->{'hydra:view'};

			$next_records = $decoded->{'hydra:member'};

			if ( ! isset( $decoded->{'hydra:view'}->{'hydra:next'} ) ) {
				return $records;
			} else {
				foreach ( $next_records as $record ) {
					$records[ $record->{'@id'} ] = $record;
				}

				return $this->getPagedData( $decoded->{'hydra:view'}->{'hydra:next'}, $records );
			}
		}

		throw new Exception( '$next_url cannot be empty' );
	}

	private function getPlaceData( $events ) {

		$places = [];

		foreach ( $events as $event ) {
			$occurences = $event->occurrences;
			$placeID    = empty( $occurences ) ? null : $occurences[0]->place->{'@id'};

			if ( $placeID && ! array_key_exists( $placeID, $places ) ) {

				$placeCache = $this->cache->getItem( 'detskeriaarhus_place' . str_replace( '/', '_', $placeID ) );
				if ( ! $placeCache->isHit() ) {

					// Place endpoint performance i poor :-(
					set_time_limit( 45 );
					try {
						$response = $this->client->get( $placeID );

						// https://github.com/8p/GuzzleBundle/issues/48
						$response->getBody()->rewind();
						$content = $response->getBody()->getContents();
						$decoded = json_decode( $content );

						$places[ $placeID ] = $decoded;

					} catch ( RequestException $e ) {

						$decoded = null;
					}


					$placeCache->set( $decoded );
					$placeCache->expiresAfter( 24 * 60 * 60 );
					$this->cache->save( $placeCache );

				} else {
					$decoded            = $placeCache->get();
					$places[ $placeID ] = $decoded;
				}

			}
		}

		return $places;
	}

	public function normalizeForOrganicity() {
		$lastSyncCache = $this->cache->getItem( 'detskeriaarhus_lastSync' );
		if ( ! $lastSyncCache->isHit() ) {
			$next_url = self::FEED_PATH . '?updatedAt[after]=' . urlencode( '2016-01-01T00:00:00+00:00' );
		} else {
			$lastSync = $lastSyncCache->get();
			$next_url = self::FEED_PATH . '?updatedAt[after]=' . urlencode( $lastSync );
		}

		$lastSync = gmdate( 'Y-m-d\TH:i:sP' );

		$events_array = $this->getPagedData( $next_url );
		$places_array = $this->getPlaceData( $events_array );
		$assets       = [];

		foreach ( $events_array as $record ) {

			$count = count( $record->occurrences );

			if ( $count > 0 ) {

				$first = $record->occurrences[0];
				$last  = $record->occurrences[ $count - 1 ];

				$placeID = $first->place->{'@id'};

				if ( array_key_exists( $placeID, $places_array ) && ! empty( $places_array[ $placeID ] && ! empty( $first->startDate ) && ! empty( $last->endDate ) ) ) {

					$pathinfo = pathinfo( $record->{'@id'} );
					$id       = $pathinfo['basename'];

					$asset = [
						'id'   => 'urn:oc:entity:aarhus:events:' . $id,
						'type' => 'urn:oc:entityType:event',

						'origin' => [
							'type'     => 'urn:oc:attributeType:origin',
							'value'    => 'Det sker i Aarhus',
							'metadata' => [
								'urls' => [
									'type'  => 'urls',
									'value' => 'https://api.detskeriaarhus.dk' . $record->{'@id'},
								],
							],
						],
					];

					$asset['name'] = [
						'type'  => 'urn:oc:attributeType:name',
						'value' => $this->sanitizeText( $record->name ),
					];

					$asset['excerpt'] = [
						'type'  => 'urn:oc:attributeType:excerpt',
						'value' => $this->sanitizeText( $record->excerpt ),
					];

					$tagsValue = $this->sanitizeText( implode( ', ', $record->tags ) );
					if ( ! empty( $tagsValue ) ) {
						$asset['excerpt']['metadata'] = [
							'tags' => [
								'type'  => 'event:tags',
								'value' => $this->sanitizeText( implode( ', ', $record->tags ) ),
							],
						];
					}

					// Time
					$startTime = strtotime( $first->startDate );
					$endTime   = strtotime( $last->endDate );

					$asset['TimeInstant'] = [
						'type'  => 'urn:oc:attributeType:ISO8601',
						'value' => gmdate( 'Y-m-d\TH:i:s.000\Z', $startTime ),
					];

					$asset['startTime'] = [
						'type'  => 'urn:oc:attributeType:ISO8601',
						'value' => gmdate( 'Y-m-d\TH:i:s.000\Z', $startTime ),
					];

					$asset['endTime'] = [
						'type'  => 'urn:oc:attributeType:ISO8601',
						'value' => gmdate( 'Y-m-d\TH:i:s.000\Z', $endTime ),
					];

					$asset['numberOfOccurrences'] = [
						'type'  => 'urn:oc:attributeType:numberOfOccurrences',
						'value' => $count,
					];

					$asset['organizer'] = [
						'type'  => 'urn:oc:attributeType:organizer',
						'value' => $this->sanitizeText( $record->organizer->name ),
					];

					$asset['imageURL'] = [
						'type'  => 'urn:oc:attributeType:imageURL',
						'value' => $this->sanitizeUrl( $record->image ),
					];

					$asset['videoURL'] = [
						'type'  => 'urn:oc:attributeType:videoURL',
						'value' => $this->sanitizeUrl( $record->videoUrl ),
					];

					$asset['URL'] = [
						'type'  => 'urn:oc:attributeType:url',
						'value' => $this->sanitizeUrl( $record->url ),
					];

					$asset['ticketURL'] = [
						'type'  => 'urn:oc:attributeType:url',
						'value' => $this->sanitizeUrl( $record->ticketPurchaseUrl ),
					];

					$asset['ticketPriceRange'] = [
						'type'  => 'urn:oc:attributeType:ticketPriceRange',
						'value' => $this->sanitizeText( $first->ticketPriceRange ),
					];

					// Location
					$place = $places_array[ $placeID ];

					$point_LAT         = $place->latitude;
					$point_LNG         = $place->longitude;
					$asset['location'] = [
						'type'  => 'geo:point',
						'value' => $point_LAT . ', ' . $point_LNG,
					];

					$asset['streetAddress'] = [
						'type'  => 'urn:oc:attributeType:streetAddress',
						'value' => $this->sanitizeText( $place->streetAddress ),
					];

					$asset['city'] = [
						'type'  => 'urn:oc:attributeType:city',
						'value' => $this->sanitizeText( $place->addressLocality ),
					];

					$asset['postalCode'] = [
						'type'  => 'urn:oc:attributeType:postalCode',
						'value' => $this->sanitizeText( $place->postalCode ),
					];

					$assets[] = $asset;

				}
			}

		}

		$lastSyncCache->set( $lastSync );
		$this->cache->save( $lastSyncCache );

		return $assets;
	}

}