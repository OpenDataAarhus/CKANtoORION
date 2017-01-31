<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class FriluftslivDogWalkingAreaJob extends BaseJob
{
  private $interval = 24 * 60 * 60;

  public function run($args)
  {
    parent::run($args);

    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('friluftsliv_dagwalkingarea');
    $feed->syncToOrganicity();
  }
}
