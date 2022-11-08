<?php
include_once __DIR__ . '/../vendor/autoload.php';
use Balsama\DrupalVersionTestMatrix\DrupalReleaseApiClient;
use Balsama\DrupalVersionTestMatrix\DrupalReleaseApiResponseParser;
use Balsama\DrupalVersionTestMatrix\DrupalReleaseVersionsMatrix;
use GuzzleHttp\Client;

$client = new Client();
$drupalReleaseApiClient = new DrupalReleaseApiClient($client);
$drupalReleaseResponseParser = new DrupalReleaseApiResponseParser($drupalReleaseApiClient->getProjectReleaseResponse());
$drupalReleaseVersionsMatrix = new DrupalReleaseVersionsMatrix($drupalReleaseResponseParser->getProjectReleaseInfo());


$response = [
    'majors' => [
        'current' => [
            'version' => $drupalReleaseVersionsMatrix->current->major,
            'minors' => $drupalReleaseVersionsMatrix->current->getInfo(),
        ],
        'previous' => [
            'version' => $drupalReleaseVersionsMatrix->previous->major,
            'minors' => $drupalReleaseVersionsMatrix->previous->getInfo()
        ],
        'next' => [
            'version' => $drupalReleaseVersionsMatrix->next->major,
            'minors' => $drupalReleaseVersionsMatrix->next->getInfo(),
        ],
    ],
    'status' => 200,
];

print json_encode($response);
