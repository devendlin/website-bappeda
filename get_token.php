<?php

require __DIR__ . '/vendor/autoload.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

// Load environment variables if possible
$envFile = __DIR__ . '/.env';
$envClientId = '';
$envClientSecret = '';

if (file_exists($envFile)) {
    $lines = file($envFile);
    foreach ($lines as $line) {
        if (strpos($line, 'googledrive.clientId') === 0) {
            $parts = explode('=', $line);
            $envClientId = trim(str_replace(['"', "'"], '', $parts[1]));
        }
        if (strpos($line, 'googledrive.clientSecret') === 0) {
            $parts = explode('=', $line);
            $envClientSecret = trim(str_replace(['"', "'"], '', $parts[1]));
        }
    }
}

// Ask for Client ID
if (empty($envClientId)) {
    echo "Enter your OAuth Client ID: ";
    $clientId = trim(fgets(STDIN));
} else {
    $clientId = $envClientId;
    echo "Using Client ID from .env: $clientId\n";
}

// Ask for Client Secret
if (empty($envClientSecret)) {
    echo "Enter your OAuth Client Secret: ";
    $clientSecret = trim(fgets(STDIN));
} else {
    $clientSecret = $envClientSecret;
    echo "Using Client Secret from .env\n";
}

$client = new Google\Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri('http://localhost'); // Modern redirect for CLI/Desktop apps
$client->setScopes(Google\Service\Drive::DRIVE_FILE);
$client->setAccessType('offline'); // Crucial for getting a refresh token
$client->setPrompt('consent');     // Force consent to ensure refresh token is returned

// Request authorization
$authUrl = $client->createAuthUrl();

echo "\n------------------------------------------------------------\n";
echo "Open the following link in your browser:\n";
echo $authUrl . "\n";
echo "------------------------------------------------------------\n";
echo "------------------------------------------------------------\n";
echo "1. Sign in with your Google account.\n";
echo "2. Allow the application access.\n";
echo "3. The browser will try to open localhost (and might fail to load the page).\n";
echo "4. COPY the 'code' parameter from the URL in your browser's address bar.\n";
echo "   (Example: http://localhost/?code=4/0Af...)\n";
echo "------------------------------------------------------------\n";
echo "Enter the verification code here: ";

$authCode = trim(fgets(STDIN));

if (empty($authCode)) {
    exit("Error: No code entered.\n");
}

// Exchange authorization code for an access token
try {
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Check for errors
    if (array_key_exists('error', $accessToken)) {
        throw new Exception(join(', ', $accessToken));
    }

    echo "\n------------------------------------------------------------\n";
    echo "SUCCESS! Here is your Refresh Token:\n";
    echo "------------------------------------------------------------\n";
    if (isset($accessToken['refresh_token'])) {
        echo $accessToken['refresh_token'] . "\n";
        echo "------------------------------------------------------------\n";
        echo "Copy this token and paste it into app/Config/GoogleDrive.php\n";
    } else {
        echo "WARNING: No refresh token returned. Did you already authorize this app?\n";
        echo "Visit https://myaccount.google.com/permissions to revoke access and try again.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
