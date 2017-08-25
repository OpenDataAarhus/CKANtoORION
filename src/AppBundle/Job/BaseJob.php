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
            $seconds++;
        }
    }

}
