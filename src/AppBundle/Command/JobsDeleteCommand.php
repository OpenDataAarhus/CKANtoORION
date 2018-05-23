<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobsDeleteCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			// the name of the command (the part after "bin/console")
			->setName( 'app:jobs:delete' )
			// the short description shown while running "php bin/console list"
			->setDescription( 'Delete All Jobs.' )
			// the full command description shown when running the command with
			// the "--help" option
			->setHelp( "This command delete all repeating jobs for CKANtoORION" );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		// get resque
		$resque = $this->getContainer()->get( 'ResqueBundle\Resque\Resque' );
		$resque->clearQueue( 'default' );


		// https://github.com/resquebundle/resque/issues/13
		$timestamps = \Resque::redis()->zrange( 'delayed_queue_schedule', 0, - 1 );
		foreach ( $timestamps as $timestamp ) {
			\Resque::redis()->del( 'delayed:' . $timestamp );
		}
		\Resque::redis()->del( 'delayed_queue_schedule' );

	}
}