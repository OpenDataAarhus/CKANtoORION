<?php

namespace AppBundle\Job;

class DetSkerIAarhusJob extends BaseJob
{
    protected $interval = 3600; // 60 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('detskeriaarhus');
        $assets = $feed->normalizeForOrganicity();

        $this->spawnSingleJobs($assets);
    }
}
