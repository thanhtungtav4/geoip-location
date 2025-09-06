# GeoIP Location WordPress Plugin

Detect user location by IP using MaxMind GeoLite2 City database. Automatically updates the database monthly. Easily configure MaxMind credentials from the WordPress admin settings.

## Features
- Detects user location (city, country, etc.) by IP address
- Uses MaxMind GeoLite2 City database
- Auto-downloads and updates database monthly via WP-Cron
- Admin settings page for MaxMind Account ID and License Key
- Test IP lookup from settings page
- Robust error handling

## Installation
1. Clone or copy the plugin to your `wp-content/plugins/geoip-location` directory.
2. Install the required PHP library using Composer:
   ```sh
   composer require geoip2/geoip2
   ```
   If Composer is not installed, see [Composer Installation](https://getcomposer.org/download/).
3. Activate the plugin in WordPress admin.
4. Go to **Settings > GeoIP Location** and enter your MaxMind Account ID and License Key.

## Usage
### In Your Theme or Plugin
```php
if (class_exists('GeoIP_Location')) {
    $geoip = new GeoIP_Location();
    $location = $geoip->get_location(); // Uses current user's IP
    // $location = $geoip->get_location('8.8.8.8'); // Or specify an IP
    if ($location) {
        echo 'City: ' . $location['city'] . '<br>';
        echo 'Country: ' . $location['country'] . '<br>';
    } else {
        echo 'Location not found.';
    }
}
```

## Settings
- **Account ID** and **License Key**: Get these from your MaxMind account ([Sign up](https://www.maxmind.com/en/geolite2/signup)).
- **Test IP**: Use the settings page to test location lookup for any IP address.

## Troubleshooting
- If you see errors about missing classes, make sure Composer dependencies are installed.
- If the database is not downloading, check your credentials and file permissions.

## License
This plugin uses the MaxMind GeoLite2 database. See [MaxMind GeoLite2 EULA](https://www.maxmind.com/en/geolite2/eula) for details.

---
Author: Thanh Tùng
Website: https://nttung.dev
