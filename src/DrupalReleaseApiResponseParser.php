<?php

namespace Balsama\DrupalVersionTestMatrix;

use GuzzleHttp\Psr7\Response;
use Noodlehaus\Exception\ParseException;
use Noodlehaus\Parser\Xml;

class DrupalReleaseApiResponseParser
{
    private ProjectReleaseInfo $drupalReleaseProjectInfo;

    /**
     * @throws ParseException
     */
    public function __construct(Response $response)
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
    private function parseResponse(Response $response): ProjectReleaseInfo
    {
        $parser = new Xml();
        $projectInfo = $parser->parseString($response->getBody());
        return new ProjectReleaseInfo(
            $projectInfo['supported_branches'],
            $projectInfo['releases']['release'],
        );
    }
}
