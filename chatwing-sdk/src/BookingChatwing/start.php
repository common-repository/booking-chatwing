<?php

/**
 * @author chatwing
 * @package BookingChatwing_SDK
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!defined('BOOKING_CHATWING_DEBUG')) {
    define('BOOKING_CHATWING_DEBUG', false);
}

define('BOOKING_CHATWING_SDK_VESION', '1.0');
define('BOOKING_CHATWING_ENV_DEVELOPMENT', 'development');
define('BOOKING_CHATWING_ENV_PRODUCTION', 'production');

use BookingChatwing\Application as App;

$app = App::getInstance();
$app->bind(
  'api',
  function (\BookingChatwing\Container $container) {
    $app = new BookingChatwing\Api($container->get('client_id'));
    
    $app->setEnv(
      defined('BOOKING_CHATWING_USE_STAGING') && BOOKING_CHATWING_USE_STAGING ? BOOKING_CHATWING_ENV_DEVELOPMENT : BOOKING_CHATWING_ENV_PRODUCTION
    );

    if ($container->has('access_token')) {
      $app->setAccessToken($container->get('access_token'));
    }

    return $app;
  }
);

$app->factory(
    'booking',
    function (\BookingChatwing\Container $container) {
      return new \BookingChatwing\Booking($container->get('api'));
    }
);