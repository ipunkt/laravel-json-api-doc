<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parameter;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class FilterParser extends BaseParser
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
    function parse(
        int $version,
        ResourceManager $resourceManager,
        string $resourceName,
        ResourceDocumentation $resourceDocumentation
    )
    {
        $filterFactory = $resourceManager->resolveFilterFactory($resourceName, [], $version);

        $availableFilters = $filterFactory->allAvailable();
        foreach ($availableFilters as $filterName => $filterClasspath) {
            $reflectionClass = new \ReflectionClass($filterClasspath);
            $classDocblock = $reflectionClass->getDocComment();

            $examples = $this->docblockParser->findTags('example', $classDocblock);
            $example = implode(' ', $examples);

            $comments = $this->docblockParser->findTags('brief', $classDocblock);
            $comment = implode(' ', $comments);

            $this->setFilter($classDocblock, $filterName, $comment, $example, $resourceDocumentation);
            $this->setQuery($classDocblock, $filterName, $comment, $example, $resourceDocumentation);
        }
    }

    /**
     * set a filter
     *
     * @param string $docBlock
     * @param string $filterName
     * @param string $comment
     * @param string $example
     * @param ResourceDocumentation $resourceDocumentation
     */
    private function setFilter(
        string $docBlock,
        string $filterName,
        string $comment,
        string $example,
        ResourceDocumentation $resourceDocumentation
    )
    {
        if ($this->docblockParser->hasTag('nofilter', $docBlock)) {
            return;
        }

        $filter = $this->buildParameter('filter', $filterName, $comment, $example);

        $resourceDocumentation->addParameter($filter);
    }

    /**
     * set a query
     *
     * @param string $docBlock
     * @param string $filterName
     * @param string $comment
     * @param string $example
     * @param ResourceDocumentation $resourceDocumentation
     */
    private function setQuery(
        string $docBlock,
        string $filterName,
        string $comment,
        string $example,
        ResourceDocumentation $resourceDocumentation
    )
    {
        if ($this->docblockParser->hasTag('noquery', $docBlock)) {
            return;
        }

        $parameter = $this->buildParameter('query', $filterName, $comment, $example);

        $resourceDocumentation->addParameter($parameter);
    }

    /**
     * build a parameter
     *
     * @param string $type
     * @param string $parameterName
     * @param string $comment
     * @param string $example
     * @return Parameter
     */
    private function buildParameter(string $type, string $parameterName, string $comment, string $example)
    {
        $parameter = new Parameter();
        $parameter->setName($type . '[' . $parameterName . ']');
        $parameter->setComment($comment);
        $parameter->setExample($example);

        return $parameter;
    }
}
