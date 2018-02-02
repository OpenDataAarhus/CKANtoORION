<?php

namespace AppBundle\Controller;

use AppBundle\Feed\Dokk1CountersJob;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller {
	/**
	 * @Route("/", name="homepage")
	 * @Method("GET")
	 */
	public function indexAction( Request $request ) {
		// replace this example code with whatever you need
		return $this->render( 'default/index.html.twig',
			[
				'base_dir' => realpath( $this->getParameter( 'kernel.root_dir' ) . '/..' ),
			] );
	}

	/**
	 * @Route("/dokk1counters", name="dokk1_counters")
	 * @Method("GET")
	 */
	public function dokk1CountersAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/realtimetraffic", name="real_time_traffic")
	 * @Method("GET")
	 */
	public function realTimeTrafficAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_firepits", name="friluftsliv_firepits")
	 * @Method("GET")
	 */
	public function friluftslivFirepitsAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_fitness", name="friluftsliv_fitness")
	 * @Method("GET")
	 */
	public function friluftslivFitnessGymAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_gearstation", name="friluftsliv_gearstation")
	 * @Method("GET")
	 */
	public function friluftslivGearStationAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_shelter", name="friluftsliv_shelter")
	 * @Method("GET")
	 */
	public function friluftslivShelterAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_naturecenter", name="friluftsliv_naturecenter")
	 * @Method("GET")
	 */
	public function friluftslivNaturecenterAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_toilet", name="friluftsliv_toilet")
	 * @Method("GET")
	 */
	public function friluftslivToiletAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_treeclimbing", name="friluftsliv_treeclimbing")
	 * @Method("GET")
	 */
	public function friluftslivTreeClimbingAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_beacharea", name="friluftsliv_beacharea")
	 * @Method("GET")
	 */
	public function friluftslivBeachAreaAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_dagwalkingarea", name="friluftsliv_dagwalkingarea")
	 * @Method("GET")
	 */
	public function friluftslivDogWalkingAreaAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_parks", name="friluftsliv_parks")
	 * @Method("GET")
	 */
	public function friluftslivParkAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_forests", name="friluftsliv_forests")
	 * @Method("GET")
	 */
	public function friluftslivForestAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_forests_small", name="friluftsliv_forests_small")
	 * @Method("GET")
	 */
	public function friluftslivForestSmallAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_hikingtrails", name="friluftsliv_hikingtrails")
	 * @Method("GET")
	 */
	public function friluftslivHikingTrailsAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_mountainbiketrails", name="friluftsliv_mountainbiketrails")
	 * @Method("GET")
	 */
	public function friluftslivMountainBikeTrailsAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_runningtrails", name="friluftsliv_runningtrails")
	 * @Method("GET")
	 */
	public function friluftslivRunnningTrailsAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/friluftsliv_horseridingtrails", name="friluftsliv_horseridingtrails")
	 * @Method("GET")
	 */
	public function friluftslivHorseRidingTrailsAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/detskeriaarhus", name="detskeriaarhus")
	 * @Method("GET")
	 */
	public function detskeriaarhusAction( Request $request ) {
		return $this->baseFeedReaderAction( $request );
	}

	/**
	 * @Route("/routes", name="routes")
	 * @Method("GET")
	 * @Template("routes.html.twig")
	 *
	 * @return array
	 */
	public function routeAction() {
		/** @var Router $router */
		$router = $this->get( 'router' );
		$routes = $router->getRouteCollection();

		foreach ( $routes as $route ) {
			$this->convertController( $route );
		}

		return [
			'routes' => $routes,
		];
	}


	private function baseFeedReaderAction( Request $request ) {
		$routeName = $request->get( '_route' );
		$feed      = $this->get( 'app.feed_reader_factory' )->getFeedReader( $routeName );
		$assets    = $feed->normalizeForOrganicity();

		$selection = array_slice( $assets, 0, 5, true );

		return new JsonResponse( $selection );
	}


	private function convertController( \Symfony\Component\Routing\Route $route ) {
		$nameParser = $this->get( 'controller_name_converter' );
		if ( $route->hasDefault( '_controller' ) ) {
			try {
				$route->setDefault( '_controller', $nameParser->build( $route->getDefault( '_controller' ) ) );
			} catch ( \InvalidArgumentException $e ) {
			}
		}
	}

}
