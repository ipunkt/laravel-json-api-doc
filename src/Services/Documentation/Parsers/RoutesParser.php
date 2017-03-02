<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Ipunkt\LaravelJsonApi\Contracts\RelatedRepository;
use Ipunkt\LaravelJsonApi\Contracts\Repositories\JsonApiRepository;
use Ipunkt\LaravelJsonApi\Contracts\RequestHandlers\HandlesCollectionRequest;
use Ipunkt\LaravelJsonApi\Contracts\RequestHandlers\HandlesDeleteRequest;
use Ipunkt\LaravelJsonApi\Contracts\RequestHandlers\HandlesItemRequest;
use Ipunkt\LaravelJsonApi\Contracts\RequestHandlers\HandlesPatchRequest;
use Ipunkt\LaravelJsonApi\Contracts\RequestHandlers\HandlesPostRequest;
use Ipunkt\LaravelJsonApi\Contracts\RequestHandlers\NeedsAuthenticatedUser;
use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApi\Resources\ResourceNotDefinedException;
use Ipunkt\LaravelJsonApi\Serializers\Serializer;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\RepositoryConditions\LimitCondition;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Route;
use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\SerializerInterface;

class RoutesParser extends BaseParser
{
    /**
     * RoutesParser constructor.
     * @param DocblockParser $docblockParser
     */
    public function __construct(DocblockParser $docblockParser)
    {
        $this->docblockParser = $docblockParser;
        parent::__construct($docblockParser);
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
    function parse(
        int $version,
        ResourceManager $resourceManager,
        string $resourceName,
        ResourceDocumentation $resourceDocumentation
    )
    {

        $requestHandler = $resourceManager->resolveRequestHandler($resourceName, [], $version);
        $isSecure = $requestHandler instanceof NeedsAuthenticatedUser;
        $routeName = ($isSecure)
            ? 'secure-api.resource'
            : 'api.resource';

        try {
            $repository = $resourceManager->resolveRepository($resourceName, [], $version);
            $serializer = $resourceManager->resolveSerializer($resourceName, [], $version);

            $repositoryReflection = new \ReflectionClass($repository);
            $docblock = $repositoryReflection->getDocComment();
            $docblockParser = new DocblockParser();
            if ($docblockParser->hasTag('docHandler', $docblock)) {
                return;
            }

            if ($requestHandler instanceof HandlesItemRequest) {
                $route = $this->createItemRoute($version, $resourceName, $routeName, $isSecure, $repository,
                    $serializer);
                $resourceDocumentation->addRoute($route);
            }

            if ($requestHandler instanceof HandlesCollectionRequest) {
                $route = $this->createIndexRoute($version, $resourceName, $routeName, $isSecure, $repository,
                    $serializer);
                $resourceDocumentation->addRoute($route);
            }
        } catch (ResourceNotDefinedException $e) {
            // Post braucht kein repository
        }

        if ($requestHandler instanceof HandlesPostRequest) {
            $route = $this->createPostRoute($version, $resourceName, $routeName, $isSecure);
            $resourceDocumentation->addRoute($route);
        }

        if ($requestHandler instanceof HandlesPatchRequest) {
            $route = $this->createPatchRoute($version, $resourceName, $routeName, $isSecure);
            $resourceDocumentation->addRoute($route);
        }

        if ($requestHandler instanceof HandlesDeleteRequest) {
            $route = $this->createDeleteRoute($version, $resourceName, $routeName, $isSecure);
            $resourceDocumentation->addRoute($route);
        }
    }

    /**
     *
     *
     * @param int $version
     * @param string $resourceName
     * @param string $routeName
     * @param bool $isSecure
     * @param JsonApiRepository|RelatedRepository $repository
     * @param SerializerInterface|AbstractSerializer|Serializer $serializer
     * @return Route
     */
    private function createItemRoute(
        int $version,
        string $resourceName,
        string $routeName,
        bool $isSecure,
        $repository,
        $serializer
    ): Route
    {
        $route = new Route('ITEM', route($routeName, [
                'version' => $version,
                'resource' => $resourceName,
            ], false) . '/{id}', 'Fetching resource of type ' . ucwords($resourceName));

        $route->setSecure($isSecure);
        if ($isSecure) {
            $route->addResponse(401, '');
        }

        $repository->applyCondition(new LimitCondition(1));
        $item = $repository->get()->first();
        $response = new Document();
        $response->setData(new Resource($item, $serializer));
        $encodedResponse = json_encode($response->toArray(), JSON_PRETTY_PRINT);

        $route->addResponse(200, $encodedResponse);
        $route->addResponse(400, '');
        $route->addResponse(404, '');

        return $route;
    }

    /**
     *
     *
     * @param int $version
     * @param string $resourceName
     * @param $routeName
     * @param $isSecure
     * @param JsonApiRepository|RelatedRepository $repository
     * @param SerializerInterface|AbstractSerializer|Serializer $serializer
     * @return Route
     */
    private function createIndexRoute(
        int $version,
        string $resourceName,
        string $routeName,
        bool $isSecure,
        $repository,
        $serializer
    ): Route
    {
        $route = new Route('INDEX', route($routeName, [
            'version' => $version,
            'resource' => $resourceName,
        ], false), 'Fetching a list of resources of type ' . ucwords($resourceName));
        $route->setSecure($isSecure);
        if ($isSecure) {
            $route->addResponse(401, '');
        }

        $repository->applyCondition(new LimitCondition(5));
        $collection = $repository->get();
        $response = new Document();
        $response->setData(new Collection($collection, $serializer));
        $encodedResponse = json_encode($response->toArray(), JSON_PRETTY_PRINT);

        $route->addResponse(200, $encodedResponse);
        $route->addResponse(400, '');
        $route->addResponse(404, '');

        return $route;
    }

    /**
     * creates post route
     *
     * @param int $version
     * @param string $resourceName
     * @param string $routeName
     * @param bool $isSecure
     * @return Route
     */
    private function createPostRoute(
        int $version,
        string $resourceName,
        string $routeName,
        bool $isSecure
    ): Route
    {
        $route = new Route('POST', route($routeName, [
            'version' => $version,
            'resource' => $resourceName,
        ], false), 'Creating a resource of type ' . ucwords($resourceName));
        $route->setSecure($isSecure);
        if ($isSecure) {
            $route->addResponse(401, '');
        }

        $route->addResponse(201, '');
        $route->addResponse(400, '');
        $route->addResponse(404, '');

        return $route;
    }

    /**
     * creates patch route
     *
     * @param int $version
     * @param string $resourceName
     * @param string $routeName
     * @param bool $isSecure
     * @return Route
     */
    private function createPatchRoute(
        int $version,
        string $resourceName,
        string $routeName,
        bool $isSecure
    ): Route
    {
        $route = new Route('PATCH', route($routeName, [
                'version' => $version,
                'resource' => $resourceName,
            ], false) . '/{id}', 'Updating a resource of type ' . ucwords($resourceName));
        $route->setSecure($isSecure);
        if ($isSecure) {
            $route->addResponse(401, '');
        }

        $route->addResponse(200, '');
        $route->addResponse(400, '');
        $route->addResponse(404, '');

        return $route;
    }

    /**
     * creates delete route
     *
     * @param int $version
     * @param string $resourceName
     * @param string $routeName
     * @param bool $isSecure
     * @return Route
     */
    private function createDeleteRoute(
        int $version,
        string $resourceName,
        string $routeName,
        bool $isSecure
    ): Route
    {
        $route = new Route('DELETE', route($routeName, [
                'version' => $version,
                'resource' => $resourceName,
            ], false) . '/{id}', 'Deleting a resource of type ' . ucwords($resourceName));
        $route->setSecure($isSecure);
        if ($isSecure) {
            $route->addResponse(401, '');
        }

        $route->addResponse(204, '');
        $route->addResponse(400, '');
        $route->addResponse(404, '');

        return $route;
    }
}
