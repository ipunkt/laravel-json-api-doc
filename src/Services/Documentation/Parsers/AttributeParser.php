<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Attribute;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class AttributeParser extends BaseParser
{
    /**
     * parse
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

        $attributeLines = $this->docblockParser->findTags('attribute', $classDocblock);

        $attributes = $this->parseAttributes($attributeLines);

        $resourceDocumentation->setAttributes($attributes);
    }

    /**
     * parses attributes
     *
     * @param array $attributeLines
     * @return array
     */
    private function parseAttributes(array $attributeLines) : array
    {
        $attributes = array();

        foreach ($attributeLines as $attributeLine) {
            $attributes[] = $this->parseAttribute($attributeLine);
        }

        return $attributes;
    }

    /**
     * parses an attribute line
     *
     * @param string $attributeLine
     * @return Attribute
     * @throws \InvalidArgumentException
     */
    private function parseAttribute(string $attributeLine) : Attribute
    {
        $attribute = new Attribute();

        $words = explode(' ', $attributeLine);
        $numWords = count($words);
        if ($numWords < 1) {
            throw new \InvalidArgumentException("Kein Attributname angegeben für Attribut ($attributeLine).");
        }

        $name = $words[0];
        $type = '';
        $comment = '';

        if (1 < $numWords) {
            $type = array_shift($words);
            $name = array_shift($words);

            if (2 < $numWords) {
                $comment = implode(' ', $words);
            }
        }

        $attribute->setName($name);
        $attribute->setType($type);

        if (preg_match('~(.*)\<xmp\>(.*?)\</xmp\>(.*)~', $comment, $matches)) {
            $comment = $matches[1] . $matches[3];
            $attribute->setExample($matches[2]);
        }

        $attribute->setComment($comment);

        return $attribute;
    }
}
