<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Interfaces\Parser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

abstract class BaseParser implements Parser
{
    /**
     * @var DocblockParser
     */
    protected $docblockParser;

    /**
     * AttributeParser constructor.
     * @param DocblockParser $docblockParser
     */
    public function __construct(DocblockParser $docblockParser)
    {
        $this->docblockParser = $docblockParser;
    }

    /**
     * parses
     *
     * @param int $version
     * @param ResourceManager $resourceManager
     * @param string $resourceName
     * @param ResourceDocumentation $resourceDocumentation
     * @return void
     */
    abstract function parse(
        int $version,
        ResourceManager $resourceManager,
        string $resourceName,
        ResourceDocumentation $resourceDocumentation
    );

    /**
     * returns serializer doc block
     *
     * @param ResourceManager $resourceManager
     * @param string $resourceName
     * @return string
     */
    protected function getSerializerDocBlock(ResourceManager $resourceManager, string $resourceName): string
    {
        $serializer = $resourceManager->resolveSerializer($resourceName);
        $reflectionClass = new \ReflectionClass($serializer);
        return $reflectionClass->getDocComment();
    }

    /**
     * returns request handler doc block
     *
     * @param ResourceManager $resourceManager
     * @param string $resourceName
     * @return string
     */
    protected function getRequestHandlerDocBlock(ResourceManager $resourceManager, string $resourceName): string
    {
        $requestHandler = $resourceManager->resolveRequestHandler($resourceName);
        $reflectionClass = new \ReflectionClass($requestHandler);
        return $reflectionClass->getDocComment();
    }
}
