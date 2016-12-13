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

class FriluftslivNaturCenterReader extends BaseFriluftslivPointReader
{

  protected $feed_path = '/dataset/f950bf36-90fe-481d-a780-b68810abbbdb/resource/2b660ef1-80af-4b68-9abb-b04732f8c6f1/download/NaturcentreGrejbankerWGS84.json';

  protected $id_string = 'naturecenters';
  protected $type = 'naturecenter';
  protected $origin_value = 'Nature Centers from Friluftliv Aarhus';
  protected $origin_url = 'https://www.odaa.dk/dataset/naturcentre';

}