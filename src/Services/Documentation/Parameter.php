<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation;

class Parameter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var
     */
    private $comment;

    /**
     * @var string
     */
    private $example;

    /**
     * @var string
     */
    private $type = 'string';

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Parameter
     */
    public function setComment(string $comment) : self
    {
        $this->comment = trim($comment);

        return $this;
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
     * @return Parameter
     */
    public function setName(string $name) : self
    {
        $this->name = trim($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * @param string $example
     * @return Parameter
     */
    public function setExample(string $example) : self
    {
        $this->example = trim($example);

        return $this;
    }

    /**
     * returns type
     *
     * @return string
     */
    public function getType() : string
    {
        if (substr($this->type, -1, 1) === '?') {
            return substr($this->type, 0, -1) . ', optional';
        }

        return $this->type;
    }

    /**
     * @param string $type
     * @return Parameter
     */
    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }
}
