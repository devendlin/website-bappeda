<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\GoogleDriveService;

class TestGDrive extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'gdrive:test';
    protected $description = 'Tests Google Drive connectivity and authentication.';

    public function run(array $params)
    {
        CLI::write('Testing Google Drive Connection...', 'yellow');

        try {
            $service = new GoogleDriveService();
            CLI::write('Service initialized successfully.', 'green');

            CLI::write('Attempting to list files in the configured folder...', 'yellow');
            
            // We'll use the underlying service to list files
            // This is a direct test of the authentication
            $reflect = new \ReflectionClass($service);
            $serviceProp = $reflect->getProperty('service');
            $serviceProp->setAccessible(true);
            $driveService = $serviceProp->getValue($service);
            
            $folderIdProp = $reflect->getProperty('folderId');
            $folderIdProp->setAccessible(true);
            $folderId = $folderIdProp->getValue($service);

            $optParams = [
                'pageSize' => 5,
                'fields'   => 'nextPageToken, files(id, name)',
                'q'        => "'$folderId' in parents and trashed = false"
            ];
            
            $results = $driveService->files->listFiles($optParams);

            if (count($results->getFiles()) == 0) {
                CLI::write("No files found, but authentication seems successful.", 'cyan');
            } else {
                CLI::write("Authentication successful! Found " . count($results->getFiles()) . " files:", 'green');
                foreach ($results->getFiles() as $file) {
                    CLI::write("- " . $file->getName() . " (" . $file->getId() . ")", 'white');
                }
            }

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            log_message('error', '[GDrive Test Command Error] ' . $e->getMessage());
        }
    }
}
