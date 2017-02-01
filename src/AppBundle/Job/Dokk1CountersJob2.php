<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;
use Symfony\Component\Translation\Interval;
use AppBundle\OrionSync\SyncJob;

class Dokk1CountersJob2 // extends BaseJob
{
  protected $interval = 60 * 60;
  private $feed;

  public function run($args)
  {
//    parent::run($args);

//    $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('dokk1_counters');
    $assets = $this->feed->normalizeForOrganicity();

    $syncJob = new SyncJob();
    $syncJob->args = array(
      'assets'    => $assets
    );

    $this->resque->enqueue($syncJob);
  }

  public function setFeed($feed)
  {
    $this->feed = $feed;
  }

}
