<?php

namespace AppBundle\Job;

class CityLabJob extends BaseJob {
	protected $interval = 300; // 5 * 60

	public function run( $args ) {
		parent::run( $args );

		$feed   = $this->getContainer()->get( 'app.feed_reader_factory' )->getFeedReader( 'city_lab' );
		$assets = $feed->normalizeForOrganicity();

    $this->pointsPersister->persistPoints($assets, 'CityLab');
		$this->spawnBatchJob( $assets );
	}
}