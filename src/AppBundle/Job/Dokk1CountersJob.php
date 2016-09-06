<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class Dokk1CountersJob extends ContainerAwareJob
{
  const INTERVAL = 60*5;

  public function run($args)
  {
    // get resque
    $resque = $this->getContainer()->get('resque');

    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('dokk1_counters');
    $assets = $feed->normalizeForOrganicity();

    $resque->enqueueIn(self::INTERVAL, $this);
  }
}
