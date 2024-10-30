<?php namespace BookingChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @package BookingChatwing\IntegrationPlugins\Wordpress
 * @author chatwing
 */

use InvalidArgumentException;
use BookingChatwing\Application as App;

class Admin extends PluginBase {
  protected function init() {
    parent::init();
  }

  protected function registerHooks() {
    add_action('admin_menu', array($this, 'registerAdminMenu'));
    add_action('admin_action_booking_chatwing_save_token', array($this, 'handleTokenSaving'));
    add_action('admin_action_booking_chatwing_save_settings', array($this, 'handleSettingsSave'));
  }
 
  protected function registerFilters() {

  }

  public function registerAdminMenu() {
    add_menu_page(__('Booking Chatwing plugin settings', BOOKING_CHATWING_TEXTDOMAIN), 'Booking Chatwing', 'manage_options', 'booking_chatwing', array($this, 'showSettingsPage'));
  }

  /**
   * Show booking chatwing settings page
   */
  public function showSettingsPage() {
    try {
      if ($this->getModel()->hasAccessToken()) {
        $bookings = $this->getModel()->getBookingList();
        $this->loadTemplate('settings', array('bookings' => $bookings));
      } else {
         $this->loadTemplate('settings', array('bookings' => array()));
      }
    } catch (\Exception $e) {

    }
  }

  /**
   * Hanle token update/remove
   */
  public function handleTokenSaving($skipNonce = false) {
    $token = sanitize_text_field($_POST['token']);
    $removeToken = sanitize_text_field($_POST['remove_token']);
    $nonceValue = sanitize_text_field($_POST['nonce']);
    $nonce = !empty($nonceValue) ? $nonceValue : '';
    if (!$skipNonce) {
      if (!wp_verify_nonce($nonce, 'token_save')) {
        die('Oops .... Authentication failed!');
      }
    }
    if (empty($token)) {
      if (isset($removeToken) && $removeToken == 1) {
        $this->getModel()->deleteAccessToken();
      } else {
        die('Unknown action!');
      }
    } else {
      $this->getModel()->saveAccessToken($token);
    }

    wp_redirect('admin.php?page=booking_chatwing');
    die;
  }

  public function handleSettingsSave() {
    $token = sanitize_text_field($_POST['token']);
    $removeToken = sanitize_text_field($_POST['remove_token']);
    $app_id= sanitize_text_field($_POST["app_id"]);
    $nonceValue = sanitize_text_field($_POST['nonce']);
    $nonce = !empty($nonceValue) ? $nonceValue : '';
    if (!wp_verify_nonce($nonce, 'settings_save')) {
        die('Oops .... Authentication failed!');
    }

    $fieldsToUpdate = array('width', 'height');

    foreach($fieldsToUpdate as $field) {
      $value = (int)sanitize_text_field($_POST[$field]);
      if (!empty($value)) {
        update_option('booking_chatwing_default_' . $field, $value);
      }
    }

    if (!empty($app_id)) {
         update_option('booking_chatwing_default_app_id', $app_id);
    }
    if (!empty($token) || !empty($removeToken)) {
      $this->handleTokenSaving(true);
    } else {
      wp_redirect('admin.php?page=booking_chatwing');
      die;
    }
  }

  /**
   * Load admin template
   * @param  string $templateName
   * @param  array $data
   * @throws InvalidArgumentException
   */
  public function loadTemplate($templateName, $data = array()) {
    if (strpos($templateName, '.php') === false) {
      $templateName .= '.php';
    }

    $file =  BOOKING_CHATWING_TPL_PATH . '/' . $templateName;
    if (file_exists($file)) {
      ob_start();
      if (!empty($data)) {
        extract($data);
      }
      require $file;
      $content = ob_get_clean();

      echo $content;
    } else {
      throw new InvalidArgumentException("Tempalte {$templateName} doesn't exist");
    }
  }

}
