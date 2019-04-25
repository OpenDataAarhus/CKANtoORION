<?php

namespace AppBundle\Job;

class FriluftslivPlaygroundJob extends BaseJob
{
    protected $interval = 86400; // 24 * 60 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('friluftsliv_playgrounds');
        $assets = $feed->normalizeForOrganicity();

        $this->spawnBatchJob($assets);
    }
}
