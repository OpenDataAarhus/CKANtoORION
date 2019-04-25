<?php

namespace AppBundle\Command;

use AppBundle\Job\CityLabJob;
use AppBundle\Job\CityProbeJob;
use AppBundle\Job\Dokk1BookReturnsJob;
use AppBundle\Job\Dokk1CountersJob;
use AppBundle\Job\RealTimeParkingJob;
use AppBundle\Job\RealTimeSolarArrayJob;
use AppBundle\Job\RealTimeTrafficJob;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobsTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:jobs:test')
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates feeds.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command sets up the repeating jobs for CKANtoORION');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get resque
        $resque = $this->getContainer()->get('ResqueBundle\Resque\Resque');
        $jobsService = $this->getContainer()->get('app.jobs_service');

        // create your job
        $job = new CityLabJob();
//        $job = new CityProbeJob([]);
//        $job = new Dokk1BookReturnsJob();
//        $job = new Dokk1CountersJob();
//        $job = new RealTimeParkingJob();
//        $job = new RealTimeSolarArrayJob();
//        $job = new RealTimeTrafficJob();

        $args = [
            'kernel.root_dir' => '/vagrant/htdocs/app',
            'kernel.debug' => true,
            'kernel.environment' => 'dev',
            'resque.retry_strategy' => [60],
            'resque.retry_attempt' => 1,
        ];

        $job->run($args);
    }
}
