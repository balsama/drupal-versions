<?php

namespace Balsama\DrupalVersionTestMatrix;

class ProjectReleaseInfo
{
    public function __construct(
        public string $supportedBranches,
        public array $releases,
    ) {
    }
}
