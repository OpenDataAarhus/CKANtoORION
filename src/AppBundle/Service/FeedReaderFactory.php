<?php

namespace AppBundle\Service;

use AppBundle\Feed\CityLabReader;
use AppBundle\Feed\CityProbeReader;
use AppBundle\Feed\Dokk1BookReturnsReader;
use AppBundle\Feed\FriluftslivBeachAreaReader;
use AppBundle\Feed\FriluftslivDogWalkingAreaReader;
use AppBundle\Feed\FriluftslivFirepitsReader;
use AppBundle\Feed\FriluftslivForestReader;
use AppBundle\Feed\FriluftslivForestSmallReader;
use AppBundle\Feed\FriluftslivGearStationReader;
use AppBundle\Feed\FriluftslivFitnessGymReader;
use AppBundle\Feed\FriluftslivHikingTrailsReader;
use AppBundle\Feed\FriluftslivHorseRidingTrailsReader;
use AppBundle\Feed\FriluftslivKioskReader;
use AppBundle\Feed\FriluftslivMountainBikeTrailsReader;
use AppBundle\Feed\FriluftslivNaturCenterReader;
use AppBundle\Feed\FriluftslivParksReader;
use AppBundle\Feed\FriluftslivPlaygroundReader;
use AppBundle\Feed\FriluftslivRunningTrailsReader;
use AppBundle\Feed\FriluftslivShelterReader;
use AppBundle\Feed\FriluftslivToiletReader;
use AppBundle\Feed\FriluftslivTreeClimbingReader;
use AppBundle\Feed\DetskeriaarhusReader;
use AppBundle\Feed\RealTimeParkingReader;
use AppBundle\Feed\RealTimeSolarArrayReader;
use Doctrine\ORM\EntityManager;
use Exception;
use GuzzleHttp\Client;
use AppBundle\Feed\RealTimeTrafficReader;
use AppBundle\Feed\Dokk1CountersReader;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class FeedReaderFactory
{
    private $openDataDkClient;
    private $orionUpdater;
    private $detskeriaarhusClient;
    private $adapter;
    private $entityManager;

    public function __construct(Client $openDataDkClient, Client $orionUpdater, Client $detskeriaarhusClient, AdapterInterface $adapter, EntityManager $entityManager)
    {
        $this->openDataDkClient = $openDataDkClient;
        $this->orionUpdater = $orionUpdater;
        $this->detskeriaarhusClient = $detskeriaarhusClient;
        $this->adapter = $adapter;
        $this->entityManager = $entityManager;
    }

    public function getFeedReader(string $identifier)
    {
        switch ($identifier) {
      case 'city_lab':
        return new CityLabReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'city_probe':
        return new CityProbeReader($this->openDataDkClient, $this->orionUpdater, $this->adapter, $this->entityManager);
        break;

      case 'dokk1_counters':
        return new Dokk1CountersReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'dokk1_book_returns':
        return new Dokk1BookReturnsReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'real_time_traffic':
        return new RealTimeTrafficReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'real_time_parking':
        return new RealTimeParkingReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'real_time_solar_array':
        return new RealTimeSolarArrayReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_firepits':
        return new FriluftslivFirepitsReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_fitness':
        return new FriluftslivFitnessGymReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_gearstation':
        return new FriluftslivGearStationReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_shelter':
        return new FriluftslivShelterReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_naturecenter':
        return new FriluftslivNaturCenterReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_kiosk':
        return new FriluftslivKioskReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_toilet':
        return new FriluftslivToiletReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_treeclimbing':
        return new FriluftslivTreeClimbingReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_beacharea':
        return new FriluftslivBeachAreaReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_dagwalkingarea':
        return new FriluftslivDogWalkingAreaReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_parks':
        return new FriluftslivParksReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_forests':
        return new FriluftslivForestReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_forests_small':
        return new FriluftslivForestSmallReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_hikingtrails':
        return new FriluftslivHikingTrailsReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_mountainbiketrails':
        return new FriluftslivMountainBikeTrailsReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_runningtrails':
        return new FriluftslivRunningTrailsReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_horseridingtrails':
        return new FriluftslivHorseRidingTrailsReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'friluftsliv_playgrounds':
        return new FriluftslivPlaygroundReader($this->openDataDkClient, $this->orionUpdater, $this->adapter);
        break;

      case 'detskeriaarhus':
        return new DetskeriaarhusReader($this->detskeriaarhusClient, $this->orionUpdater, $this->adapter);
        break;

      default:
        throw new Exception('unknown feed $identifier');
    }
    }
}
