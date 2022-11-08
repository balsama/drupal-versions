<?php

namespace Balsama\DrupalVersionTestMatrix;

use PHPUnit\Framework\TestCase;

class DrupalReleaseVersionsMatrixTest extends TestCase
{
    private DrupalReleaseVersionsMatrix $drupalReleaseVersionsMatrix_nov2022;
    private DrupalReleaseVersionsMatrix $drupalReleaseVersionsMatrix_oct2021;
    private DrupalReleaseVersionsMatrix $drupalReleaseVersionsMatrix_jan2023;

    protected function setUp(): void
    {
        parent::setUp();

        $projectReleaseInfo_nov2022 = new ProjectReleaseInfo(
            '10.0.,9.3.,9.4.,9.5.',
            json_decode(file_get_contents(
                __DIR__ . '/../../fixtures/2022-11-04-releases.json'
            ), true),
        );
        $this->drupalReleaseVersionsMatrix_nov2022 = new DrupalReleaseVersionsMatrix($projectReleaseInfo_nov2022);

        $projectReleaseInfo_oct2021 = new ProjectReleaseInfo(
            '9.3,9.2,9.1,8.9',
            json_decode(file_get_contents(
                __DIR__ . '/../../fixtures/2021-10-01-releases.json'
            ), true),
        );
        $this->drupalReleaseVersionsMatrix_oct2021 = new DrupalReleaseVersionsMatrix($projectReleaseInfo_oct2021);

        $projectReleaseInfo_jan2023 = new ProjectReleaseInfo(
            '10.0.,10.1.,9.5.,9.4.',
            json_decode(file_get_contents(
                __DIR__ . '/../../fixtures/2023-01-01-releases.json'
            ), true),
        );
        $this->drupalReleaseVersionsMatrix_jan2023 = new DrupalReleaseVersionsMatrix($projectReleaseInfo_jan2023);
    }

    public function testMajors()
    {
        // Nov 2022
        $this->assertEquals(9, $this->drupalReleaseVersionsMatrix_nov2022->current->major);
        $this->assertEquals(10, $this->drupalReleaseVersionsMatrix_nov2022->next->major);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_nov2022->previous->major);

        // Jan 2023
        $this->assertEquals(10, $this->drupalReleaseVersionsMatrix_jan2023->current->major);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_jan2023->next->major);
        $this->assertEquals(9, $this->drupalReleaseVersionsMatrix_jan2023->previous->major);

        // Oct 2021
        $this->assertEquals(9, $this->drupalReleaseVersionsMatrix_oct2021->current->major);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_oct2021->next->major);
        $this->assertEquals(8, $this->drupalReleaseVersionsMatrix_oct2021->previous->major);
    }

    public function testMinors()
    {
        // Nov 2022
        $this->assertEquals(4, $this->drupalReleaseVersionsMatrix_nov2022->current->currentMinor);
        $this->assertEquals(5, $this->drupalReleaseVersionsMatrix_nov2022->current->nextMinor);
        $this->assertEquals(3, $this->drupalReleaseVersionsMatrix_nov2022->current->previousMinor);

        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_nov2022->previous->currentMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_nov2022->previous->nextMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_nov2022->previous->previousMinor);

        $this->assertEquals(0, $this->drupalReleaseVersionsMatrix_nov2022->next->currentMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_nov2022->next->nextMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_nov2022->next->previousMinor);

        // Jan 2023
        $this->assertEquals(0, $this->drupalReleaseVersionsMatrix_jan2023->current->currentMinor);
        $this->assertEquals(1, $this->drupalReleaseVersionsMatrix_jan2023->current->nextMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_jan2023->current->previousMinor);

        $this->assertEquals(5, $this->drupalReleaseVersionsMatrix_jan2023->previous->currentMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_jan2023->previous->nextMinor);
        $this->assertEquals(4, $this->drupalReleaseVersionsMatrix_jan2023->previous->previousMinor);

        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_jan2023->next->currentMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_jan2023->next->nextMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_jan2023->next->previousMinor);

        // Oct 2021
        $this->assertEquals(2, $this->drupalReleaseVersionsMatrix_oct2021->current->currentMinor);
        $this->assertEquals(3, $this->drupalReleaseVersionsMatrix_oct2021->current->nextMinor);
        $this->assertEquals(1, $this->drupalReleaseVersionsMatrix_oct2021->current->previousMinor);

        $this->assertEquals(9, $this->drupalReleaseVersionsMatrix_oct2021->previous->currentMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_oct2021->previous->nextMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_oct2021->previous->previousMinor);

        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_oct2021->next->currentMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_oct2021->next->nextMinor);
        $this->assertEquals(null, $this->drupalReleaseVersionsMatrix_oct2021->next->previousMinor);
    }

    public function testLatestReleases()
    {
        // Nov 2022
        $this->assertEquals(
            '9.4.8',
            $this->drupalReleaseVersionsMatrix_nov2022->current->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            '9.3.22',
            $this->drupalReleaseVersionsMatrix_nov2022->current->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            '9.5.0-beta2',
            $this->drupalReleaseVersionsMatrix_nov2022->current->getNextMinorLatestRelease()
        );

        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_nov2022->previous->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_nov2022->previous->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_nov2022->previous->getNextMinorLatestRelease()
        );

        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_nov2022->next->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_nov2022->next->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            '10.0.0-beta2',
            $this->drupalReleaseVersionsMatrix_nov2022->next->getNextMinorLatestRelease()
        );

        // Jan 2023
        $this->assertEquals(
            '10.0.0',
            $this->drupalReleaseVersionsMatrix_jan2023->current->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_jan2023->current->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_jan2023->current->getNextMinorLatestRelease()
        );

        $this->assertEquals(
            '9.5.0',
            $this->drupalReleaseVersionsMatrix_jan2023->previous->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            '9.4.8',
            $this->drupalReleaseVersionsMatrix_jan2023->previous->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_jan2023->previous->getNextMinorLatestRelease()
        );

        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_jan2023->next->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_jan2023->next->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_jan2023->next->getNextMinorLatestRelease()
        );

        // Oct 2021
        $this->assertEquals(
            '9.2.6',
            $this->drupalReleaseVersionsMatrix_oct2021->current->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            '9.1.15',
            $this->drupalReleaseVersionsMatrix_oct2021->current->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_oct2021->current->getNextMinorLatestRelease()
        );

        $this->assertEquals(
            '8.9.19',
            $this->drupalReleaseVersionsMatrix_oct2021->previous->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_oct2021->previous->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_oct2021->previous->getNextMinorLatestRelease()
        );

        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_oct2021->next->getCurrentMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_oct2021->next->getLastMinorLatestRelease()
        );
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_oct2021->next->getNextMinorLatestRelease()
        );
    }

    public function testDevBranches()
    {
        $this->assertEquals(
            null,
            $this->drupalReleaseVersionsMatrix_nov2022->previous->getCurrentMinorDevBranch()
        );

        $this->assertEquals(
            '10.0.x-dev',
            $this->drupalReleaseVersionsMatrix_nov2022->next->getNextMinorDevBranch()
        );
    }
}
