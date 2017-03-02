<?php

namespace Ipunkt\LaravelJsonApiDoc\Exceptions;

class NoRouteForVerbException extends LaravelJsonApiDocException
{
    /**
     * @var string
     */
    private $verb;

    /**
     * NoRouteForVerbException constructor.
     * @param string $verb
     */
    public function __construct(string $verb)
    {
        $this->verb = $verb;
    }

    /**
     * @return string
     */
    public function getVerb(): string
    {
        return $this->verb;
    }
}