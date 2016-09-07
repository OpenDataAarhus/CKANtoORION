<?php

namespace AppBundle\Service;

use AppBundle\Feed\FriluftslivFirepitsReader;
use AppBundle\Feed\FriluftslivFitnessGymReader;
use Exception;
use GuzzleHttp\Client;
use AppBundle\Feed\RealTimeTrafficReader;
use AppBundle\Feed\Dokk1CountersReader;

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

    switch ($identifier) {
      case 'dokk1_counters':
        return new Dokk1CountersReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'real_time_traffic':
        return new RealTimeTrafficReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_firepits':
        return new FriluftslivFirepitsReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_fitness':
        return new FriluftslivFitnessGymReader($this->odaaClient, $this->orionUpdater);
        break;

      default:
        throw new Exception('unknown feed $identifier');
    }
  }

}