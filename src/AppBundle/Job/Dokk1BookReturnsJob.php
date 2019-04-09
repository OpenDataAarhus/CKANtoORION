<?php

namespace AppBundle\Job;

use AppBundle\OrionSync\SyncJob;
use ResqueBundle\Resque\ContainerAwareJob;
use Symfony\Component\Translation\Interval;

class Dokk1BookReturnsJob extends BaseJob {
	protected $interval = 300; // 5 * 60

	public function run( $args ) {
		parent::run( $args );

		$feed   = $this->getContainer()->get( 'app.feed_reader_factory' )->getFeedReader( 'dokk1_book_returns' );
		$assets = $feed->normalizeForOrganicity();

    $this->pointsPersister->persistPointsByTimestamp($assets, 'Dokk1BookReturns');
		$this->spawnBatchJob( $assets );
	}

}
