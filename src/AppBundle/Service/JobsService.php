<?php

namespace AppBundle\Service;

use ResqueBundle\Resque\Resque;

class JobsService
{
    private $resque;

    public function __construct(Resque $resque)
    {
        $this->resque = $resque;
    }

    public function isAllreadyQueued($class)
    {
        $timestamps = $this->resque->getDelayedJobTimestamps();
        $className = get_class($class);

        foreach ($timestamps as $timestamp) {
            $delayed = $this->resque->getJobsForTimestamp($timestamp[0]);
            foreach ($delayed as $job) {
                if ($className === $job['class']) {
                    return true;
                }
            }
        }

        return false;
    }
}
