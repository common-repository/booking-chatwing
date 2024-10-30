<?php
/**
 * @author chatwing <dev@chatwing.com>
 */

namespace BookingChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BookingChatwing\Application as App;


class ShortCode {
  /**
   * @param array $params
   * @return string
   */
  public static function render($params = array()) {
    $model = DataModel::getInstance();
    
    $defaultAttributes = array(
      'width' => $model->getOption('width'),
      'height' => $model->getOption('height'),
    );

    $params = array_merge($defaultAttributes, $params);

    if (empty($params['id'])) {
      return '';
    }

    /**
     * @var \BookingChatwing\Booking $booking
     */
    $booking = App::getInstance()->get('booking');
    $booking->setId($params['id']);
    $booking->setAlias($params['alias']);

    $booking->setData('width', $params['width']);
    $booking->setData('height', $params['height']);

    return $booking->getIframe();
  }

  /**
   * Generate shortcode for a booking
   * @param  array $params
   * @return string
   */
  public static function generateShortCode($params = array()) {
    if (empty($params) || (empty($params['id']))) {
      return '';
    }

    $model = DataModel::getInstance();

    $defaultAttributes = array(
      'id' => '',
      'alias' => '',
      'width' => $model->getOption('width'),
      'height' => $model->getOption('height')
    );

    $params = shortcode_atts($defaultAttributes, $params);

    if (!empty($params['key'])) {
      unset($params['alias']);
    } else {
      unset($params['key']);
    }

    $shortCode = '';
    foreach ($params as $key => $value) {
      $shortCode .= "{$key}=\"{$value}\" ";
    }
    $shortCode = "[booking_chatwing {$shortCode} ][/booking_chatwing]";
    return $shortCode;
  }
}