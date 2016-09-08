<?php

namespace AppBundle\Controller;

use AppBundle\Feed\Dokk1CountersJob;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
  /**
   * @Route("/", name="homepage")
   * @Method("GET")
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
   * @Method("GET")
   */
  public function dokk1CountersAction(Request $request)
  {
    $feed = $this->get('app.feed_reader_factory')->getFeedReader('dokk1_counters');
    $assets = $feed->normalizeForOrganicity();

    $feed->syncToOrganicity();

    $selection = array_slice($assets, 0, 5, true);

    return new JsonResponse($selection);
  }

  /**
   * @Route("/realtimetraffic", name="realtimetraffic")
   * @Method("GET")
   */
  public function realTimeTrafficAction(Request $request)
  {
    $feed = $this->get('app.feed_reader_factory')->getFeedReader('real_time_traffic');
    $assets = $feed->normalizeForOrganicity();

    $selection = array_slice($assets, 0, 5, true);

    return new JsonResponse($selection);
  }

  /**
   * @Route("/friluftsliv_firepits", name="friluftsliv_firepits")
   * @Method("GET")
   */
  public function friluftslivFirepitsAction(Request $request)
  {
    $feed = $this->get('app.feed_reader_factory')->getFeedReader('friluftsliv_firepits');
    $assets = $feed->normalizeForOrganicity();

    $selection = array_slice($assets, 0, 5, true);

    return new JsonResponse($selection);
  }

  /**
   * @Route("/friluftsliv_fitness", name="friluftsliv_fitness")
   * @Method("GET")
   */
  public function friluftslivFitnessGymAction(Request $request)
  {
    $feed = $this->get('app.feed_reader_factory')->getFeedReader('friluftsliv_fitness');
    $assets = $feed->normalizeForOrganicity();

    $selection = array_slice($assets, 0, 5, true);

    return new JsonResponse($selection);
  }

  /**
   * @Route("/friluftsliv_gearstation", name="friluftsliv_gearstation")
   * @Method("GET")
   */
  public function friluftslivGearStationAction(Request $request)
  {
    $feed = $this->get('app.feed_reader_factory')->getFeedReader('friluftsliv_gearstation');
    $assets = $feed->normalizeForOrganicity();

    $selection = array_slice($assets, 0, 5, true);

    return new JsonResponse($selection);
  }

  /**
   * @Route("/friluftsliv_shelter", name="friluftsliv_shelter")
   * @Method("GET")
   */
  public function friluftslivShelterAction(Request $request)
  {
    $feed = $this->get('app.feed_reader_factory')->getFeedReader('friluftsliv_shelter');
    $assets = $feed->normalizeForOrganicity();

    $selection = array_slice($assets, 0, 5, true);

    return new JsonResponse($selection);
  }

  /**
   * @Route("/routes", name="routes")
   * @Method("GET")
   * @Template("routes.html.twig")
   *
   * @return array
   */
  public function routeAction()
  {
    /** @var Router $router */
    $router = $this->get('router');
    $routes = $router->getRouteCollection();

    foreach ($routes as $route) {
      $this->convertController($route);
    }

    return [
      'routes' => $routes
    ];
  }


  private function convertController(\Symfony\Component\Routing\Route $route)
  {
    $nameParser = $this->get('controller_name_converter');
    if ($route->hasDefault('_controller')) {
      try {
        $route->setDefault('_controller', $nameParser->build($route->getDefault('_controller')));
      } catch (\InvalidArgumentException $e) {
      }
    }
  }

}
