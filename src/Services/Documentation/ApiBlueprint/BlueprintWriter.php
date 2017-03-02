<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\ApiBlueprint;

class BlueprintWriter
{
    /**
     * version
     * @var int
     */
    private $version;

    /**
     * BlueprintWriter constructor.
     * @param int $version
     */
    public function __construct(int $version)
    {
        $this->version = $version;
    }

    /**
     * write string to file
     *
     * @param string $text
     * @return int
     */
    public function write(string $text) : int
    {
        $versionName = $this->version;
        $path = storage_path("api/blueprints/v$versionName.apbi");

        return \File::put($path, $text);
    }
}
