<?php namespace BookingChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BookingChatwing\Object;
use BookingChatwing\Encryption\DataEncryptionHelper;
use BookingChatwing\Application as BookingChatwingContainer;

class DataModel extends Object {
  protected $token = null;

  protected static $isntance = null;

  function __construct() {

  }

  /**
   * @return DataModel|null
   */
  public static function getInstance() {
    if (is_null(self::$isntance)) {
      self::$isntance = new self;
    }

    return self::$isntance;
  }

  public function hasAccessToken() {
    return (bool) $this->getAccessToken();
  }

  public function getAccessToken() {
    if (is_null($this->token)) {
      try {
        $this->token = DataEncryptionHelper::decrypt(get_option('booking_chatwing_access_token'));
      } catch (\Exception $e) {
        die($e->getMessage());
      }
    }
    return $this->token;
  }

  /**
   * Save access token
   * @param  $token
   */
  public function saveAccessToken($token) {
    if ($token) {
      $token = DataEncryptionHelper::encrypt($token);
    }

    $this->token = $token;

    update_option('booking_chatwing_access_token', $token);
  }

  public function deleteAccessToken() {
    return delete_option('booking_chatwing_access_token');
  }

  public function saveOption($key, $value) {
    $value = sanitize_text_field($value);
    update_option('booking_chatwing_default_' . $key, $value);
  }

  public function getOption($key, $default = null) {
    return get_option( 'booking_chatwing_default_' . $key, $default );
  }

  public function getBookingList() {
    $bookings = array();
    $result = array();

    try {
      $api = BookingChatwingContainer::getInstance()->get('api');
      $response = $api->call('app/float_ui/list', array('app_id' => get_option('booking_chatwing_default_app_id')));
      if ($response->isSuccess()) {
        $bookings = $response->get('data');
        foreach ($bookings as $booking) {
         if ($booking['floating_type'] == 'schedule') {
          array_push($result, $booking);
         }
        };
          
      }
    } catch (\Exception $e) {
      die($e->getMessage());
    }

    return $result;
  }

  public function findAliasByID($ID) {
    $bookings = DataModel::getInstance()->getBookingList();
    if (!empty($bookings)){
      foreach ($bookings as $booking) {
        if ($booking['id'] === $ID) {
          return $booking['alias'];
        }
      }
    }
    return "";
  }
}