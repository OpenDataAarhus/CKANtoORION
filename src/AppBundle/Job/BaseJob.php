<?php

namespace AppBundle\Job;

use AppBundle\OrionSync\SyncJob;
use ResqueBundle\Resque\ContainerAwareJob;
use Symfony\Component\Translation\Interval;

class BaseJob extends ContainerAwareJob
{
  protected $interval = 60 * 60 * 24;
  protected $resque;

  public function run($args)
  {
    $this->resque = $this->getContainer()->get('resque');
    $jobsService = $this->getContainer()->get('app.jobs_service');

    if (!$jobsService->isAllreadyQueued($this)) {
      $this->resque->enqueueIn($this->interval, $this);
    }

  }

  protected function spawnSingleJobs($assets) {
    foreach ($assets as $asset) {
      $syncJob = new SyncJob();
      $syncJob->args = array(
        'assets' => array($asset)
      );

      $this->resque->enqueue($syncJob);
    }
  }

  protected function spawnBatchJob($assets) {
    $syncJob = new SyncJob();
    $syncJob->args = array(
      'assets'    => $assets
    );

    $this->resque->enqueue($syncJob);
  }

}