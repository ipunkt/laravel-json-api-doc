<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation;

use Illuminate\Support\Collection;
use Ipunkt\LaravelJsonApiDoc\Exceptions\NoRouteForVerbException;

class ResourceDocumentation
{
    /**
     * routes
     *
     * @var \Illuminate\Support\Collection
     */
    private $routes;

    /**
     * resource group
     *
     * @var string
     */
    private $resourceGroup;

    /**
     * @var string
     */
    private $name;

    /**
     * Currently the same as name, might change in the future.
     *
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var Relation[]
     */
    private $relations = array();

    /**
     * @var Attribute[]
     */
    private $attributes = array();

    /**
     * @var Link[]
     */
    private $links = array();

    /**
     * @var array
     */
    private $parameter = array();

    /**
     * @var Meta[]
     */
    private $meta = array();

    /**
     * ResourceDocumentation constructor.
     */
    public function __construct()
    {
        $this->routes = collect();
    }

    /**
     * adds a route
     *
     * @param Route $route
     * @return ResourceDocumentation
     */
    public function addRoute(Route $route) : self
    {
        $this->routes->push($route);

        return $this;
    }

    /**
     * returns all routes
     *
     * @return \Illuminate\Support\Collection
     */
    public function routes()
    {
        return $this->routes;
    }

    /**
     * returns resource group
     *
     * @return string
     */
    public function resourceGroup() : string
    {
        return $this->resourceGroup;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return Relation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param Relation[] $relations
     */
    public function setRelations($relations)
    {
        $this->relations = $relations;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * returns attribute string
     *
     * @return string
     */
    public function getRequiredAttributesString() : string
    {
        $result = [];
        foreach ($this->getAttributes() as $attribute) {
            $result[] = '"' . $attribute->getName() . '"';
        }

        return implode(',', $result);
    }

    /**
     * @param Attribute[] $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Link[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param Link[] $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * @return array
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @param array $parameter
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * @param $parameter
     */
    public function addParameter($parameter)
    {
        $this->parameter[] = $parameter;
    }

    /**
     * @return Meta[]
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param Meta[] $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    /**
     * @param Meta $meta
     */
    public function addMeta(Meta $meta)
    {
        $this->meta[] = $meta;
    }

    /**
     * returns route by verb
     *
     * @param string $verb
     * @return Route
     */
    public function routeByVerb($verb) : Route
    {
    	$route = $this->routes->filter(function (Route $route) use ($verb) {
            return $route->verb() === strtoupper($verb);
        })->first();

	    if( $route === null )
	    	throw new NoRouteForVerbException($verb);

	    return $route;
    }

    /**
     * returns all item-based routes: GET(item), PUT, PATCH, DELETE
     *
     * @return Collection
     */
    public function itemRoutes() : Collection
    {
        return $this->routes->filter(function (Route $route) {
            return $route->verb() === 'ITEM'
            || $route->method() === 'PUT'
            || $route->method() === 'PATCH'
            || $route->method() === 'DELETE';
        });
    }

    /**
     * returns all collection-based routes: GET(index), POST
     *
     * @return Collection
     */
    public function collectionRoutes() : Collection
    {
        return $this->routes->filter(function (Route $route) {
            return $route->verb() === 'INDEX'
            || $route->method() === 'POST';
        });
    }

    /**
     * returns possible relations as comma-separated string
     *
     * @return string
     */
    public function possibleRelationsString() : string
    {
        $relations = [];
        foreach ($this->relations as $relation) {
            $relations[] = $relation->getName();
        }

        return implode(',', $relations);
    }
}
