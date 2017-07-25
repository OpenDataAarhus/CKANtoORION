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

class DetskeriaarhusReader
{
  const FEED_PATH = '/api/events';

  private $detskeriaarhusClient;
  private $orionUpdater;
  private $cache;

  public function __construct(Client $detskeriaarhusClient, Client $orionUpdater, $cache)
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
      $next_records = $decoded->{'hydra:member'};
      foreach ($next_records as $record) {
        $records[$record->{'@id'}] = $record;
      }

      if (!isset($decoded->{'hydra:view'}->{'hydra:next'})) {
        return $records;
      } else {
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

            // https://github.com/8p/GuzzleBundle/issues/48
            $response->getBody()->rewind();
            $content = $response->getBody()->getContents();
            $decoded = json_decode($content);

          } catch (RequestException $e) {

//            @TODO Event DB returns a HTTP 500 for places with many events/occurences, set place to NULL and skip

//            echo Psr7\str($e->getRequest());
//            if ($e->hasResponse()) {
//              echo Psr7\str($e->getResponse());
//            }
//            throw new Exception('Network Error retrieving: ' . $placeID);

            $decoded = NULL;
          }

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

        $placeID = $first->place->{'@id'};
        $place = $places_array[$placeID];

//      @TODO Event DB returns a HTTP 500 for places with many events/occurences, set place to NULL and skip
        if($place) {

          $asset = [
            'id' => 'urn:oc:entity:aarhus:events:' . $id,
            'type' => 'urn:oc:entityType:event',

            'origin' => [
              'type' => 'urn:oc:attributeType:origin',
              'value' => 'Det sker i Aarhus',
              'metadata' => [
                'urls' => [
                  'type' => 'urls',
                  'value' => 'http://api.detskeriaarhus.dk/'
                ]
              ]
            ]
          ];

          $asset['name'] = [
            'type' => 'urn:oc:attributeType:name',
            'value' => $this->sanitizeText($record->name)
          ];

          $excerpt = $this->sanitizeText($record->excerpt);
          $asset['excerpt'] = [
            'type' => 'urn:oc:attributeType:excerpt',
            'value' => $excerpt
          ];

          // @TODO Orion doesn't accept html so field excluded
//        $asset['description'] = array(
//          'type' => 'urn:oc:attributeType:description',
//          'value' => $record->description
//        );

          // Time
          $startTime = strtotime($first->startDate);
          $endTime = strtotime($last->startDate);

          $asset['TimeInstant'] = [
            'type' => 'urn:oc:attributeType:ISO8601',
            'value' => gmdate('Y-m-d\TH:i:s.000\Z', $startTime)
          ];

          $asset['firstEventTime'] = [
            'type' => 'urn:oc:attributeType:ISO8601',
            'value' => gmdate('Y-m-d\TH:i:s.000\Z', $endTime)
          ];

          $asset['lastEventTime'] = [
            'type' => 'urn:oc:attributeType:ISO8601',
            'value' => gmdate('Y-m-d\TH:i:s.000\Z', $endTime)
          ];

          $asset['numberOfOccurrences'] = [
            'type' => 'urn:oc:attributeType:numberOfOccurrences',
            'value' => $count
          ];

//        @TODO Orion doesn't accept the metadata list so field excluded
//        $list = array();
//        foreach ($record->occurrences as $occurrence) {
//          $list[] = array('startDate' => $occurrence->startDate, 'endDate' => $occurrence->endDate);
//        }
//        $asset['occurences'] = array(
//          'type' => 'urn:oc:attributeType:count',
//          'value' => $count,
//          'metadata' => array(
//            'list' => $list
//          )
//        );

          $asset['organizer'] = [
            'type' => 'urn:oc:attributeType:organizer',
            'value' => $this->sanitizeText($record->organizer->name)
          ];

          $asset['imageURL'] = [
            'type' => 'urn:oc:attributeType:url',
            'value' => $this->sanitizeUrl($record->image)
          ];

          $asset['videoURL'] = [
            'type' => 'urn:oc:attributeType:url',
            'value' => $this->sanitizeUrl($record->videoUrl)
          ];

          $asset['URL'] = [
            'type' => 'urn:oc:attributeType:url',
            'value' => $this->sanitizeUrl($record->url)
          ];

          $asset['ticketURL'] = [
            'type' => 'urn:oc:attributeType:url',
            'value' => $this->sanitizeUrl($record->ticketPurchaseUrl)
          ];

          $asset['ticketPriceRange'] = [
            'type' => 'urn:oc:attributeType:ticketPriceRange',
            'value' => $this->sanitizeText($first->ticketPriceRange)
          ];

          // Location
          $point_LAT = $place->latitude;
          $point_LNG = $place->longitude;
          $asset['location'] = [
            'type' => 'geo:point',
            'value' => $point_LAT . ', ' . $point_LNG
          ];

          $asset['streetAddress'] = [
            'type' => 'urn:oc:attributeType:streetAddress',
            'value' => $this->sanitizeText($place->streetAddress)
          ];

          $asset['city'] = [
            'type' => 'urn:oc:attributeType:city',
            'value' => $this->sanitizeText($place->addressLocality)
          ];

          $asset['postalCode'] = [
            'type' => 'urn:oc:attributeType:postalCode',
            'value' => $place->postalCode
          ];

          $assets[] = $asset;
        }
      }
    }

    $lastSync = gmdate('Y-m-d\TH:i:sP');
    $lastSyncCache->set($lastSync);
    $this->cache->save($lastSyncCache);

    return $assets;
  }

  /**
   * Sanitize text for Orion, remove whitespace and linebreaks, remove Orion forbidden characters
   *
   * @param $text
   *
   * @return mixed
   */
  private function sanitizeText($text) {
    if ($text) {
      $text = trim(preg_replace('/\s+/', ' ', $text));

      // https://fiware-orion.readthedocs.io/en/master/user/forbidden_characters/index.html#forbidden-characters
      $text = str_replace('<', '', $text);
      $text = str_replace('>', '', $text);
      $text = str_replace('"', '', $text);
      $text = str_replace("'", '', $text);
      $text = str_replace('=', '', $text);
      $text = str_replace(';', '', $text);
      $text = str_replace('(', '', $text);
      $text = str_replace(')', '', $text);
    }

    return $text;
  }

  /**
   * Sanitize url for Orion, remove Orion forbidden characters
   *
   * @param $url
   *
   * @return mixed
   */
  private function sanitizeUrl($url) {
    if($url) {

      // https://fiware-orion.readthedocs.io/en/master/user/forbidden_characters/index.html#forbidden-characters
      $url = str_replace('<', '%3C', $url);
      $url = str_replace('>', '%3E', $url);
      $url = str_replace('"', '%22', $url);
      $url = str_replace("'", '%27', $url);
      $url = str_replace('=', '%3D', $url);
      $url = str_replace(';', '%3B', $url);
      $url = str_replace('(', '%28', $url);
      $url = str_replace(')', '%29', $url);
    }

    return $url;
  }

}