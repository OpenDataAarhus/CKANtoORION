<?php

namespace AppBundle\Job;

class FriluftslivGearStationJob extends BaseJob
{
    protected $interval = 86400; // 24 * 60 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('friluftsliv_gearstation');
        $assets = $feed->normalizeForOrganicity();

        $this->spawnBatchJob($assets);
    }
}
