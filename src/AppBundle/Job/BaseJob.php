<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;
use Symfony\Component\Translation\Interval;

class BaseJob extends ContainerAwareJob
{
  private $interval = 60 * 60 * 24;

  public function run($args)
  {
    $jobsService = $this->getContainer()->get('app.jobs_service');

    if (!$jobsService->isAllreadyQueued($this)) {
      $resque = $this->getContainer()->get('resque');
      $resque->enqueueIn($this->interval, $this);
    }

  }

}
