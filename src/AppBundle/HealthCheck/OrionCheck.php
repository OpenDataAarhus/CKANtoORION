<?php

namespace AppBundle\HealthCheck;

use GuzzleHttp\Client;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class OrionCheck implements CheckInterface
{
    private $orionClient;

    public function __construct(Client $orionClient)
    {
        $this->orionClient = $orionClient;
    }

    public function check()
    {
        $params = [
            'query' => [
                'limit' => 1,
                'idPattern' => 'urn:oc:entity:aarhus:traffic:fixed:*',
                'orderBy' => '!TimeInstant',
            ],
        ];

        try {
            $response = $this->orionClient->request('GET', '', $params);
            $response->getBody()->rewind();

            if (200 === $response->getStatusCode()) {
                $body = $response->getBody()->getContents();
                $entities = json_decode($body, false);

                if ($entities) {
                    $entity = is_array($entities) ? $entities[0] : null;

                    if ($entity && isset($entity->TimeInstant, $entity->TimeInstant->value)) {
                        $entityTimestamp = new \DateTime($entity->TimeInstant->value);

                        $interval = new \DateInterval('PT600S');
                        $now = new \DateTime();

                        if ($entityTimestamp > $now->sub($interval)) {
                            return new Success('Entity is newer than interval');
                        } else {
                            return new Failure('Entity is to old ['.$entityTimestamp->format('c').']');
                        }
                    }
                }

                return new Failure('Cannot parse response body');
            } else {
                return new Failure('HTTP error '.$response->getStatusCode());
            }
        } catch (\Exception $e) {
            return new Failure('Exception: '.$e->getMessage());
        }

        return new Failure('Unknown error state');
    }

    public function getLabel(): string
    {
        return 'Orion status';
    }
}
