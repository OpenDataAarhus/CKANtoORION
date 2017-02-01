<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class RealTimeTrafficJob extends BaseJob
{
  protected $interval = 5 * 60;

  public function run($args)
  {
    parent::run($args);

    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('real_time_traffic');
    $assets = $feed->normalizeForOrganicity();

    $this->spawnBatchJob($assets);
  }
}
