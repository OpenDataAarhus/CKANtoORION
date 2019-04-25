<?php

namespace AppBundle\Job;

class CityLabJob extends BaseJob
{
    protected $interval = 30; // 5 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('city_lab');
        $assets = $feed->normalizeForOrganicity();

        $assets = $this->pointsPersister->persistPointsById($assets, 'CityLab');

        $assets = $this->removeDuplicates($assets);
        $this->spawnBatchJob($assets);
    }
}
