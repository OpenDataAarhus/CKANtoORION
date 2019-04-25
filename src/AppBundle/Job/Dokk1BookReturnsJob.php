<?php

namespace AppBundle\Job;

class Dokk1BookReturnsJob extends BaseJob
{
	protected $interval = 240; // 4 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('dokk1_book_returns');
        $assets = $feed->normalizeForOrganicity();

        $this->pointsPersister->persistPointsByTimestamp($assets, 'Dokk1BookReturns');
        $this->spawnBatchJob($assets);
    }
}
