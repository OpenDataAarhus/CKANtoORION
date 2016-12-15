<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class RealTimeTrafficJob extends ContainerAwareJob
{
  const INTERVAL = 5 * 60;

  public function run($args)
  {
    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('real_time_traffic');
    $feed->syncToOrganicity();
  }
}
