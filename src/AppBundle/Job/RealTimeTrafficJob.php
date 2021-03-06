<?php

namespace AppBundle\Job;

class RealTimeTrafficJob extends BaseJob
{
	protected $interval = 240; // 4 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('real_time_traffic');
        $assets = $feed->normalizeForOrganicity();

        $this->pointsPersister->persistPointsByTimestamp($assets, 'RealTimeTraffic');
        $this->spawnBatchJob($assets);
    }
}
