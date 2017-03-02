<?php

namespace Ipunkt\LaravelJsonApiDoc\Commands;

use Illuminate\Console\Command;
use Ipunkt\LaravelJsonApi\Resources\ResourceManager;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ApiBlueprint\BlueprintDocumentationFormatter;
use Ipunkt\LaravelJsonApiDoc\Services\Documentation\ApiBlueprint\BlueprintWriter;
use Ipunkt\LaravelJsonApiDoc\Services\DocumentationService;

class GenerateDocCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-doc:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate documentation page for the JSONApi';

    /**
     * resource manager
     *
     * @var ResourceManager
     */
    private $resourceManager;

    /**
     * documentation service
     *
     * @var DocumentationService
     */
    private $documentationService;

    /**
     * Create a new command instance.
     * @param ResourceManager $resourceManager
     * @param DocumentationService $documentationService
     */
    public function __construct(ResourceManager $resourceManager, DocumentationService $documentationService)
    {
        parent::__construct();
        $this->resourceManager = $resourceManager;
        $this->documentationService = $documentationService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $versions = $this->resourceManager->versions();

        if ($versions->isEmpty()) {
            $this->error('No version available');
        }

        $this->info('Available versions:' . PHP_EOL . '- ' . $versions->implode(PHP_EOL . '- '));

        foreach ($versions as $versionName) {

            $formatter = new BlueprintDocumentationFormatter();
            $formatter->setName(config('json-api-documentation.name'));
            $formatter->setDescription(config('json-api-documentation.description'));
            $formatter->setFormat(config('json-api-documentation.format'));

            $writer = new BlueprintWriter($versionName);
            $this->documentationService->buildFor($versionName, $this->resourceManager, $formatter, $writer);
        }

        return 0;
    }
}
