<?php

namespace Ipunkt\LaravelJsonApiDoc\Services;

use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ApiBlueprint\BlueprintDocumentationFormatter;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ApiBlueprint\BlueprintWriter;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\ApiDocumentationParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\AttributeParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\CommentParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\DocblockParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\FilterParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\LinksParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\MetaParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\NameParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\ParameterParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\RelationParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\RoutesParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers\TypeParser;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class DocumentationService
{
    /**
     * docblock parser
     *
     * @var DocblockParser
     */
    private $docblockParser;

    /**
     * documentation parser
     *
     * @var ApiDocumentationParser
     */
    private $documentationParser;

    /**
     * DocumentationService constructor.
     * @param DocblockParser $docblockParser
     * @param ApiDocumentationParser $documentationParser
     */
    public function __construct(DocblockParser $docblockParser, ApiDocumentationParser $documentationParser)
    {
        $documentationParser->addParser(new RoutesParser($docblockParser));
        $documentationParser->addParser(new ParameterParser($docblockParser));
        $documentationParser->addParser(new AttributeParser($docblockParser));
        $documentationParser->addParser(new CommentParser($docblockParser));
        $documentationParser->addParser(new RelationParser($docblockParser));
        $documentationParser->addParser(new LinksParser($docblockParser));
        $documentationParser->addParser(new FilterParser($docblockParser));
        $documentationParser->addParser(new NameParser($docblockParser));
        $documentationParser->addParser(new TypeParser($docblockParser));
        $documentationParser->addParser(new MetaParser($docblockParser));

        $this->docblockParser = $docblockParser;
        $this->documentationParser = $documentationParser;
    }

    /**
     * build for version
     *
     * @param int $version
     * @param ResourceManager $resourceManager
     * @param BlueprintDocumentationFormatter $formatter
     * @param BlueprintWriter $writer
     */
    public function buildFor(
        int $version,
        ResourceManager $resourceManager,
        BlueprintDocumentationFormatter $formatter,
        BlueprintWriter $writer
    )
    {
        $resources = $this->documentationParser->parse($version, $resourceManager);

        $text = $formatter->format($resources->sortBy(function (ResourceDocumentation $resource) {
            return $resource->getName();
        }));
        $writer->write($text);
    }
}
