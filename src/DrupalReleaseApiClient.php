<?php

namespace Balsama\DrupalVersionTestMatrix;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DrupalReleaseApiClient
{
    private const APIURIPATTERN = 'https://updates.drupal.org/release-history/%s/current';
    private const OBJCACHEFILEPATTERN = 'response_%o.obj';
    private const OBJCACHEDIR = __DIR__ . '/../cache/';
    private const CACHEEXPIRYAGE = 10800;
    private Client $httpClient;
    private string $project;
    private string $projectReleaseResponse;
    private Filesystem $fs;
    private Finder $finder;
    private string|null $cachedResponse;
    private bool $forceOriginRefresh;

    public function __construct(Client $httpClient, bool $forceOriginRefresh = false, string $project = 'drupal')
    {
        $this->fs = new Filesystem();
        $this->finder = new Finder();
        $this->httpClient = $httpClient;
        $this->project = $project;
        $this->forceOriginRefresh = $forceOriginRefresh;
        $this->cachedResponse = $this->getResponseObjectCache();
        $this->projectReleaseResponse = $this->fetchResponse();
    }

    public function getProjectReleaseResponse(): string
    {
        return $this->projectReleaseResponse;
    }

    private function fetchResponse($retryOnError = 5): string
    {
        if (!$this->forceOriginRefresh) {
            if ($this->cachedResponse) {
                return $this->cachedResponse;
            }
        }

        $uri = sprintf(self::APIURIPATTERN, $this->project);
        try {
            $options = ['headers' => ['User-Agent' => 'DrupalVersionMatrix/v1.0']];
            $response = $this->httpClient->get($uri, $options);
        } catch (ServerException | GuzzleException $e) {
            if ($retryOnError) {
                $retryOnError--;
                usleep(250000);
                return $this->fetchResponse($retryOnError);
            }
            throw $e;
        }

        $xml = $response->getBody()->getContents();
        $time = time();
        $response = [
            'xml' => $xml,
            'generatedTimestamp' => $time,
            'servedFrom' => 'cache',
        ];

        $this->writeResponseObjectBodyToFileCache(json_encode($response));

        $response['servedFrom'] = 'origin';
        return json_encode($response);
    }

    private function writeResponseObjectBodyToFileCache(string $response): void
    {
        file_put_contents(sprintf(self::OBJCACHEDIR . self::OBJCACHEFILEPATTERN, time()), $response);
    }

    public function getResponseObjectCache(): ?string
    {
        $cacheDirFiles = $this->finder
            ->files()
            ->in(self::OBJCACHEDIR)
            ->sortByName()
            ->reverseSorting()
            ->filter(static function (SplFileInfo $file) {
                \preg_match('/\.(obj)$/', $file->getPathname());
            });
        if (!$cacheDirFiles->hasResults()) {
            return null;
        }

        $iterator = $cacheDirFiles->getIterator();
        $iterator->current();
        $latest = $iterator->current();
        $cacheDirFiles->files();
        foreach ($cacheDirFiles as $cacheFile) {
            if ($cacheFile->getFilename() !== $latest->getFilename()) {
                // Clean up any old cache files.
                $this->fs->remove($cacheFile);
            }
        }

        if ((time() - $latest->getATime()) > self::CACHEEXPIRYAGE) {
            $this->fs->remove($latest);
            return null;
        }

        return $latest->getContents();
    }
}
