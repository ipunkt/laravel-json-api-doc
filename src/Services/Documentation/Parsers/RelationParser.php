<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

use Exception;
use Ipunkt\LaravelJsonApi\Contracts\OneToManyRelationRepository;
use Ipunkt\LaravelJsonApi\Contracts\OneToOneRelationRepository;
use Ipunkt\LaravelJsonApi\Contracts\RequestHandlers\NeedsAuthenticatedUser;
use Ipunkt\LaravelJsonApi\Repositories\Conditions\LimitCondition;
use Ipunkt\LaravelJsonApi\Resources\ResourceDefinition;
use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApi\Resources\ResourceNotDefinedException;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Relation;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\Route;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;

class RelationParser extends BaseParser
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
        $resourceDefinition = $resourceManager->definition($resourceName);

        $relations = [];
        foreach ($resourceDefinition->relations as $relationDefinition) {
            $relation = new Relation();

            $name = $type = $relationDefinition->resource;
            $comment = $relationDefinition->description();

            $relation->setName($name);
            $relation->setType($type);
            $relation->setComment($comment);
            $this->parseRoutes($relationDefinition, $version, $resourceManager, $resourceName, $resourceDocumentation);

            $relations[] = $relation;
        }

        $resourceDocumentation->setRelations($relations);
    }

    /**
     * @param ResourceDefinition $relation
     * @param int $version
     * @param ResourceManager $resourceManager
     * @param string $resourceName
     * @param ResourceDocumentation $resourceDocumentation
     */
    private function parseRoutes(
        ResourceDefinition $relation,
        int $version,
        ResourceManager $resourceManager,
        string $resourceName,
        ResourceDocumentation $resourceDocumentation
    )
    {

        $relationName = $relation->resource;

        /**
         * @var OneToOneRelationRepository|OneToManyRelationRepository $relationshipRepository
         */
        try {

            $requestHandler = $resourceManager->resolveRequestHandler($resourceName, [], $version);
            $resourceRepository = $resourceManager->resolveRepository($resourceName, [], $version);

            $repositoryReflection = new \ReflectionClass($resourceRepository);
            $docblock = $repositoryReflection->getDocComment();
            $docblockParser = new DocblockParser();
            if ($docblockParser->hasTag('docHandler', $docblock)) {
                return;
            }

            $resourceRepository->applyCondition(new LimitCondition(1));
            $resource = $resourceRepository->get()->first();
            if ($resource === null) {
                return;
            }

            $relationshipRepository = $resourceManager->resolveRepository($resourceName . '.' . $relationName);
            $relationshipSerializer = $resourceManager->resolveSerializer($resourceName . '.' . $relationName);

            // TODO: get relation responses from repository and create serializer to get to correct result

            $isSecure = $requestHandler instanceof NeedsAuthenticatedUser;
            $routeName = ($isSecure)
                ? 'secure-api.resource.relationship'
                : 'api.resource.relationship';

            if ($relationshipRepository instanceof OneToOneRelationRepository) {
                $route = new Route('ITEM', route($routeName, [
                        'version' => $version,
                        'resource' => $resourceName,
                        'id' => urlencode('{id}'),
                        'relationship' => $relation->resource,
                    ], false) . '/{id}', 'Fetching a related resource of type ' . ucwords($relationName));
                $route->setSecure($isSecure);

                $route->addResponse(400, '');
                $route->addResponse(404, '');
                if ($isSecure) {
                    $route->addResponse(401, '');
                }

                $item = $relationshipRepository->getOne($resource);
                $response = new Document();
                $response->setData(new Resource($item, $relationshipSerializer));
                $encodedResponse = json_encode($response->toArray(), JSON_PRETTY_PRINT);
                $route->addResponse(200, $encodedResponse);

                $resourceDocumentation->addRoute($route);
            }

            if ($relationshipRepository instanceof OneToManyRelationRepository) {
                $route = new Route('INDEX', route($routeName, [
                    'version' => $version,
                    'resource' => $resourceName,
                    'id' => urlencode('{id}'),
                    'relationship' => $relation->resource,
                ], false), 'Fetching a list of related resources of type ' . ucwords($relationName));
                $route->setSecure($isSecure);
                $route->addResponse(200, '');
                $route->addResponse(400, '');
                $route->addResponse(404, '');
                if ($isSecure) {
                    $route->addResponse(401, '');
                }

                $items = $relationshipRepository->getMany($resource);
                $response = new Document();
                $response->setData(new Collection($items, $relationshipSerializer));
                $encodedResponse = json_encode($response->toArray(), JSON_PRETTY_PRINT);
                $route->addResponse(200, $encodedResponse);

                $resourceDocumentation->addRoute($route);
            }
        } catch (ResourceNotDefinedException $e) {
        } catch (Exception $e) {
            throw new \RuntimeException("Warning: Fatal Error in $resourceName: " . $e->getMessage(), 0, $e);
        }
    }
}
