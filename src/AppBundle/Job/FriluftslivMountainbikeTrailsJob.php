<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class FriluftslivMountainbikeTrailsJob extends BaseJob
{
  protected $interval = 24 * 60 * 60;

  public function run($args)
  {
    parent::run($args);

    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('friluftsliv_mountainbiketrails');
    $assets = $feed->normalizeForOrganicity();

    $this->spawnSingleJobs($assets);
  }
}
