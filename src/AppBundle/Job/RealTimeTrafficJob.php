<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class RealTimeTrafficJob extends ContainerAwareJob
{
  const INTERVAL = 300;

  public function run($args)
  {
    // get resque
    $resque = $this->getContainer()->get('resque');

    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('dokk1_counters');
    $feed->syncToOrganicity();

    $resque->enqueueIn(self::INTERVAL, $this);
  }
}
