<?php

namespace AppBundle\Service;

use Exception;
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
        // get resque
        $timestamps = $this->resque->getDelayedJobTimestamps();
        $waiting = $this->resque->getNumberOfDelayedJobs();

        $classname = get_class($class);

        foreach ($timestamps as $timestamp) {
            $delayed = $this->resque->getJobsForTimestamp($timestamp[0]);
            foreach ($delayed as $job) {
                if ($classname === $job['class']) {
                    return true;
                }
            }
        }

        return false;
    }

}