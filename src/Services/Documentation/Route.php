<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation;

use Illuminate\Support\Collection;

/**
 * Class Route
 * @package Ipunkt\LaravelJsonApiDoc\Services\Documentation
 *
 * Dokumentiert eine einzelne Route die zu einer Resource gehört.
 * Wird hauptsächlich vom RouteParser und vom RelationParser gesetzt
 */
class Route
{
    /**
     * verb / method
     *
     * @var string
     */
    private $verb;

    /**
     * path
     *
     * @var string
     */
    private $path;
    /**
     * label
     *
     * @var string
     */
    private $label;

    /**
     * parameter collection
     *
     * @var \Illuminate\Support\Collection
     */
    private $parameter;

    /**
     * secure route?
     *
     * @var bool
     */
    private $secure = false;

	/**
	 * Responses die diese Route zurückgeben kann
	 * Format:
	 * [ 'responseCode' => 'responseText' ]
	 *
	 * Wenn der responseText leer gelassen wird dann wird stattdessen das Partial response-$responseCode eingespielt
	 *
	 * @var string[]
	 */
	private $responses = [];

    /**
     * Route constructor.
     * @param string $verb
     * @param string $path
     * @param string $label
     */
    public function __construct(string $verb, string $path, string $label)
    {
        $this->verb = $verb;
        $this->path = $path;
        $this->label = $label;

        $this->parameter = collect();
    }

    /**
     * sets route secure
     *
     * @param bool $flag
     * @return Route
     */
    public function setSecure($flag = true) : self
    {
        $this->secure = $flag === true;

        return $this;
    }

    /**
     * returns verb
     *
     * @return string
     */
    public function verb() : string
    {
        return $this->verb;
    }

    /**
     * returns method
     *
     * @return string
     */
    public function method() : string
    {
        if ($this->verb === 'INDEX') {
            return 'GET';
        }
        if ($this->verb === 'ITEM') {
            return 'GET';
        }

        return $this->verb;
    }

    /**
     * returns true when route is get
     *
     * @return bool
     */
    public function isGet() : bool
    {
        return $this->method() === 'GET';
    }

    /**
     * returns true when route is post
     *
     * @return bool
     */
    public function isPost() : bool
    {
        return $this->method() === 'POST';
    }

    /**
     * returns true when route is patch
     *
     * @return bool
     */
    public function isPatch() : bool
    {
        return $this->method() === 'PATCH';
    }

    /**
     * returns true when route is delete
     *
     * @return bool
     */
    public function isDelete() : bool
    {
        return $this->method() === 'DELETE';
    }

    /**
     * is route secure?
     *
     * @return bool
     */
    public function isSecure() : bool
    {
        return $this->secure;
    }

    /**
     * returns path
     *
     * @return string
     */
    public function path() : string
    {
        return $this->path;
    }

    /**
     * returns label
     *
     * @return string
     */
    public function label() : string
    {
        return $this->label;
    }

    /**
     * adds parameter to route
     *
     * @param Parameter $parameter
     * @return Route
     */
    public function addParameter(Parameter $parameter) : self
    {
        $this->parameter->push($parameter);

        return $this;
    }

    /**
     * returns parameter collection
     *
     * @return Collection
     */
    public function parameter() : Collection
    {
        return $this->parameter;
    }

    /**
     * returns parameter query string
     *
     * @param array|Relation[] $relations
     * @param array $except
     * @return string
     */
    public function getParameterQueryString(array $relations = [], array $except = []) : string
    {
        if ($this->isPost() || $this->isPatch() || $this->isDelete()) {
            return '';
        }

        $params = [];

        if (!$this->parameter->isEmpty()) {
            foreach ($this->parameter as $parameter) {
                $params[$parameter->getName()] = $parameter->getName();
            }
        }

        if (count($relations)) {
            $params['include'] = 'include';
        }

        $params = collect($params)->except($except)->all();

        return count($params) === 0
            ? ''
            : '?' . urlencode(implode(',', $params));
    }

	/**
	 * Hole die möglichen responses dieser route Format: ['responseCode' => 'responseText']
	 *
	 * @return \string[]
	 */
	public function getResponses(): array {
		return $this->responses;
	}

	/**
	 * Setze eine response. Wenn der responseText leer gelassen wird wird stattdessen das partial `response-$code` eingespielt
	 *
	 * @param $responseCode
	 * @param $responseText
	 */
	public function addResponse($responseCode, $responseText) {
		$this->responses[$responseCode] = $responseText;
		ksort($this->responses);
	}

}
