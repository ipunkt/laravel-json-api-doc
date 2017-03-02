<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Interfaces;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

interface Parser
{
    /**
     * parses
     *
     * @param int $version
     * @param ResourceManager $resourceManager
     * @param string $resourceName
     * @param ResourceDocumentation $resourceDocumentation
     * @return void
     */
    function parse(int $version, ResourceManager $resourceManager, string $resourceName, ResourceDocumentation $resourceDocumentation);
}
