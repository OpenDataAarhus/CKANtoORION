<?php

namespace AppBundle\Job;

class FriluftslivHikingTrailsJob extends BaseJob
{
    protected $interval = 86400; // 24 * 60 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('friluftsliv_hikingtrails');
        $assets = $feed->normalizeForOrganicity();

        $this->spawnSingleJobs($assets);
    }
}
