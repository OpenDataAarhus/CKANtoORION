<?php

namespace AppBundle\Service;

use AppBundle\Feed\FriluftslivBeachAreaReader;
use AppBundle\Feed\FriluftslivDogWalkingAreaReader;
use AppBundle\Feed\FriluftslivFirepitsReader;
use AppBundle\Feed\FriluftslivForestReader;
use AppBundle\Feed\FriluftslivForestSmallReader;
use AppBundle\Feed\FriluftslivGearStationReader;
use AppBundle\Feed\FriluftslivFitnessGymReader;
use AppBundle\Feed\FriluftslivKioskReader;
use AppBundle\Feed\FriluftslivNaturCenterReader;
use AppBundle\Feed\FriluftslivParksReader;
use AppBundle\Feed\FriluftslivShelterReader;
use AppBundle\Feed\FriluftslivToiletReader;
use AppBundle\Feed\FriluftslivTreeClimbingReader;
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

      case 'friluftsliv_gearstation':
        return new FriluftslivGearStationReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_shelter':
        return new FriluftslivShelterReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_naturecenter':
        return new FriluftslivNaturCenterReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_kiosk':
        return new FriluftslivKioskReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_toilet':
        return new FriluftslivToiletReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_treeclimbing':
        return new FriluftslivTreeClimbingReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_beacharea':
        return new FriluftslivBeachAreaReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_dagwalkingarea':
        return new FriluftslivDogWalkingAreaReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_parks':
        return new FriluftslivParksReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_forests':
        return new FriluftslivForestReader($this->odaaClient, $this->orionUpdater);
        break;

      case 'friluftsliv_forests_small':
        return new FriluftslivForestSmallReader($this->odaaClient, $this->orionUpdater);
        break;

      default:
        throw new Exception('unknown feed $identifier');
    }
  }

}