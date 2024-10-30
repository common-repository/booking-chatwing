<?php
/**
 * @package BookingChatwing\IntegrationPlugins\Wordpress
 */

/*
Plugin Name: ScheduleWing  – Online Booking and Appointment Schedule
Description: Online Booking and Appointment Schedule plugin from Chatwing will enable online booking services for your site.
Version: 0.0.2
Author: chatwing
Author URI: https://chatwing.com/
License: GPLv2 or later
Text Domain: booking_chatwing
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('BOOKING_CHATWING_VERSION', '2.4.2');
define('BOOKING_CHATWING_TEXTDOMAIN', 'booking_chatwing');
define('BOOKING_CHATWING_PATH', dirname(__FILE__));
define('BOOKING_CHATWING_CLASS_PATH', BOOKING_CHATWING_PATH . '/classes');
define('BOOKING_CHATWING_TPL_PATH', BOOKING_CHATWING_PATH . '/templates');
define('BOOKING_CHATWING_PLG_MAIN_FILE', __FILE__);
define('BOOKING_CHATWING_PLG_URL', plugin_dir_url(__FILE__));

define('BOOKING_CHATWING_DEBUG', false);
define('BOOKING_CHATWING_USE_STAGING', false);

define('BOOKING_CHATWING_CLIENT_ID', 'wordpress');

require_once BOOKING_CHATWING_PATH . '/chatwing-sdk/src/BookingChatwing/autoloader.php';
require_once BOOKING_CHATWING_PATH . '/chatwing-sdk/src/BookingChatwing/start.php';
$keyPath = BOOKING_CHATWING_PATH . '/key.php';
if (file_exists($keyPath)) {
    require $keyPath;
}

/**
 * Plugin class autoloader
 * @param  $className
 * @return bool
 * @throws Exception
 */
function bookingChatwingAutoloader($className)
{
    $prefix = 'BookingChatwing\\IntegrationPlugins\\WordPress\\';

    if ($pos = strpos($className, $prefix) !== 0) {
        return false;
    }

    $filePath = BOOKING_CHATWING_CLASS_PATH . '/' . str_replace('\\', '/', substr($className, strlen($prefix))) . '.php';
    if (file_exists($filePath)) {
        require_once($filePath);

        if (!class_exists($className)) {
            throw new Exception(__("Class {$className} doesn't exist ", BOOKING_CHATWING_TEXTDOMAIN));
        }

        return true;
    } else {
        throw new Exception(__("Cannot find file at {$filePath} ", BOOKING_CHATWING_PATHCHATWING_TEXTDOMAIN));
    }
}

function booking_chatwing_text_domain() {
    load_plugin_textdomain( 'booking_chatwing', WP_PLUGIN_DIR . '/booking-chatwing/'. 'languages', basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'booking_chatwing_text_domain' );
add_action( 'admin_print_styles', 'register_plugin_styles_booking' );
/**
 * Register style sheet.
 */
function register_plugin_styles_booking() {
    wp_register_style( 'formStyleBooking', plugins_url( 'assets/forms-min.css', __FILE__ ));
    wp_register_style( 'buttonStyleBooking', plugins_url( 'assets/buttons-min.css', __FILE__ ));
    wp_enqueue_style( 'formStyleBooking' );
    wp_enqueue_style( 'buttonStyleBooking' );
}

spl_autoload_register('bookingChatwingAutoloader');

use BookingChatwing\Application as BookingChatwing;
use BookingChatwing\IntegrationPlugins\WordPress\Application;
use BookingChatwing\IntegrationPlugins\WordPress\DataModel;

BookingChatwing::getInstance()->bind('client_id', BOOKING_CHATWING_CLIENT_ID);
$app = new Application(DataModel::getInstance());
$app->run();