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
use ForceUTF8\Encoding;

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

  public function syncToOrganicity()
  {
    $assets = $this->normalizeForOrganicity();

    $this->batchAction($assets);
  }

  /**
   * @param $assets
   * @param $actionType APPEND or UPDATE
   * @throws Exception
   */

  protected function batchAction($assets, $actionType = 'APPEND')
  {
    $client = $this->orionUpdater;
    $body = array(
      'actionType' => $actionType,
      'entities' => $assets
    );
    $json = json_encode($body);

    try {
      $response = $client->post('', array(
        'body' => $json
      ));
      $response->getBody()->rewind();
      $content = json_decode($response->getBody()->getContents());
    } catch (RequestException $e) {

      if($e->getResponse()) {
        $body = json_decode($e->getResponse()->getBody()->getContents());
        $error = isset($body->error) ? $body->error : 'UNKNOWN';
        $description = isset($body->description) ? $body->description : 'UNKNOWN';
        throw new Exception("Orion Error: " . $e->getCode() . ', ' . $error . ', ' . $description);
      } else {
        throw $e;
      }
    }
  }

  protected function getPagedData($next_url, $records = array())
  {
    if (!empty($next_url)) {
      $client = $this->odaaClient;

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

  protected function getGeoData($url, $records = array())
  {
    if (!empty($url)) {
      $client = $this->odaaClient;

      try {
        $response = $client->get($url);
      } catch (RequestException $e) {
        echo Psr7\str($e->getRequest());
        if ($e->hasResponse()) {
          echo Psr7\str($e->getResponse());
        }
        throw new Exception('Network Error retrieving: ' . $url);
      }

      // https://github.com/8p/GuzzleBundle/issues/48
      $response->getBody()->rewind();
      $content = $response->getBody()->getContents();

      if (empty($content)) {
        throw new Exception('No content retrived from: ' . $url );
      }

      $content = Encoding::toUTF8($content);
      $content = json_decode($content);

      if (!$content) {
        throw new Exception('JSON Decode Error: ' . json_last_error_msg() );
      }

      return array('features' => $content->features, 'Last-Modified' => $response->getHeader('Last-Modified')[0]);
    }

    throw new Exception('$url cannot be empty');
  }

}