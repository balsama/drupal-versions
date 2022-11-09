<?php

namespace Balsama\DrupalVersionTestMatrix;

class ProjectReleaseInfo
{
    public function __construct(
        public string $supportedBranches,
        public array $releases,
        public string $servedFrom = 'unknown',
        public int $generatedTimestamp = 0,
    ) {
    }
}
