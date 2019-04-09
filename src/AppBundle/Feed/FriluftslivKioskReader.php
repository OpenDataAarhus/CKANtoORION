<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35.
 */

namespace AppBundle\Feed;

class FriluftslivKioskReader extends BaseFriluftslivPointReader
{
    protected $feed_path = '/dataset/2d5e2ffa-2341-4b32-876f-73b1df1ffa04/resource/3ace0e47-46a7-4ab8-9d7a-2da81fff641f/download/kioskerwgs84.json';

    protected $id_string = 'kiosks';
    protected $type = 'kiosk';
    protected $origin_value = 'Kiosks from Friluftliv Aarhus';
    protected $origin_url = 'https://portal.opendata.dk/dataset/kiosker';
}
