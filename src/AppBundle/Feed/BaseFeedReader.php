<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 25/08/16
 * Time: 10:03
 */

namespace AppBundle\Feed;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Exception;

abstract class BaseFeedReader
{
  private $odaaClient;
  private $orionUpdater;

  public function __construct(Client $odaaClient, Client $orionUpdater)
  {
    $this->odaaClient = $odaaClient;
    $this->orionUpdater = $orionUpdater;
  }

  abstract public function normalizeForOrganicity();

  public function syncToOrganicity() {
    $assets = $this->normalizeForOrganicity();
    $json = json_encode($assets);

    $this->sendUpdate($json);
  }

  protected function sendUpdate($json) {
    $client   = $this->orionUpdater;

    try {
      $request = $client->post('', array(
        'body' => $json
      ));
    } catch (RequestException $e) {
      echo Psr7\str($e->getRequest());
      if ($e->hasResponse()) {
        echo Psr7\str($e->getResponse());
      }
      throw new Exception('Network Error: Cannot post to Orion');
    }
  }

  protected function getPagedData($next_url, $records = array())
  {
    if(!empty($next_url)) {
      $client = $this->odaaClient;

      $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://www.odaa.dk',
        // You can set any number of default request options.
        'timeout' => 2.0,
      ]);

      try {
        $response = $client->get($next_url);
      } catch (RequestException $e) {
        echo Psr7\str($e->getRequest());
        if ($e->hasResponse()) {
          echo Psr7\str($e->getResponse());
        }
        throw new Exception('Network Error retrieving: ' . $next_url);
      }

      $content = json_decode($response->getBody()->getContents());
      $next_records = $content->result->records;
      $next_url = $content->result->_links->next;

      if (empty($next_records)) {
        return $records;
      } else {
        foreach ($next_records as $record) {
          $records[$record->_id] = $record;
        }
        return $this->getPagedData($next_url, $records);
      }
    }

    throw new Exception('$next_url cannot be empty');
  }

}