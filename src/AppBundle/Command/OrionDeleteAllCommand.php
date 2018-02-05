<?php

namespace AppBundle\Command;

use AppBundle\Job\FriluftslivGearStationJob;
use AppBundle\Job\FriluftslivFirepitsJob;
use AppBundle\Job\Dokk1CountersJob;
use AppBundle\Job\FriluftslivFitnessGymJob;
use AppBundle\Job\RealTimeTrafficJob;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrionDeleteAllCommand extends ContainerAwareCommand {
	private $client;

	protected function configure() {
		$this
			// the name of the command (the part after "bin/console")
			->setName( 'app:orion:delete-all' )
			// the short description shown while running "php bin/console list"
			->setDescription( 'Delete All Entities' )
			// the full command description shown when running the command with
			// the "--help" option
			->setHelp( "This command deletes all aarhus:* enities from ORION" );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		// get Guzzle Client
		$this->client      = $this->getContainer()->get( 'app.orion.enity' );
		$this->batchClient = $this->getContainer()->get( 'app.orion.batch' );

		$list  = $this->getEntityList();
		$count = 0;

		while ( ! empty( $list ) ) {
			foreach ( $list as $entity ) {
				$this->deleteEntity( $entity->id );
				$count ++;
			}

			$list = $this->getEntityList();
		}

		$output->writeln( '<info>' . $count . ' entities deleted</info>' );
	}

	private function deleteEntity( $id ) {
		try {
			$response = $this->client->delete( '/v2/entities/' . $id );
			$response->getBody()->rewind();
			$content = json_decode( $response->getBody()->getContents() );
		} catch ( RequestException $e ) {

			if ( $e->getResponse() ) {
				$body        = json_decode( $e->getResponse()->getBody()->getContents() );
				$error       = isset( $body->error ) ? $body->error : 'UNKNOWN';
				$description = isset( $body->description ) ? $body->description : 'UNKNOWN';
				throw new Exception( "Orion Error: " . $e->getCode() . ', ' . $error . ', ' . $description );
			} else {
				throw $e;
			}
		}
	}

	private function getEntityList() {
		$query = [
			'idPattern' => 'urn:oc:entity:aarhus.*',
		];

		try {
			$response = $this->client->get( '',
				[
					'query' => $query,
				] );
			$response->getBody()->rewind();
			$content = json_decode( $response->getBody()->getContents() );
		} catch ( RequestException $e ) {

			if ( $e->getResponse() ) {
				$body        = json_decode( $e->getResponse()->getBody()->getContents() );
				$error       = isset( $body->error ) ? $body->error : 'UNKNOWN';
				$description = isset( $body->description ) ? $body->description : 'UNKNOWN';
				throw new Exception( "Orion Error: " . $e->getCode() . ', ' . $error . ', ' . $description );
			} else {
				throw $e;
			}
		}

		return $content;
	}
}