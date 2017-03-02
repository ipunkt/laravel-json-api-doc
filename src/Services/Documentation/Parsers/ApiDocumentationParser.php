<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Illuminate\Support\Collection;
use Ipunkt\LaravelJsonApi\Http\RequestHandlers\DefaultRequestHandler;
use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApi\Resources\ResourceNotDefinedException;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Interfaces\Parser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class ApiDocumentationParser
{
    /**
     * all parsers
     *
     * @var Parser[]
     */
    private $parser = array();

    /**
     * adds a parser
     *
     * @param Parser $parser
     * @return ApiDocumentationParser
     */
    public function addParser(Parser $parser) : self
    {
        $this->parser[] = $parser;

        return $this;
    }

    /**
     * @param int $version
     * @param ResourceManager $resourceManager
     * @return ResourceDocumentation[]|Collection|array
     */
    public function parse(int $version, ResourceManager $resourceManager) : Collection
    {
        $resourceNames = $resourceManager->resources($version);

        $documents = array();
        foreach ($resourceNames as $resourceName) {
            if (!$this->parsable($resourceManager, $resourceName)) {
                continue;
            }
            $resourceDocumentation = new ResourceDocumentation();

            foreach ($this->parser as $parser) {
                $parser->parse($version, $resourceManager, $resourceName, $resourceDocumentation);
            }

            $documents[] = $resourceDocumentation;
        }

        return collect($documents);
    }

    /**
     * is the resource parsable
     *
     * @param ResourceManager $resourceManager
     * @param string $resourceName
     * @return bool
     */
    private function parsable(ResourceManager $resourceManager, string $resourceName) : bool
    {
        $requestHandler = $resourceManager->resolveRequestHandler($resourceName);
        if (get_class($requestHandler) !== DefaultRequestHandler::class) {
            return true;
        }

        try {
            $repository = $resourceManager->resolveRepository($resourceName);
        } catch (ResourceNotDefinedException $e)
        {
            //  no repository -> no api call possible
            return false;
        }

        return true;
    }
}
