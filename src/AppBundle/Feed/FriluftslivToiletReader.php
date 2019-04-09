<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35.
 */

namespace AppBundle\Feed;

class FriluftslivToiletReader extends BaseFriluftslivPointReader
{
    protected $feed_path = '/dataset/c637921c-6ad8-4c1a-bd8d-9dac8605c2d4/resource/1bd17c11-f20f-4db6-ab2a-a56baaf62980/download/toiletwgs84.json';

    protected $id_string = 'toilets';
    protected $type = 'toilet';
    protected $origin_value = 'Toiltes from Friluftliv Aarhus';
    protected $origin_url = 'https://portal.opendata.dk/dataset/toiletter-ved-parker-og-skove-i-aarhus-kommune';
}
