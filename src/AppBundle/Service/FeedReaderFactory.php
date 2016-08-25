<?php

namespace AppBundle\Service;

use Exception;
use AppBundle\Feed\Dokk1CountersReader;
use GuzzleHttp\Client;

class FeedReaderFactory
{
  private $odaaClient;
  private $orionUpdater;

  public function __construct(Client $odaaClient, Client $orionUpdater)
  {
    $this->odaaClient = $odaaClient;
    $this->orionUpdater = $orionUpdater;
  }

  public function getFeedReader(string $identifier) {
    if($identifier === 'dokk1_counters') {
      return new Dokk1CountersReader($this->odaaClient, $this->orionUpdater);
    }

    throw new Exception('unknown feed $identifier');
  }

}