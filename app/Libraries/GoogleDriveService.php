<?php

namespace App\Libraries;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Config\GoogleDrive;

class GoogleDriveService
{
    protected $client;
    protected $service;
    protected $folderId;

    public function __construct()
    {
        $config = new GoogleDrive();
        
        $this->client = new Client();
        
        // Use OAuth 2.0 if Client ID is provided
        if (!empty($config->clientId)) {
            $this->client->setClientId($config->clientId);
            $this->client->setClientSecret($config->clientSecret);
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');

            try {
                // Manually refresh the access token using the refresh token
                $accessToken = $this->client->fetchAccessTokenWithRefreshToken($config->refreshToken);
                
                if (isset($accessToken['error'])) {
                    log_message('error', '[Google Drive Auth Error] ' . json_encode($accessToken));
                    throw new \RuntimeException('Google Drive Authentication Failed: ' . ($accessToken['error_description'] ?? $accessToken['error']));
                }
                
                $this->client->setAccessToken($accessToken);
            } catch (\Exception $e) {
                log_message('error', '[Google Drive Client Error] ' . $e->getMessage());
                throw $e;
            }
        } 
        // Fallback to Service Account if credentials path exists (Legacy support)
        elseif (!empty($config->credentialsPath) && file_exists($config->credentialsPath)) {
            $this->client->setAuthConfig($config->credentialsPath);
        } else {
            throw new \RuntimeException('Google Drive configuration invalid: Missing Client ID or Credentials file.');
        }

        $this->client->addScope(Drive::DRIVE_FILE);
        
        $this->service = new Drive($this->client);
        $this->folderId = $config->folderId;
    }

    /**
     * Upload a file to Google Drive
     *
     * @param string $filePath Full path to the file on local server
     * @param string $fileName Name of the file to be shown on Drive
     * @param string $mimeType Mime type of the file
     * @return string The File ID from Google Drive
     */
    public function uploadToDrive($filePath, $fileName, $mimeType = 'application/pdf')
    {
        $fileMetadata = new DriveFile([
            'name' => $fileName,
            'parents' => [$this->folderId]
        ]);

        $content = file_get_contents($filePath);

        $file = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink',
            'supportsAllDrives' => true
        ]);

        return $file->id;
    }

    /**
     * Get the shareable link for a file (Optional helper)
     */
    public function getFileLink($fileId)
    {
        $file = $this->service->files->get($fileId, [
            'fields' => 'webViewLink',
            'supportsAllDrives' => true
        ]);
        return $file->webViewLink;
    }

    /**
     * Delete a file from Google Drive
     *
     * @param string $fileId The File ID from Google Drive
     * @return bool
     */
    public function deleteFile($fileId)
    {
        try {
            $this->service->files->delete($fileId, ['supportsAllDrives' => true]);
            return true;
        } catch (\Exception $e) {
            log_message('error', '[Google Drive Delete Error] ' . $e->getMessage());
            return false;
        }
    }
}
