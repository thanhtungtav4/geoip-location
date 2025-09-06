<?php
// Get credentials from options
$accountId  = get_option('geoip_location_account_id', '');
$licenseKey = get_option('geoip_location_license_key', '');
$url        = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key={$licenseKey}&suffix=tar.gz";

$dbPath     = __DIR__ . '/GeoLite2-City.mmdb';
$tmpFile    = __DIR__ . '/GeoLite2-City.tar.gz';
$tarFile    = __DIR__ . '/GeoLite2-City.tar';

function cleanup_files($files) {
    foreach ($files as $file) {
        if (file_exists($file)) @unlink($file);
    }
}

function download_file($url, $dest) {
    $fp = fopen($dest, 'w');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $success = curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $success;
}

try {
    // Download
    if (!download_file($url, $tmpFile)) {
        throw new Exception('Failed to download GeoLite2 database.');
    }

    // Decompress .tar.gz to .tar
    $phar = new PharData($tmpFile);
    $phar->decompress();

    // Extract .tar
    $pharTar = new PharData($tarFile);
    $pharTar->extractTo(__DIR__, null, true);

    // Find .mmdb file
    $mmdbFound = false;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));
    foreach ($iterator as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'mmdb') {
            copy($file, $dbPath);
            $mmdbFound = true;
            break;
        }
    }

    // Cleanup
    cleanup_files([$tmpFile, $tarFile]);

    if ($mmdbFound) {
        echo "✅ GeoLite2 DB updated successfully: {$dbPath}\n";
    } else {
        throw new Exception('GeoLite2 .mmdb file not found after extraction.');
    }

} catch (Exception $e) {
    cleanup_files([$tmpFile, $tarFile]);
    echo "❌ Error updating GeoLite2 DB: " . $e->getMessage() . "\n";
}
