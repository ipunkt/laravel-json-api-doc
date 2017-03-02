<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Link;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class LinksParser extends BaseParser
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
        $classDocblock = $this->getSerializerDocBlock($resourceManager, $resourceName);

        $linkLines = $this->docblockParser->findTags('link', $classDocblock);

        $links = $this->parseLinks($linkLines);

        $resourceDocumentation->setLinks($links);
    }

    /**
     * parses links
     *
     * @param $linkLines
     * @return Link[]|array
     */
    private function parseLinks(array $linkLines): array
    {
        $links = array();

        foreach ($linkLines as $linkLine) {
            $links[] = $this->parseLink($linkLine);
        }

        return $links;
    }

    /**
     * parse a link line
     *
     * @param $linkLine
     * @return Link
     */
    private function parseLink(string $linkLine)
    {
        $link = new Link();

        $words = explode(' ', $linkLine);
        $name = array_shift($words);
        $comment = implode(' ', $words);

        $link->setName($name);
        $link->setComment($comment);

        return $link;
    }
}
