<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
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
        $docBlock = $this->getSerializerDocBlock($resourceManager, $resourceName);
        $types = $this->docblockParser->findTags('type', $docBlock);
        $type = implode('', $types);

        $resourceDocumentation->setType($type);
        if (empty($type)) {
            $resourceDocumentation->setType($resourceName);
        }
    }
}
