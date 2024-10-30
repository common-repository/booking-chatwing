<?php

/**
 * @author  chatwing
 * @package BookingChatwing\SDK
 */

namespace BookingChatwing;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BookingChatwing\Exception\BookingChatwingException;

class Booking extends Object {
  /**
   * @var Api
   */
  protected $api;
  protected $id = null;
  protected $key = null;
  protected $alias = null;
  protected $params = array();
  protected $secret = null;

  protected $baseUrl = null;
  public function __construct(Api $api) {
    $this->api = $api;
  }

  public function getBaseUrl() {  
    $api = new Api('');
    $domain = $api->getDomain();
    if(strpos($domain[$api->getEnv()], "staging")) {
      return $domain[$api->getEnv()]. "schedule/". $this->getAlias();
    } else {
      return "https://chatwing.com/schedule/". $this->getAlias();
    }
  }

  /**
   * Return booking's url
   *
   * @throws BookingChatwingException If no alias or booking key is set
   * @return string
   */
  public function getBookingUrl() {
    if (!$this->getId()) {
      throw new BookingChatwingException(__("Booking ID is not set!", BOOKING_CHATWING_TEXTDOMAIN));
    }

    $bookingUrl = $this->getBaseUrl();

    if (!empty($this->params)) {
      if ($this->getSecret()) {
        $this->getEncryptedSession(); // call this method to create encrypted session
      }
      $bookingUrl .= '&' . http_build_query($this->params);
    }

    return $bookingUrl;
  }

  /**
   * Return booking iframe code
   * @throws BookingChatwingException If no alias or booking key is set
   * @return string
   */
  public function getIframe() {
    $url = $this->getBookingUrl();
    return '<iframe src="'. $url .'" height="'. $this->getData('height') .'" width="'. $this->getData('width') .'" frameborder="0"></iframe>';
  }

  /**
   * Set booking ID
   * @param string $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return null
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set booking key
   *
   * @param string $key
   *
   * @return $this
   */
  public function setKey($key) {
    $this->key = $key;
    return $this;
  }

  /**
   * get the current booking's key
   *
   * @return string
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * Set booking alias
   *
   * @param string $alias
   *
   * @return $this
   */
  public function setAlias($alias) {
    $this->alias = $alias;
    return $this;
  }

  /**
   * Get current booking's alias
   *
   * @return string
   */
  public function getAlias() {
    return $this->alias;
  }

  /**
   * Set booking's parameter
   *
   * @param string|array $key 
   * @param string $value
   *
   * @return $this
   */
  public function setParam($key, $value = '') {
    if (is_array($key)) {
      foreach ($key as $k => $v) {
          $this->setParam($k, $v);
      }
    } else {
      $this->params[$key] = $value;
    }
    return $this;
  }

  /**
   * Get parameter
   * @param  string $key     
   * @param  null|mixed $default 
   * @return mixed|null
   */
  public function getParam($key = '', $default = null) {
    if (empty($key)) {
      return $this->params;
    }
    return isset($this->params[$key]) ? $this->params[$key] : $default;
  }

  /**
   * Get all parameters
   *
   * @return array
   */
  public function getParams() {
    return $this->params;
  }

  /**
   * Set booking secret key
   * @param $s
   *
   * @return $this
   */
  public function setSecret($s) {
    $this->secret = $s;
    return $this;
  }

  /**
   * Get secret
   * @return string|null
   */
  public function getSecret() {
    return $this->secret;
  }

  /**
   * Get encrypted session
   * @return string
   */
  public function getEncryptedSession() {
    if (isset($this->params['custom_session'])) {
      $customSession = $this->params['custom_session'];
      if (is_string($customSession)) {
        return $customSession;
      }

      if (is_array($customSession) && !empty($customSession) && $this->getSecret()) {
        $session = new CustomSession();
        $session->setSecret($this->getSecret());
        $session->setData($customSession);
        $this->setParam('custom_session', $session->toEncryptedSession());

        return $this->getParam('custom_session');
      }

      unset($this->params['custom_session']);
    }

    return false;
  }
} 