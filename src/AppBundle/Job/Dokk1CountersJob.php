<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;
use Symfony\Component\Translation\Interval;

class Dokk1CountersJob extends BaseJob
{
  private $interval = 60 * 60;

  public function run($args)
  {
    parent::run($args);

    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('dokk1_counters');
    $feed->syncToOrganicity();
  }

}
