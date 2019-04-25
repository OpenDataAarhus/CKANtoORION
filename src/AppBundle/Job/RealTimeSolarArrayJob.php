<?php

namespace AppBundle\Job;

class RealTimeSolarArrayJob extends BaseJob
{
	protected $interval = 240; // 4 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('real_time_solar_array');
        $assets = $feed->normalizeForOrganicity();

        $this->pointsPersister->persistPointsByTimestamp($assets, 'RealTimeSolarArray');
        $this->spawnBatchJob($assets);
    }
}
