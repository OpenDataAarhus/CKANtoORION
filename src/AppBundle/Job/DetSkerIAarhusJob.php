<?php

namespace AppBundle\Job;

use AppBundle\OrionSync\SyncJob;
use ResqueBundle\Resque\ContainerAwareJob;
use Symfony\Component\Translation\Interval;

class DetSkerIAarhusJob extends BaseJob
{
  protected $interval = 60 * 60;

  public function run($args)
  {
    parent::run($args);

    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('detskeriaarhus');
    $assets = $feed->normalizeForOrganicity();

    $this->spawnBatchJob($assets);
  }

}
