<?php
include_once __DIR__ . '/../vendor/autoload.php';
use Balsama\DrupalVersionTestMatrix\DrupalReleaseApiClient;
use Balsama\DrupalVersionTestMatrix\DrupalReleaseApiResponseParser;
use Balsama\DrupalVersionTestMatrix\DrupalReleaseVersionsMatrix;
use GuzzleHttp\Client;

$forceRefresh = false;
if (array_key_exists('forceRefresh', $_GET)) {
    $forceRefresh = 'true';
}

$client = new Client();
$drupalReleaseApiClient = new DrupalReleaseApiClient($client, $forceRefresh);
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
    'generatedFrom' => $drupalReleaseVersionsMatrix->projectReleaseInfo->servedFrom,
    'generatedTimestamp' => $drupalReleaseVersionsMatrix->projectReleaseInfo->generatedTimestamp,
];

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: inline; filename="drupal-versions-' . $drupalReleaseVersionsMatrix->projectReleaseInfo->generatedTimestamp . '.json"');
print json_encode($response, JSON_PRETTY_PRINT);
