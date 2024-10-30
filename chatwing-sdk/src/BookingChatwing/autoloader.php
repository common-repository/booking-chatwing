<?php

/**
 * @package BookingChatwing_Api
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('BOOKING_CHATWING_SDK_PATH', dirname(__FILE__));

/**
 * Autoloader function for PSR-0 coding style
 * @param  string $class 
 * @return boolean        
 */
function bookingChatwingSDKAutoload($class) {
  $originalClass = $class;
  if (strpos($class, '\\') === 0) {
    $class = substr($class, 1);
  }

  if (strpos($class, 'BookingChatwing') === 0) {
    $class = substr($class, 15);
    $path = BOOKING_CHATWING_SDK_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($path)) {
      include($path);

      if (!class_exists($originalClass)) {
        return false;
      } else {
        return true;
      }
    }
  }
}

spl_autoload_register('bookingChatwingSDKAutoload');
