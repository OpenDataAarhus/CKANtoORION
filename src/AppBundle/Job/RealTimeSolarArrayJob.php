<?php

namespace AppBundle\Job;

class RealTimeSolarArrayJob extends BaseJob {
	protected $interval = 300; // 5 * 60

	public function run( $args ) {
		parent::run( $args );

		$feed   = $this->getContainer()->get( 'app.feed_reader_factory' )->getFeedReader( 'real_time_solar_array' );
		$assets = $feed->normalizeForOrganicity();

    $this->pointsPersister->persistPoints($assets, 'RealTimeSolarArray');
		$this->spawnBatchJob( $assets );
	}
}