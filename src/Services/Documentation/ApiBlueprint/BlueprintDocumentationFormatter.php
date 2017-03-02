<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\ApiBlueprint;

use Illuminate\Support\Collection;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ResourceDocumentation;

class BlueprintDocumentationFormatter {

	/**
	 * Api name which will be written to the blueprint definition
	 * defaults to config('api-documentation.name')
	 *
	 * @var string
	 */
	private $name = 'Placeholder name';

	/**
	 * Api description which will be written to the blueprint definition
	 * defaults to config('api-documentation.description')
	 *
	 * @var string
	 */
	private $description = 'Placeholder description';

	/**
	 * @var string
	 */
	private $format = '1A';

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return BlueprintDocumentationFormatter
	 */
	public function setName(string $name): BlueprintDocumentationFormatter {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return BlueprintDocumentationFormatter
	 */
	public function setDescription(string $description): BlueprintDocumentationFormatter {
		$this->description = $description;
		return $this;
	}

	/**
	 * @param string $format
	 * @return BlueprintDocumentationFormatter
	 */
	public function setFormat(string $format): BlueprintDocumentationFormatter {
		$this->format = $format;
		return $this;
	}

	/**
	 * BlueprintDocumentationFormatter constructor.
	 */
	public function __construct() {
	}

	/**
	 * @param Collection|ResourceDocumentation[] $resourceObjects
	 * @return string
	 */
    public function format(Collection $resourceObjects) {
    	$name = $this->name;
	    $description = $this->description;
	    $format = $this->format;

	    $resources = [];
	    foreach($resourceObjects as $resource) {

			$resources[] = view('laravel-json-api-doc::resource', compact('resource'));
	    }

        return view('laravel-json-api-doc::blueprint', compact('resources', 'name', 'description', 'format'))->render();
    }
}
