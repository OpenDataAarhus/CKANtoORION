<?php

namespace AppBundle\OrionSync;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use ResqueBundle\Resque\ContainerAwareJob;

class SyncJob extends ContainerAwareJob
{
    public function __construct($args = [])
    {
        parent::__construct($args);

        $this->queue = 'orion_sync';
    }

    public function run($args)
    {
        $assets = $args['assets'];

        $this->batchAction($assets);
    }

    /**
     * @param $assets
     * @param $actionType APPEND or UPDATE
     *
     * @throws Exception
     */
    protected function batchAction(&$assets, $actionType = 'APPEND'): void
    {
        $client = $this->getContainer()->get('app.orion.batch');

        $body = [
            'actionType' => $actionType,
            'entities' => array_values($assets),
        ];
        $json = json_encode($body);

        try {
            $response = $client->post('',
                [
                    'body' => $json,
                ]);
            $response->getBody()->rewind();
        } catch (ServerException $e) {
            if ($e->hasResponse()) {
                $errorCode = $e->getResponse()->getStatusCode();
                $reasonPhrase = $e->getResponse()->getReasonPhrase();
                $content = $e->getResponse()->getBody()->getContents();
                throw new Exception('Orion/ServerException: '.$errorCode.', '.$reasonPhrase.' - Body: '.substr($content, 0, 250).'...');
            } else {
                throw new Exception('Guzzle/ServerException - Request Body: '.$json.' - '.$e->getMessage());
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $body = json_decode($e->getResponse()->getBody()->getContents());
                $error = $body->error ?? 'UNKNOWN';
                $description = $body->description ?? 'UNKNOWN';
                throw new Exception('Orion/RequestException: '.$e->getCode().', '.$error.', '.$description.' - Request Body: '.$json);
            } else {
                throw new Exception('Guzzle/RequestException - Request Body: '.$json.' - '.$e->getMessage());
            }
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $body = json_decode($e->getResponse()->getBody()->getContents());
                $error = $body->error ?? 'UNKNOWN';
                $description = $body->description ?? 'UNKNOWN';
                throw new Exception('Orion/ClientException: '.$e->getCode().', '.$error.', '.$description.' - Request Body: '.$json);
            } else {
                throw new Exception('Guzzle/ClientException - Request Body: '.$json.' - '.$e->getMessage());
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
