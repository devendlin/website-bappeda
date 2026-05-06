<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class GoogleDrive extends BaseConfig
{
    /**
     * OAuth 2.0 Client Credentials
     * Nilai ini dikosongkan karena akan otomatis mengambil dari file .env
     * (googledrive.clientId, googledrive.clientSecret, googledrive.refreshToken)
     */
    public $clientId = '';
    public $clientSecret = '';
    public $refreshToken = '';

    /**
     * Google Drive Folder ID
     * Nilai ini akan mengambil dari googledrive.folderId di .env
     */
    public $folderId = '';
}
