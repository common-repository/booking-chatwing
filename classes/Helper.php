<?php
namespace BookingChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Helper {
  public static function prepareUserInformationForCustomLogin(\WP_User $user) {
    $customSession = array();
    if ($user->ID) {
      $avatar = simplexml_load_string(get_avatar($user->ID));
      if ($avatar) {
        $attributes = $avatar->attributes();
        $avatar = (string) $attributes['src'];
      } else {
        $avatar = 'https://chatwing.com/images/no-avatar.gif';
      }

      $customSession['id'] = $user->ID;
      $customSession['name'] = $user->user_nicename;
      $customSession['avatar'] = $avatar;
      $customSession['expiration'] = round(microtime(true) * 1000) + 60 * 60 * 100;
    }

    return $customSession;
  }
}