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
    'notes' => [
        'This service does not include information on Drupal 7.'
    ],
    'links' => [
        'Drupal core release cycle: major, minor, and patch releases' => 'https://www.drupal.org/about/core/policies/core-release-cycles/schedule',
        'Continuous upgrades between major versions' => 'https://www.drupal.org/about/core/policies/core-change-policies/continuous-upgrades-between-major-versions',
        'Drupal core releases XML feed' => 'https://updates.drupal.org/release-history/drupal/current',
    ],
];

print json_encode($response);
