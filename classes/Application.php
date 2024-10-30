<?php namespace BookingChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @package BookingChatwing\IntegrationPlugins\Wordpress
 * @author chatwing
 */
use BookingChatwing\Encryption\DataEncryptionHelper;
use BookingChatwing\Application as BookingChatwing;

class Application extends PluginBase {
  protected function init() {
    if (!defined('BOOKING_CHATWING_ENCRYPTION_KEY')) {
      $this->onPluginActivation();
      $this->getModel()->saveAccessToken('');
      return;
    }

    DataEncryptionHelper::setEncryptionKey(BOOKING_CHATWING_ENCRYPTION_KEY);
    BookingChatwing::getInstance()->bind('access_token', $this->getModel()->getAccessToken());
    add_shortcode('booking_chatwing', array('BookingChatwing\\IntegrationPlugins\\WordPress\\ShortCode', 'render'));
  }

  protected function registerHooks() {
    register_activation_hook(BOOKING_CHATWING_PLG_MAIN_FILE, array($this, 'onPluginActivation'));
    if ($this->getModel()->hasAccessToken()) {
      add_action('widgets_init', function(){
        register_widget('BookingChatwing\\IntegrationPlugins\\WordPress\\Widget');
      });
    }
  }

  protected function registerFilters() {
    add_filter('login_redirect', array($this, 'handleUserLogin'), 10, 3);
  }

  public function onPluginActivation() {
    // check if we have encryption key
    $filePath = BOOKING_CHATWING_PATH . '/key.php';
    if (!file_exists($filePath)) {
      $encryptionKey = DataEncryptionHelper::generateKey();
      $n = file_put_contents($filePath, "<?php define('BOOKING_CHATWING_ENCRYPTION_KEY', '{$encryptionKey}');?>");
      if ($n) {
        require $filePath;
      } else {
        die("Cannot create encryption key.");
      }
    }
  }

  public function run() {
    parent::run();

    if (is_admin()) {
      $admin = new Admin($this->getModel());
      $admin->run();
    }
  }


  /**
   * @param $redirectUrl
   * @param string $requestedRedirectUrl
   * @param \WP_Error|\WP_User $user
   * @return string
   */
  public function handleUserLogin($redirectUrl, $requestedRedirectUrl = '', $user = null) {
    $targetURL = $redirectUrl;

    if ($user instanceof \WP_User && $user->ID) {
      // login successfully
      if (!empty($requestedRedirectUrl)) {
        $targetURL = $requestedRedirectUrl;
      }

      $targetURL = urldecode($targetURL);
      $parsedData = parse_url($targetURL);
      if (!empty($parsedData['host'])
          && in_array($parsedData['host'], array('chatwing.com', 'staging.chatwing.com'))
      ) {
        // try to get the booking alias
        // then determine if we have custom redirection URL
        $parts = isset($parsedData['path']) ? array_filter(explode('/', $parsedData['path'])) : array();
        if (count($parts) > 1) {
          $bookingKey = $parts[2];
          $bookingId = null;
          $bookingList = $this->getModel()->getbookingList();
          foreach ($bookingList as $booking) {
            if ($booking['key'] == $bookingKey) {
                $bookingId = $booking['id'];
                break;
            }
          }

          if ($bookingId) {
            $response = BookingChatwing::getInstance()->get('api')->call('chatbox/read', array('id' => $bookingId));
            if ($response->isSuccess()){
              $bookingData = $response->get('data');
              $secret = $bookingData['custom_login']['secret'];
              $customSession = Helper::prepareUserInformationForCustomLogin($user);

              $booking = BookingChatwing::getInstance()->get('booking');
              $booking->setId($bookingId);
              $booking->setParam('custom_session', $customSession);
              $booking->setSecret($secret);

              $targetURL = $booking->getBookingUrl();

              ?>
              <script>
                window.opener.location = '<?php echo $targetURL;?>';
                self.close();
              </script>
              <?php
              die;
            }
          }

        }
      }
    } else {
      switch (true) {
        case !empty($_GET['redirect_url']):
          $targetURL = $_GET['redirect_url'];
          break;
        case !empty($requestedRedirectUrl):
          $targetURL = $requestedRedirectUrl;
          break;

        default:
          break;
      }
    }
    return urldecode($targetURL);
  }

  protected function redirectUser($url, WP_User $user){
    
  }

}