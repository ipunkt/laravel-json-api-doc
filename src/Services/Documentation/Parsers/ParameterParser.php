<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Exceptions\NoRouteForVerbException;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parameter;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class ParameterParser extends BaseParser
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
        $classDocblock = $this->getRequestHandlerDocBlock($resourceManager, $resourceName);

        $parameters = $this->docblockParser->findTags('parameter', $classDocblock);

        foreach ($parameters as $parameter) {
            list($verb, $type, $name, $comment) = explode(' ', $parameter, 4);

            $parameter = new Parameter();
            $parameter->setName($name)
                ->setType($type);

            if (str_contains($comment, '<xmp>')) {
                if (preg_match('~(.*)\<xmp\>(.*?)\</xmp\>(.*)~', $comment, $matches)) {
                    $comment = $matches[1] . $matches[3];
                    $parameter->setExample($matches[2]);
                }
            }
            $parameter->setComment($comment);

            try {
                $resourceDocumentation->routeByVerb($verb)
                    ->addParameter($parameter);
            } catch (NoRouteForVerbException $e) {
                // Call does not exists for this route
            }
//            $route = $resourceDocumentation->routeByVerb($verb);
//            dd($route->getParameterQueryString());
        }
    }
}
