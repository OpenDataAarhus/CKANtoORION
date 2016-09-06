<?php

namespace AppBundle\Job;

use ResqueBundle\Resque\ContainerAwareJob;

class TestJob extends ContainerAwareJob
{
  public function run($args)
  {

  }
}
