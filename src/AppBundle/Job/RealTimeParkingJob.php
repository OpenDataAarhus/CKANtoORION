<?php

namespace AppBundle\Job;

class RealTimeParkingJob extends BaseJob
{
    protected $interval = 300; // 5 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('real_time_parking');
        $assets = $feed->normalizeForOrganicity();

        $this->pointsPersister->persistPointsByTimestamp($assets, 'RealTimeParking');
        $this->spawnBatchJob($assets);
    }
}
