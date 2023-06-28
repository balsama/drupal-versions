<?php

namespace Balsama\DrupalVersionTestMatrix;

class MajorVersion
{
    private const MINOR_ATTRIBUTES = [
        'version' => null,
        'latest' => null,
        'devBranch' => null,
    ];
    private array $info = [
        'current' => self::MINOR_ATTRIBUTES,
        'previous' => self::MINOR_ATTRIBUTES,
        'next' => self::MINOR_ATTRIBUTES,
    ];

    public function __construct(
        private readonly array $releases,
        public readonly int|null $major,
        public readonly int|null $currentMinor,
        public readonly int|null $nextMinor = null,
        public readonly int|null $previousMinor = null,
    ) {
        $this->fillInfo();
    }

    public function getInfo(): array
    {
        return $this->info;
    }

    private function fillInfo(): void
    {
        $this->info['current']['version'] = $this->currentMinor;
        $this->info['current']['latest'] = $this->getCurrentMinorLatestRelease();
        $this->info['current']['devBranch'] = $this->getCurrentMinorDevBranch();

        $this->info['previous']['version'] = $this->previousMinor;
        $this->info['previous']['latest'] = $this->getLastMinorLatestRelease();
        $this->info['previous']['devBranch'] = $this->getLastMinorDevBranch();

        $this->info['next']['version'] = $this->nextMinor;
        $this->info['next']['latest'] = $this->getNextMinorLatestRelease();
        $this->info['next']['devBranch'] = $this->getNextMinorDevBranch();
    }

    public function getCurrentMinorLatestRelease(): ?string
    {
        if ($this->currentMinor === null) {
            return null;
        }
        return $this->getLatestRelease($this->major, $this->currentMinor);
    }
    public function getCurrentMinorDevBranch(): ?string
    {
        if ($this->currentMinor === null) {
            return null;
        }
        return "$this->major.$this->currentMinor.x-dev";
    }

    public function getNextMinorLatestRelease(): ?string
    {
        if ($this->nextMinor === null) {
            return null;
        }
        return $this->getLatestRelease($this->major, $this->nextMinor);
    }
    public function getNextMinorDevBranch(): ?string
    {
        if ($this->nextMinor === null) {
            return null;
        }
        return "$this->major.$this->nextMinor.x-dev";
    }

    public function getLastMinorLatestRelease(): ?string
    {
        if ($this->previousMinor === null) {
            return null;
        }
        return $this->getLatestRelease($this->major, $this->previousMinor);
    }
    public function getLastMinorDevBranch(): ?string
    {
        if ($this->previousMinor === null) {
            return null;
        }
        return "$this->major.$this->previousMinor.x-dev";
    }

    private function getLatestRelease(int $major, int $minor): ?string
    {
        $potentialLatest = [];
        foreach ($this->releases as $release) {
            if (str_starts_with($release['version'], "$major.$minor")) {
                $potentialLatest[] = $release['version'];
            }
        }
        $sortedPotentialReleases = Helpers::sortReleases($potentialLatest);
        $latestRelease = reset($sortedPotentialReleases);
        if (Helpers::isDevRelease($latestRelease)) {
            return null;
        }

        return $latestRelease;
    }
}
