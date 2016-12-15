<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class FriluftslivShelterJob extends ContainerAwareJob
{
  const INTERVAL = 24 * 60 * 60;

  public function run($args)
  {
    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('friluftsliv_shelter');
    $feed->syncToOrganicity();
  }
}
