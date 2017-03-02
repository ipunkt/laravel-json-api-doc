<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation;

use Carbon\Carbon;

class Attribute
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var string
     */
    private $comment = '';

    /**
     * example
     *
     * @var string
     */
    private $example = '';

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
     * sets example
     *
     * @param string $example
     * @return Attribute
     */
    public function setExample(string $example) : self
    {
        $this->example = $example;

        return $this;
    }

    /**
     * returns example
     *
     * @return string
     */
    public function example() : string
    {
        $prefix = $suffix = '"';

        switch ($this->type) {
            case 'string':
                $this->example = empty($this->example) ? 'Hello World' : $this->example;
                break;
            case 'date':
                $this->example = empty($this->example) ? Carbon::now()->toIso8601String() : $this->example;
                break;
            case 'int':
            case 'integer':
                $this->example = empty($this->example) ? '1' : $this->example;
                $prefix = $suffix = '';
                break;
            case 'bool':
            case 'boolean':
                $this->example = empty($this->example) ? 'true' : $this->example;
                $prefix = $suffix = '';
                break;
            default:
                $this->example = empty($this->example) ? 'example' : $this->example;
                break;
        }

        return $prefix . $this->example . $suffix;
    }
}
