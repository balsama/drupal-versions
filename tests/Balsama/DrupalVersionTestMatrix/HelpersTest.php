<?php

namespace Balsama\DrupalVersionTestMatrix;

class HelpersTest extends \PHPUnit\Framework\TestCase
{
    public function testSortReleases()
    {
        $releases = [
            '1.2.12',
            '1.2.0-alpha3',
            '1.2.1',
            '1.2.0-rc3',
            '1.2.13',
        ];
        $releases = Helpers::sortReleases($releases);
        $this->assertEquals('1.2.13', reset($releases));
    }
}
