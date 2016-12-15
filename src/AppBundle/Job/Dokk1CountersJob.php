<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class Dokk1CountersJob extends ContainerAwareJob
{
  const INTERVAL = 60 * 60;

  public function run($args)
  {
    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('dokk1_counters');
    $feed->syncToOrganicity();
  }
}
