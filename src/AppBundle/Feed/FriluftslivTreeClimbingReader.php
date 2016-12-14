<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35
 */

namespace AppBundle\Feed;

use Exception;
use DateTime;
use DateTimeZone;
use stdClass;

class FriluftslivTreeClimbingReader extends BaseFriluftslivGeoJsonReader
{
  protected $feed_path = '/dataset/3f319888-a952-40d9-9ce6-c9ab2b9a0e3a/resource/0c6930f7-c291-408a-8049-839f09659b4e/download/TraeklatringWGS84.json';

  protected $id_string = 'treeclimbing';
  protected $type = 'treeclimbing';
  protected $origin_value = 'Tree Climbing locations from Friluftliv Aarhus';
  protected $origin_url = 'https://www.odaa.dk/dataset/traeklatring';

}