<?php

namespace AppBundle\Controller;

use AppBundle\Feed\Dokk1CountersJob;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
  /**
   * @Route("/", name="homepage")
   */
  public function indexAction(Request $request)
  {
    // replace this example code with whatever you need
    return $this->render('default/index.html.twig', [
      'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
    ]);
  }

  /**
   * @Route("/dokk1counters", name="dokk1counters")
   */
  public function dokk1CountersAction(Request $request)
  {
    $feed = $this->get('app.feed_reader_factory')->getFeedReader('dokk1_counters');
    $assets = $feed->normalizeForOrganicity();

    $selection = array_slice($assets, 0, 5, true);

    return new JsonResponse($selection);
  }

}
