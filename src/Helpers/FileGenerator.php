<?php

namespace LaravelServiceRepositoryGenerator\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FileGenerator
{
    public function generateFiles($name, $serviceNamespace, $repositoryNamespace, $generateInterface)
    {
        // Define the paths
        $serviceStub = file_get_contents(__DIR__ . '/../Stubs/service.stub');
        $repositoryStub = file_get_contents(__DIR__ . '/../Stubs/repository.stub');

        // Replace placeholders
        $serviceContent = str_replace(['{{ namespace }}', '{{ className }}', '{{Interface}}'], [$serviceNamespace, $name, $generateInterface ? 'Interface' : ''], $serviceStub);
        $repositoryContent = str_replace(['{{ namespace }}', '{{ className }}'], [$repositoryNamespace, $name], $repositoryStub);

        // Define file paths
        $serviceFilePath = base_path(str_replace('\\', '/', $serviceNamespace) . "/{$name}Service.php");
        $repositoryFilePath = base_path(str_replace('\\', '/', $repositoryNamespace) . "/{$name}Repository.php");

        // Check if files already exist, and if so, fail the command
        if (File::exists($serviceFilePath)) {
            $this->handleFileExistenceError($serviceFilePath);
        }

        if (File::exists($repositoryFilePath)) {
            $this->handleFileExistenceError($repositoryFilePath);
        }

        // Generate interface if needed
        if ($generateInterface) {
            $interfaceStub = file_get_contents(__DIR__ . '/../Stubs/repository-interface.stub');
            $interfaceContent = str_replace(['{{ namespace }}', '{{ className }}'], [$repositoryNamespace, $name], $interfaceStub);
            $interfacePath = base_path(str_replace('\\', '/', $repositoryNamespace) . "/{$name}RepositoryInterface.php");

            // Check if the interface already exists, and if so, fail the command
            if (File::exists($interfacePath)) {
                $this->handleFileExistenceError($interfacePath);
            }

            // Ensure the directory for the interface exists
            $this->ensureDirectoryExists($interfacePath);

            // Generate the interface file
            file_put_contents($interfacePath, $interfaceContent);
        }

        // Ensure directories exist before creating files
        $this->ensureDirectoryExists($serviceFilePath);
        $this->ensureDirectoryExists($repositoryFilePath);

        // Generate the service file
        file_put_contents($serviceFilePath, $serviceContent);

        // Generate the repository file
        file_put_contents($repositoryFilePath, $repositoryContent);
    }

    /**
     * Handle file existence error by throwing an exception or logging it.
     *
     * @param string $filePath
     */
    private function handleFileExistenceError($filePath)
    {
        // Throw an exception or abort the process with an error message
        throw new \Exception("The file {$filePath} already exists. Aborting the generation process to prevent overwriting.");
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
