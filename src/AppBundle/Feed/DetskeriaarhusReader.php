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

class DetskeriaarhusReader
{
  const FEED_PATH = '/api/events';

  private $detskeriaarhusClient;
  private $orionUpdater;
  private $cache;

  public function __construct(Client $detskeriaarhusClient, Client $orionUpdater, TraceableAdapter $cache)
  {
    $this->detskeriaarhusClient = $detskeriaarhusClient;
    $this->orionUpdater = $orionUpdater;
    $this->cache = $cache;
  }

  protected function getPagedData($next_url, $records = array())
  {
    if (!empty($next_url)) {

      $client = $this->detskeriaarhusClient;

      try {
        $response = $client->get($next_url);
      } catch (RequestException $e) {
        echo Psr7\str($e->getRequest());
        if ($e->hasResponse()) {
          echo Psr7\str($e->getResponse());
        }
        throw new Exception('Network Error retrieving: ' . $next_url);
      }

      // https://github.com/8p/GuzzleBundle/issues/48
      $response->getBody()->rewind();
      $content = $response->getBody()->getContents();

      $decoded = json_decode($content);
      $member = $decoded->{'hydra:member'};
      $view = $decoded->{'hydra:view'};

      $next_records = $decoded->{'hydra:member'};

      if (!isset($decoded->{'hydra:view'}->{'hydra:next'}) || $decoded->{'hydra:view'}->{'hydra:next'} == '/api/events?page=3') {
        return $records;
      } else {
        foreach ($next_records as $record) {
          $records[$record->{'@id'}] = $record;
        }
        return $this->getPagedData($decoded->{'hydra:view'}->{'hydra:next'}, $records);
      }
    }

    throw new Exception('$next_url cannot be empty');
  }

  private function getPlaceData($events)
  {

    $client = $this->detskeriaarhusClient;
    $places = array();

    foreach ($events as $event) {
      $occurences = $event->occurrences;
      $placeID = empty($occurences) ? null : $occurences[0]->place->{'@id'};

      if ($placeID && !array_key_exists($placeID, $places)) {

        $placeCache = $this->cache->getItem('detskeriaarhus_place' . str_replace('/', '_', $placeID));
        if (!$placeCache->isHit()) {

          try {
            $response = $client->get($placeID);
          } catch (RequestException $e) {
            echo Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
              echo Psr7\str($e->getResponse());
            }
            throw new Exception('Network Error retrieving: ' . $placeID);
          }

          // https://github.com/8p/GuzzleBundle/issues/48
          $response->getBody()->rewind();
          $content = $response->getBody()->getContents();
          $decoded = json_decode($content);

          $places[$placeID] = $decoded;

          $placeCache->set($decoded);
          $placeCache->expiresAfter(24 * 60 * 60);
          $this->cache->save($placeCache);

        } else {
          $decoded = $placeCache->get();
          $places[$placeID] = $decoded;
        }

      }
    }

    return $places;
  }

  public function normalizeForOrganicity()
  {
    $lastSyncCache = $this->cache->getItem('detskeriaarhus_lastSync');
    if (!$lastSyncCache->isHit()) {
      $next_url = self::FEED_PATH;
    } else {
      $lastSync = $lastSyncCache->get();
      $next_url = self::FEED_PATH . '?updatedAt[after]=' . urlencode($lastSync);
    }

    $lastSync = gmdate('Y-m-d\TH:i:sP');
    $lastSyncCache->set($lastSync);
    $this->cache->save($lastSyncCache);

    $events_array = $this->getPagedData($next_url);
    $places_array = $this->getPlaceData($events_array);
    $assets = array();

    foreach ($events_array as $record) {

      $count = count($record->occurrences);

      if ($count > 0) {

        $pathinfo = pathinfo($record->{'@id'});
        $id = $pathinfo['basename'];

        $first = $record->occurrences[0];
        $last = $record->occurrences[$count - 1];

        $asset = array(
          'id' => 'urn:oc:entity:aarhus:events:' . $id,
          'type' => 'urn:oc:entityType:event',

          'origin' => array(
            'type' => 'urn:oc:attributeType:origin',
            'value' => 'Det sker i Aarhus',
            'metadata' => array(
              'urls' => array(
                'type' => 'urls',
                'value' => 'http://api.detskeriaarhus.dk/'
              )
            )
          )
        );

        $asset['name'] = array(
          'type' => 'urn:oc:attributeType:name',
          'value' => $record->name
        );

        $asset['excerpt'] = array(
          'type' => 'urn:oc:attributeType:excerpt',
          'value' => $record->excerpt
        );

        // @TODO Orion doesn't accept html so field excluded
//        $asset['description'] = array(
//          'type' => 'urn:oc:attributeType:description',
//          'value' => $record->description
//        );

        // Time
        $startTime = strtotime($first->startDate);
        $asset['startTime'] = array(
          'type' => 'urn:oc:attributeType:ISO8601',
          'value' => gmdate('Y-m-d\TH:i:s.000\Z', $startTime)
        );

        $endTime = strtotime($last->endDate);
        $asset['endTime'] = array(
          'type' => 'urn:oc:attributeType:ISO8601',
          'value' => gmdate('Y-m-d\TH:i:s.000\Z', $endTime)
        );

        $list = array();
        foreach ($record->occurrences as $occurrence) {
          $list[] = array('startDate' => $occurrence->startDate, 'endDate' => $occurrence->endDate);
        }

        // @TODO Orion doesn't accept the metadata list so field excluded
//        $asset['occurences'] = array(
//          'type' => 'urn:oc:attributeType:count',
//          'value' => $count,
//          'metadata' => array(
//            'list' => $list
//          )
//        );

        $asset['organizer'] = array(
          'type' => 'urn:oc:attributeType:organizer',
          'value' => $record->organizer->name
        );

        $asset['imageURL'] = array(
          'type' => 'urn:oc:attributeType:imageURL',
          'value' => $record->image
        );

        $asset['videoURL'] = array(
          'type' => 'urn:oc:attributeType:videoURL',
          'value' => $record->videoUrl
        );

        $asset['URL'] = array(
          'type' => 'urn:oc:attributeType:url',
          'value' => $record->url
        );

        $asset['ticketURL'] = array(
          'type' => 'urn:oc:attributeType:url',
          'value' => $record->ticketPurchaseUrl
        );

        $asset['ticketPriceRange'] = array(
          'type' => 'urn:oc:attributeType:ticketPriceRange',
          'value' => $first->ticketPriceRange
        );

        // Location
        $placeID = $first->place->{'@id'};
        $place = $places_array[$placeID];

        $point_LAT = $place->latitude;
        $point_LNG = $place->longitude;
        $asset['location'] = array(
          'type' => 'geo:point',
          'value' => $point_LAT . ', ' . $point_LNG
        );

        $asset['streetAddress'] = array(
          'type' => 'urn:oc:attributeType:streetAddress',
          'value' => $place->streetAddress
        );

        $asset['city'] = array(
          'type' => 'urn:oc:attributeType:city',
          'value' => $place->addressLocality
        );

        $asset['postalCode'] = array(
          'type' => 'urn:oc:attributeType:postalCode',
          'value' => $place->postalCode
        );

        $assets[] = $asset;
      }

    }

    return $assets;
  }

}