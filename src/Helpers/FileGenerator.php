<?php

namespace LaravelServiceRepositoryGenerator\Helpers;

class FileGenerator
{
    public function generateFiles($name, $serviceNamespace, $repositoryPath, $generateInterface)
    {
        $serviceStub = file_get_contents(__DIR__ . '/../Stubs/service.stub');
        $repositoryStub = file_get_contents(__DIR__ . '/../Stubs/repository.stub');

        // Replace placeholders
        $serviceContent = str_replace(['{{ namespace }}', '{{ className }}'], [$serviceNamespace, $name . 'Service'], $serviceStub);
        $repositoryContent = str_replace(['{{ namespace }}', '{{ className }}'], [$repositoryPath, $name . 'Repository'], $repositoryStub);

        // Generate service file
        $servicePath = base_path(str_replace('\\', '/', $serviceNamespace) . "/{$name}Service.php");
        file_put_contents($servicePath, $serviceContent);

        // Generate repository file
        $repositoryPath = base_path(str_replace('\\', '/', $repositoryPath) . "/{$name}Repository.php");
        file_put_contents($repositoryPath, $repositoryContent);

        // Generate interface if needed
        if ($generateInterface) {
            $interfaceStub = file_get_contents(__DIR__ . '/../Stubs/repository-interface.stub');
            $interfaceContent = str_replace(['{{ namespace }}', '{{ className }}'], [$repositoryPath, $name . 'RepositoryInterface'], $interfaceStub);
            $interfacePath = base_path(str_replace('\\', '/', $repositoryPath) . "/{$name}RepositoryInterface.php");
            file_put_contents($interfacePath, $interfaceContent);
        }
    }
}
