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

class FriluftslivMountainBikeTrailsReader extends BaseFriluftslivGeoJsonReader {
	protected $feed_path = '/dataset/1b2a8607-0307-4281-a3ce-ef0182908e21/resource/149d3cf8-7ea6-4cbf-88e2-821d2bffc585/download/mountainbikerutewgs84.json';

	protected $id_string = 'mountainbiketrail';
	protected $type = 'mounyainbiketrails';
	protected $origin_value = 'Mountain Bike Trails from Friluftliv Aarhus';
	protected $origin_url = 'https://portal.opendata.dk/dataset/mountainbikerute';

}