<?php

namespace LaravelServiceRepositoryGenerator\Helpers;

use LaravelServiceRepositoryGenerator\Services\BindingService;
use Illuminate\Support\Facades\File;

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

            // Bind the interface to the repository in AppServiceProvider
            $this->bindInterfaceToRepository($name, $repositoryNamespace);
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

    private function bindInterfaceToRepository($name, $repositoryNamespace)
    {
        $appServiceProviderPath = app_path('Providers/AppServiceProvider.php');

        if (!File::exists($appServiceProviderPath)) {
            return;
        }

        // Read the content of AppServiceProvider
        $appServiceProviderContent = file_get_contents($appServiceProviderPath);

        // Define the binding line
        $bindingLine = "        \$this->app->bind({$repositoryNamespace}\\{$name}RepositoryInterface::class, {$repositoryNamespace}\\{$name}Repository::class);";

        // Check if the binding already exists
        if (Str::contains($appServiceProviderContent, $bindingLine)) {
            return; // No need to bind if it's already present
        }

        // Find the position to insert the binding inside the register() method
        $registerMethodPosition = strpos($appServiceProviderContent, 'public function register()');

        if ($registerMethodPosition !== false) {
            $registerMethodPosition = strpos($appServiceProviderContent, '{', $registerMethodPosition) + 1;
            $beforeCode = substr($appServiceProviderContent, 0, $registerMethodPosition);
            $afterCode = substr($appServiceProviderContent, $registerMethodPosition);

            // Insert the binding line inside the register method
            $appServiceProviderContent = $beforeCode . PHP_EOL . $bindingLine . PHP_EOL . $afterCode;

            // Write the updated content back to AppServiceProvider.php
            file_put_contents($appServiceProviderPath, $appServiceProviderContent);
        }
    }
}
