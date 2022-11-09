<?php

namespace Balsama\DrupalVersionTestMatrix;

class DrupalReleaseVersionsMatrix
{
    public ProjectReleaseInfo $projectReleaseInfo;
    public MajorVersion $current;
    public MajorVersion|null $next;
    public MajorVersion|null $previous;

    public function __construct(ProjectReleaseInfo $projectReleaseInfo)
    {
        $this->projectReleaseInfo = $projectReleaseInfo;
        $this->determineMajorsAndMinors();
    }

    private function determineMajorsAndMinors()
    {
        $this->determineCurrentMajorReleases();
        $this->determinePreviousMajorReleases();
        $this->determineNextMajorReleases();
    }

    private function determineCurrentMajorReleases(): void
    {
        $currentMajor = $this->determineCurrentMajor();
        $currentMajorCurrentMinor = $this->determineCurrentMinorOfMajor($currentMajor);
        $currentMajorPreviousMinor = ($currentMajorCurrentMinor === 0) ? null : $currentMajorCurrentMinor - 1;
        $potentialCurrentMajorNextMinor = $currentMajorCurrentMinor + 1;
        $currentMajorNextMinor = ($this->majorMinorBranchIsSupported($currentMajor, $potentialCurrentMajorNextMinor))
            ? $potentialCurrentMajorNextMinor : null;

        $this->current = new MajorVersion(
            self::filterReleasesByMajor($this->projectReleaseInfo->releases, $currentMajor),
            $currentMajor,
            $currentMajorCurrentMinor,
            $currentMajorNextMinor,
            $currentMajorPreviousMinor
        );
    }

    private function determinePreviousMajorReleases(): void
    {
        $previousMajor = $this->determinePreviousMajor();
        if (!$previousMajor) {
            $this->previous = new MajorVersion(
                [],
                null,
                null,
                null,
            );
            return;
        }
        $previousMajorCurrentMinor = $this->determineCurrentMinorOfMajor($previousMajor);
        $potentialPreviousMajorPreviousMinor = ($previousMajorCurrentMinor - 1);
        if ($this->majorMinorBranchIsSupported($previousMajor, $potentialPreviousMajorPreviousMinor)) {
            $previousMajorPreviousMinor = $potentialPreviousMajorPreviousMinor;
        } else {
            $previousMajorPreviousMinor = null;
        }
        $this->previous = new MajorVersion(
            self::filterReleasesByMajor($this->projectReleaseInfo->releases, $previousMajor),
            $previousMajor,
            $previousMajorCurrentMinor,
            null,
            $previousMajorPreviousMinor,
        );
    }

    private function determineNextMajorReleases(): void
    {
        $potentialNextMajor = $this->current->major + 1;
        if (!$this->majorBranchIsSupported($potentialNextMajor)) {
            $this->next = new MajorVersion(
                [],
                null,
                null,
            );
            return;
        }
        $this->next = new MajorVersion(
            self::filterReleasesByMajor($this->projectReleaseInfo->releases, $potentialNextMajor),
            $potentialNextMajor,
            null,
            0,
            null,
        );
    }

    private function determinePreviousMajor(): ?int
    {
        $potentialPreviousMajor = $this->current->major - 1;
        $supportedBranches = explode(',', $this->projectReleaseInfo->supportedBranches);
        foreach ($supportedBranches as $supportedBranch) {
            if (str_starts_with($supportedBranch, $potentialPreviousMajor)) {
                return $potentialPreviousMajor;
            }
        }
        return null;
    }

    private function determineCurrentMinorOfMajor(int $major): int
    {
        $minorVersionsOfMajor = $this->getMinorVersionsOfMajor($major);
        $potentialCurrentMajorMinor = [];
        foreach ($minorVersionsOfMajor as $minorVersionOfMajor) {
            if ($this->minorHasStableRelease($minorVersionOfMajor)) {
                $potentialCurrentMajorMinor[] = $minorVersionOfMajor;
            }
        }
        if (empty($potentialCurrentMajorMinor)) {
            throw new \Exception('No stable minors of major ' . $major . ' found.');
        }

        asort($potentialCurrentMajorMinor, SORT_ASC);
        $parts = explode('.', end($potentialCurrentMajorMinor));
        return (int) $parts[1];
    }

    private function minorHasStableRelease($majorMinor): bool
    {
        foreach ($this->projectReleaseInfo->releases as $release) {
            if (str_starts_with($release['version'], $majorMinor)) {
                if ($this->versionIsStable($release['version'])) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getMinorVersionsOfMajor(int $major): array
    {
        $supportedBranches = explode(',', $this->projectReleaseInfo->supportedBranches);
        $minorVersionsOfMajor = [];
        foreach ($supportedBranches as $supportedBranch) {
            if ($this->getMajorVersionFromVersion($supportedBranch) === $major) {
                $minorVersionsOfMajor[] = $supportedBranch;
            }
        }
        if (empty($minorVersionsOfMajor)) {
            throw new \Exception('No minor branches found for Major version ' . $major . '.');
        }

        return $minorVersionsOfMajor;
    }

    private function determineCurrentMajor(): int
    {
        $majorVersions = $this->getMajorVersions();
        $possibleCurrentMajors = [];
        foreach ($majorVersions as $majorVersion) {
            if ($this->majorVersionHasStableRelease($majorVersion)) {
                $possibleCurrentMajors[] = $majorVersion;
            }
        }
        if (empty($possibleCurrentMajors)) {
            throw new \Exception('No major versions with stable releases found.');
        }
        asort($possibleCurrentMajors, SORT_DESC);

        return end($possibleCurrentMajors);
    }

    private function getMajorVersions(): array
    {
        $supportedBranches = explode(',', $this->projectReleaseInfo->supportedBranches);
        $majorVersions = [];
        foreach ($supportedBranches as $potentialUniqueMajor) {
            $parts = explode('.', $potentialUniqueMajor);
            $majorVersions[] = (int) reset($parts);
        }
        return array_values(array_unique($majorVersions));
    }

    private function majorVersionHasStableRelease(int $majorVersion): bool
    {
        $releases = $this->projectReleaseInfo->releases;
        foreach ($releases as $release) {
            if ($this->getMajorVersionFromVersion($release['version']) === $majorVersion) {
                if ($this->versionIsStable($release['version'])) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getMajorVersionFromVersion(string $version): int
    {
        $parts = explode('.', $version);
        return (int) reset($parts);
    }

    private function versionIsStable(string $version): bool
    {
        $parts = explode('-', $version);
        if (count($parts) === 1) {
            return true;
        }
        return false;
    }

    private static function filterReleasesByMajor($allReleases, int $major)
    {
        $filteredReleases = [];
        foreach ($allReleases as $release) {
            if (str_starts_with($release['version'], $major)) {
                $filteredReleases[] = $release;
            }
        }
        return $filteredReleases;
    }

    private function majorMinorBranchIsSupported(int $major, int $minor): bool
    {
        $branchToCheck = implode('.', [$major, $minor]);
        $supportedBranches = explode(',', $this->projectReleaseInfo->supportedBranches);
        foreach ($supportedBranches as $supportedBranch) {
            if (str_starts_with($supportedBranch, $branchToCheck)) {
                return true;
            }
        }
        return false;
    }

    private function majorBranchIsSupported(int $major): bool
    {
        $supportedBranches = explode(',', $this->projectReleaseInfo->supportedBranches);
        foreach ($supportedBranches as $supportedBranch) {
            if (str_starts_with($supportedBranch, $major)) {
                return true;
            }
        }
        return false;
    }
}
