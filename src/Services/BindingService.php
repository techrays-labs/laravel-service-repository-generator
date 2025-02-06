<?php

namespace LaravelServiceRepositoryGenerator\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BindingService
{
    /**
     * Bind the interface to the repository in the AppServiceProvider.
     *
     * @param string $name
     * @param string $repositoryNamespace
     */
    public function bindInterfaceToRepository($name, $repositoryNamespace)
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
