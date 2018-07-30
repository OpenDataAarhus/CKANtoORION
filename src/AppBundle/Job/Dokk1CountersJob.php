<?php

namespace AppBundle\Job;

use AppBundle\OrionSync\SyncJob;
use ResqueBundle\Resque\ContainerAwareJob;
use Symfony\Component\Translation\Interval;

class Dokk1CountersJob extends BaseJob {
	protected $interval = 300; // 5 * 60

	public function run( $args ) {
		parent::run( $args );

		$feed   = $this->getContainer()->get( 'app.feed_reader_factory' )->getFeedReader( 'dokk1_counters' );
		$assets = $feed->normalizeForOrganicity();

		$this->pointsPersister->persistPoints($assets, 'Dokk1Counters');
		$this->spawnBatchJob( $assets );
	}

}
