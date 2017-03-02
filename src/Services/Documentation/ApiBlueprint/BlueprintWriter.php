<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\ApiBlueprint;

use File;

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
        $path = config('json-api-documentation.storage_path');

        if (!File::isDirectory($path)) {
	        File::makeDirectory($path, 0775, true);
        }

        return File::put($path . DIRECTORY_SEPARATOR . "v$versionName.apbi", $text);
    }
}
