<?php

namespace LaravelServiceRepositoryGenerator\Helpers;

use Illuminate\Support\Facades\File;

class FileGenerator
{
    public function generateFiles($name, $serviceNamespace, $repositoryNamespace, $generateInterface)
    {
        // Define the paths
        $serviceStub = file_get_contents(__DIR__ . '/../Stubs/service.stub');
        $repositoryStub = file_get_contents(__DIR__ . '/../Stubs/repository.stub');

        // Replace placeholders
        $serviceContent = str_replace(['{{ namespace }}', '{{ className }}'], [$serviceNamespace, $name . 'Service'], $serviceStub);
        $repositoryContent = str_replace(['{{ namespace }}', '{{ className }}'], [$repositoryNamespace, $name . 'Repository'], $repositoryStub);

        // Define file paths
        $serviceFilePath = base_path(str_replace('\\', '/', $serviceNamespace) . "/{$name}Service.php");
        $repositoryFilePath = base_path(str_replace('\\', '/', $repositoryNamespace) . "/{$name}Repository.php");

        // Ensure directories exist before creating files
        $this->ensureDirectoryExists($serviceFilePath);
        $this->ensureDirectoryExists($repositoryFilePath);

        // Generate the service file
        file_put_contents($serviceFilePath, $serviceContent);

        // Generate the repository file
        file_put_contents($repositoryFilePath, $repositoryContent);

        // Generate interface if needed
        if ($generateInterface) {
            $interfaceStub = file_get_contents(__DIR__ . '/../Stubs/repository-interface.stub');
            $interfaceContent = str_replace(['{{ namespace }}', '{{ className }}'], [$repositoryNamespace, $name . 'RepositoryInterface'], $interfaceStub);
            $interfacePath = base_path(str_replace('\\', '/', $repositoryNamespace) . "/{$name}RepositoryInterface.php");

            // Ensure the directory for the interface exists
            $this->ensureDirectoryExists($interfacePath);

            // Generate the interface file
            file_put_contents($interfacePath, $interfaceContent);
        }
    }

    /**
     * Ensure the directory exists, if not, create it.
     *
     * @param string $filePath
     */
    private function ensureDirectoryExists($filePath)
    {
        // Get the directory path from the file path
        $directoryPath = dirname($filePath);

        // Check if the directory exists; if not, create it
        if (!File::exists($directoryPath)) {
            // Create the directory and any missing parent directories (0755 is a common permission setting)
            File::makeDirectory($directoryPath, 0755, true);
        }
    }
}
