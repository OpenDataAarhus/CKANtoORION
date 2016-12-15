<?php

namespace AppBundle\Command;

use AppBundle\Feed\FriluftslivBeachAreaReader;
use AppBundle\Feed\FriluftslivDogWalkingAreaReader;
use AppBundle\Job\FriluftslivBeachAreaJob;
use AppBundle\Job\FriluftslivDogWalkingAreaJob;
use AppBundle\Job\FriluftslivForestJob;
use AppBundle\Job\FriluftslivForestSmallJob;
use AppBundle\Job\FriluftslivGearStationJob;
use AppBundle\Job\FriluftslivFirepitsJob;
use AppBundle\Job\Dokk1CountersJob;
use AppBundle\Job\FriluftslivFitnessGymJob;
use AppBundle\Job\FriluftslivHikingTrailsJob;
use AppBundle\Job\FriluftslivHorseRidingTrailsJob;
use AppBundle\Job\FriluftslivKioskJob;
use AppBundle\Job\FriluftslivMountainbikeTrailsJob;
use AppBundle\Job\FriluftslivNatureCenterJob;
use AppBundle\Job\FriluftslivParksJob;
use AppBundle\Job\FriluftslivRunningTrailsJob;
use AppBundle\Job\FriluftslivShelterJob;
use AppBundle\Job\FriluftslivToiletJob;
use AppBundle\Job\FriluftslivTreeClimbingJob;
use AppBundle\Job\RealTimeTrafficJob;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateJobsCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      // the name of the command (the part after "bin/console")
      ->setName('app:jobs:create')
      // the short description shown while running "php bin/console list"
      ->setDescription('Creates feeds.')
      // the full command description shown when running the command with
      // the "--help" option
      ->setHelp("This command sets up the repeating jobs for CKANtoORION");
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // get resque
    $resque = $this->getContainer()->get('resque');
    $resque->clearQueue('default');

    // create your job
    $jobs[] = new Dokk1CountersJob();
    $jobs[] = new RealTimeTrafficJob();

    $jobs[] = new FriluftslivBeachAreaJob();
    $jobs[] = new FriluftslivDogWalkingAreaJob();
    $jobs[] = new FriluftslivFirepitsJob();
    $jobs[] = new FriluftslivFitnessGymJob();
    $jobs[] = new FriluftslivForestJob();
    $jobs[] = new FriluftslivForestSmallJob();
    $jobs[] = new FriluftslivGearStationJob();
    $jobs[] = new FriluftslivHikingTrailsJob();
    $jobs[] = new FriluftslivHorseRidingTrailsJob();
    $jobs[] = new FriluftslivKioskJob();
    $jobs[] = new FriluftslivMountainbikeTrailsJob();
    $jobs[] = new FriluftslivNatureCenterJob();
    $jobs[] = new FriluftslivParksJob();
    $jobs[] = new FriluftslivRunningTrailsJob();
    $jobs[] = new FriluftslivShelterJob();
    $jobs[] = new FriluftslivToiletJob();
    $jobs[] = new FriluftslivTreeClimbingJob();

    foreach ($jobs as $job) {
      // enqueue your job
      $resque->enqueue($job);
    }
  }
}