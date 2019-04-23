<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35.
 */

namespace AppBundle\Feed;

class FriluftslivFirepitsReader extends BaseFriluftslivPointReader
{
    protected $feed_path = '/dataset/16f6f2bd-5b57-4c37-ad3e-f4d930dff417/resource/e07c7c97-e522-481b-97bd-a3cf0b67da47/download/baalpladswgs84.json';

    protected $id_string = 'firepits';
    protected $type = 'firepit';
    protected $origin_value = 'Public Firepits from Friluftliv Aarhus';
    protected $origin_url = 'https://portal.opendata.dk/dataset/balpladser-i-aarhus';
}
