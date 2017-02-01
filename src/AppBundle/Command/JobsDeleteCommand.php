<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobsDeleteCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      // the name of the command (the part after "bin/console")
      ->setName('app:jobs:delete')
      // the short description shown while running "php bin/console list"
      ->setDescription('Delete All Jobs.')
      // the full command description shown when running the command with
      // the "--help" option
      ->setHelp("This command delete all repeating jobs for CKANtoORION");
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // get resque
    $resque = $this->getContainer()->get('resque');
    $resque->clearQueue('default');

    $timestamps = $resque->getDelayedJobTimestamps();

    foreach ($timestamps as $timestamp) {
      $delayed = $resque->getJobsForTimestamp($timestamp[0]);
      foreach ($delayed as $job) {
        $resque->removedDelayed($job);
      }
    }

  }
}