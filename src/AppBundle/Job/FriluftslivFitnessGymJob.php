<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class FriluftslivFitnessGymJob extends BaseJob {
	protected $interval = 86400; // 24 * 60 * 60

	public function run( $args ) {
		parent::run( $args );

		$feed   = $this->getContainer()->get( 'app.feed_reader_factory' )->getFeedReader( 'friluftsliv_fitness' );
		$assets = $feed->normalizeForOrganicity();

		$this->spawnBatchJob( $assets );
	}
}
