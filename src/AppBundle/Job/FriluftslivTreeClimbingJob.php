<?php

namespace AppBundle\Job;

class FriluftslivTreeClimbingJob extends BaseJob
{
    protected $interval = 86400; // 24 * 60 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('friluftsliv_treeclimbing');
        $assets = $feed->normalizeForOrganicity();

        $this->spawnBatchJob($assets);
    }
}
