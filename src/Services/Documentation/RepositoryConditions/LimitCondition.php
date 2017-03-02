<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\RepositoryConditions;

use Ipunkt\LaravelJsonApi\Contracts\Repositories\Conditions\RepositoryCondition;
use Ipunkt\LaravelJsonApi\Contracts\Repositories\Conditions\TakesConditions;

class LimitCondition implements RepositoryCondition
{
    /**
     * @var
     */
    private $limit;

    /**
     * LimitCondition constructor.
     * @param $limit
     */
    public function __construct($limit)
    {
        $this->limit = $limit;
    }

    /**
     * apply a builder
     *
     * @param TakesConditions $builder
     */
    public function apply(TakesConditions $builder)
    {
        $builder->limit($this->limit);
    }

    /**
     * sets parameter
     *
     * @param string $name
     * @param mixed $value
     * @return RepositoryCondition
     */
    function setParameter($name, $value)
    {
        $this->limit = $value;

        return $this;
    }
}