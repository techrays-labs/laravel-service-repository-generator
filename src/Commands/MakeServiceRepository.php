<?php

namespace LaravelServiceRepositoryGenerator\Commands;

use Illuminate\Console\Command;
use LaravelServiceRepositoryGenerator\Helpers\FileGenerator;

class MakeServiceRepository extends Command
{
    protected $signature = 'make:service-repository {name} 
                            {--serviceNamespace= : Custom namespace for Service} 
                            {--repositoryNamespace= : Custom path for Repository} 
                            {--interface : Generate Repository Interface}';

    protected $description = 'Generate a Service & Repository with custom namespace and path';

    public function handle()
    {
        $name = $this->argument('name');
        $serviceNamespace = $this->option('serviceNamespace') ?: config('service-repository.service_namespace');
        $repositoryNamespace = $this->option('repositoryNamespace') ?: config('service-repository.repository_path');
        $generateInterface = $this->option('interface');

        $fileGenerator = new FileGenerator();
        $fileGenerator->generateFiles($name, $serviceNamespace, $repositoryNamespace, $generateInterface);

        $this->info("Service & Repository for $name generated successfully!");
    }
}
