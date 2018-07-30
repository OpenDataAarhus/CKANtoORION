<?php

namespace AppBundle\Job;

use AppBundle\OrionSync\SyncJob;
use ResqueBundle\Resque\ContainerAwareJob;
use Symfony\Component\Translation\Interval;

class BaseJob extends ContainerAwareJob
{
  protected $interval = 86400; // 24 * 60 * 60
  protected $resque;
  protected $pointsPersister;

  public function run($args)
  {
    $this->args = $args;

    $this->resque = $this->getContainer()->get('ResqueBundle\Resque\Resque');
    $this->pointsPersister = $this->getContainer()->get('app.points_persister_service');

    $jobsService = $this->getContainer()->get('app.jobs_service');

    if (!$jobsService->isAllreadyQueued($this)) {
      $this->resque->enqueueIn($this->interval, $this);
    }

  }

  protected function spawnSingleJobs($assets)
  {
    foreach ($assets as $asset) {
      $syncJob = new SyncJob();
      $syncJob->args = [
        'assets' => [$asset],
      ];

      $this->resque->enqueue($syncJob);
    }
  }

  // @TODO Test to see if it's the batch job that cause problems with subscription updates
  protected function spawnBatchJob($assets)
  {
    $seconds = 1;
    $count = 0;

    // @TODO / Hack: Notifications seems to skip every 5. Shuffle to ensure diff order each time
    ksort($assets);

    foreach ($assets as $asset) {
      $syncJob = new SyncJob();
      $syncJob->args = [
        'assets' => [$asset],
      ];

      $this->resque->enqueueIn($seconds, $syncJob);
      $seconds = ($count % 3 == 0) ? $seconds + 1 : $seconds;
      $count++;
    }
  }

}
