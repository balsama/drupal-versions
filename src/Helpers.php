<?php

namespace Balsama\DrupalVersionTestMatrix;

use Composer\Semver\Comparator;

class Helpers
{
    /**
     * Sorts an array of SemVer release string in order of descending precedence.
     *
     * @param string[] $releases
     * @return string[]
     */
    public static function sortReleases(array $releases): array
    {
        usort($releases, function ($a, $b) {
            // PHP 8.1 complains about Comparator returning bools, so converting to int.
            if (Comparator::lessThan($a, $b) === false) {
                return -1;
            }
            if (Comparator::lessThan($a, $b) === true) {
                return 1;
            }
            return 0;
        });
        return $releases;
    }

    public static function isDevRelease(string $releaseName): bool
    {
        if (str_ends_with($releaseName, '-dev')) {
            return true;
        }
        return false;
    }
}
