<?php
/**
 * Plugin Name: GeoIP Location
 * Description: Detect user location by IP using MaxMind GeoLite2 City. Auto update DB monthly.
 * Version: 1.0.0
 * Author: Thanh Tùng
 * Author URI: https://nttung.dev
 * Text Domain: geoip-location
 */

if ( ! defined('ABSPATH')) exit;

require_once __DIR__ . '/class-geoip-location.php';
require_once __DIR__ . '/vendor/autoload.php';

// 🔹 WP-Cron auto update
register_activation_hook(__FILE__, function() {
    // Schedule monthly update
    if (! wp_next_scheduled('geoip_location_update_db')) {
        wp_schedule_event(time(), 'monthly', 'geoip_location_update_db');
    }
    // Download DB if not present
    $dbPath = __DIR__ . '/GeoLite2-City.mmdb';
    if (!file_exists($dbPath)) {
        include __DIR__ . '/update-geoip.php';
    }
});

register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('geoip_location_update_db');
});

add_action('geoip_location_update_db', function() {
    include __DIR__ . '/update-geoip.php';
});

// 🔹 Admin settings page
add_action('admin_menu', function() {
    add_options_page(
        'GeoIP Location',
        'GeoIP Location',
        'manage_options',
        'geoip-location',
        'geoip_location_settings_page'
    );
});

// Register settings
add_action('admin_init', function() {
    register_setting('geoip_location_settings', 'geoip_location_account_id');
    register_setting('geoip_location_settings', 'geoip_location_license_key');
});

function geoip_location_settings_page() {
    $geoip = new GeoIP_Location();
    $test_ip = isset($_POST['test_ip']) ? sanitize_text_field($_POST['test_ip']) : $_SERVER['REMOTE_ADDR'];
    $location = $geoip->get_location($test_ip);

    $account_id = get_option('geoip_location_account_id', '');
    $license_key = get_option('geoip_location_license_key', '');

    if (isset($_POST['geoip_location_save_settings'])) {
        update_option('geoip_location_account_id', sanitize_text_field($_POST['geoip_location_account_id']));
        update_option('geoip_location_license_key', sanitize_text_field($_POST['geoip_location_license_key']));
        $account_id = get_option('geoip_location_account_id', '');
        $license_key = get_option('geoip_location_license_key', '');
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>GeoIP Location</h1>
        <form method="post">
            <h2>MaxMind Credentials</h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="geoip_location_account_id">Account ID</label></th>
                    <td><input type="text" name="geoip_location_account_id" id="geoip_location_account_id" value="<?php echo esc_attr($account_id); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="geoip_location_license_key">License Key</label></th>
                    <td><input type="text" name="geoip_location_license_key" id="geoip_location_license_key" value="<?php echo esc_attr($license_key); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <p><input type="submit" name="geoip_location_save_settings" class="button button-primary" value="Save Settings" /></p>
        </form>

        <form method="post">
            <h2>Test IP</h2>
            <label for="test_ip">Test IP:</label>
            <input type="text" name="test_ip" value="<?php echo esc_attr($test_ip); ?>" />
            <button type="submit" class="button">Check</button>
        </form>

        <?php if ($location): ?>
            <h2>Result</h2>
            <pre><?php print_r($location); ?></pre>
        <?php endif; ?>
    </div>
    <?php
}
