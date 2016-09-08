<?php

namespace AppBundle\Command;

use AppBundle\Job\FriluftslivGearStationJob;
use AppBundle\Job\FriluftslivFirepitsJob;
use AppBundle\Job\Dokk1CountersJob;
use AppBundle\Job\FriluftslivFitnessGymJob;
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
      ->setName('app:create-feeds')
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

    // create your job
    $jobs[] = new Dokk1CountersJob();
    $jobs[] = new RealTimeTrafficJob();
    $jobs[] = new FriluftslivFirepitsJob();
    $jobs[] = new FriluftslivFitnessGymJob();
    $jobs[] = new FriluftslivGearStationJob();

    foreach ($jobs as $job) {
      // enqueue your job
      $resque->removedDelayed($job);
      $resque->enqueueIn(60, $job);
    }
  }
}