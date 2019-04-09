<?php

namespace AppBundle\Job;

use AppBundle\OrionSync\SyncJob;
use ResqueBundle\Resque\ContainerAwareJob;

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

    protected function spawnSingleJobs($assets): void
    {
        foreach ($assets as $asset) {
            $syncJob = new SyncJob();
            $syncJob->args = [
        'assets' => [$asset],
      ];

            $this->resque->enqueue($syncJob);
        }
    }

    protected function spawnBatchJob($assets): void
    {
        $syncJob = new SyncJob();
        $syncJob->args = [
      'assets' => $assets,
    ];

        $this->resque->enqueue($syncJob);
    }
}
