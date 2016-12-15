<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class FriluftslivForestSmallJob extends ContainerAwareJob
{
  const INTERVAL = 24 * 60 * 60;

  public function run($args)
  {
    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('friluftsliv_forests_small');
    $feed->syncToOrganicity();
  }
}
