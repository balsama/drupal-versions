<?php

namespace Balsama\DrupalVersionTestMatrix;

use Noodlehaus\Exception\ParseException;
use Noodlehaus\Parser\Xml;

class DrupalReleaseApiResponseParser
{
    private ProjectReleaseInfo $drupalReleaseProjectInfo;

    /**
     * @throws ParseException
     */
    public function __construct(string $response)
    {
        $this->drupalReleaseProjectInfo = $this->parseResponse($response);
    }

    public function getProjectReleaseInfo(): ProjectReleaseInfo
    {
        return $this->drupalReleaseProjectInfo;
    }

    /**
     * @throws ParseException
     */
    private function parseResponse(string $response): ProjectReleaseInfo
    {
        $parser = new Xml();
        $response = json_decode($response);
        $projectInfo = $parser->parseString($response->xml);
        return new ProjectReleaseInfo(
            $projectInfo['supported_branches'],
            $projectInfo['releases']['release'],
            $response->servedFrom,
            $response->generatedTimestamp,
        );
    }
}
