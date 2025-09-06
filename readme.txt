=== GeoIP Location ===
Contributors: Thanh Tung
Tags: geoip, location, maxmind
Requires at least: 5.5
Tested up to: 6.6
Stable tag: 1.0.0
License: GPLv2 or later

== Description ==
Detect user location (city, region, country, timezone) from IP using MaxMind GeoLite2 City database.
The database is auto-updated monthly via WP-Cron.

== Installation ==
1. Upload `geoip-location` folder to `/wp-content/plugins/`
2. Activate the plugin.
3. Go to Settings > GeoIP Location to test.

== Usage ==
In PHP code:

```php
$geoip = new GeoIP_Location();
$location = $geoip->get_location(); // detect current visitor
$location = $geoip->get_location('8.8.8.8'); // test with custom IP
```
```php
if (class_exists('GeoIP_Location')) {
    $geoip = new GeoIP_Location();
    $location = $geoip->get_location(); // Detect current visitor
    // Example: echo $location['city'];
}
```
== Output ==
Array example:

```
[
  'ip_address'   => '8.8.8.8',
  'city'         => 'Mountain View',
  'region'       => 'California',
  'country'      => 'United States',
  'country_code' => 'US',
  'latitude'     => 37.4056,
  'longitude'    => -122.0775,
  'timezone'     => 'America/Los_Angeles',
]
```

== Notes ==
- Requires a free MaxMind account and License Key.
- Database updates automatically every month.
