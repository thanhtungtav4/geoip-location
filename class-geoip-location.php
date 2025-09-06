<?php
use GeoIp2\Database\Reader;

class GeoIP_Location {
    private $reader;

    public function __construct() {
        $dbPath = __DIR__ . '/GeoLite2-City.mmdb';
        if (file_exists($dbPath)) {
            $this->reader = new Reader($dbPath);
        }
    }

    public function get_location($ip = null) {
        if (!$ip) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!$this->reader) {
            return null;
        }
        try {
            $record = $this->reader->city($ip);
            return [
                'ip_address'   => $ip,
                'city'         => $record->city->name,
                'region'       => $record->mostSpecificSubdivision->name,
                'country'      => $record->country->name,
                'country_code' => $record->country->isoCode,
                'latitude'     => $record->location->latitude,
                'longitude'    => $record->location->longitude,
                'timezone'     => $record->location->timeZone,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
