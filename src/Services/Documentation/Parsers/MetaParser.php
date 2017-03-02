<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Meta;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class MetaParser extends BaseParser
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
        $docblock = $this->getRequestHandlerDocBlock($resourceManager, $resourceName);

        $metas = $this->docblockParser->findTags('meta', $docblock);
        foreach ($metas as $metaLine) {
            $words = explode(' ', $metaLine);

            $type = array_shift($words);
            $name = array_shift($words);
            $comment = implode(' ', $words);

            $meta = new Meta();
            $meta->setName($name);
            $meta->setType($type);
            $meta->setComment($comment);
            $resourceDocumentation->addMeta($meta);
        }
    }
}
