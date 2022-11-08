<?php

namespace Balsama\DrupalVersionTestMatrix;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;

class DrupalReleaseApiClient
{
    private const APIURIPATTERN = 'https://updates.drupal.org/release-history/%s/current';
    private Client $httpClient;
    private string $project;
    private Response $projectReleaseResponse;

    public function __construct(Client $httpClient, string $project = 'drupal')
    {
        $this->httpClient = $httpClient;
        $this->project = $project;
        $this->projectReleaseResponse = $this->fetchResponse();
    }

    public function getProjectReleaseResponse(): Response
    {
        return $this->projectReleaseResponse;
    }

    private function fetchResponse($retryOnError = 5): Response
    {
        $uri = sprintf(self::APIURIPATTERN, $this->project);
        try {
            $options = ['headers' => ['User-Agent' => 'DrupalVersionMatrix/v1.0']];
            $response = $this->httpClient->get($uri, $options);
        } catch (ServerException $e) {
            if ($retryOnError) {
                $retryOnError--;
                usleep(250000);
                return $this->fetchResponse($retryOnError);
            }
            throw $e;
        } catch (GuzzleException $e) {
        }

        return $response;
    }
}
