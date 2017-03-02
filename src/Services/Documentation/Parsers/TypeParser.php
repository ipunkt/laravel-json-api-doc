<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApi\Resources\ResourceNotDefinedException;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class TypeParser extends BaseParser
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
        try {
            $docBlock = $this->getSerializerDocBlock($resourceManager, $resourceName);
        } catch (ResourceNotDefinedException $exception) {
            return;
        }

        $types = $this->docblockParser->findTags('type', $docBlock);
        $type = implode('', $types);

        $resourceDocumentation->setType($type);
        if (empty($type)) {
            $resourceDocumentation->setType($resourceName);
        }
    }
}
