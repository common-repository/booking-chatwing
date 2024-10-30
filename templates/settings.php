<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BookingChatwing\IntegrationPlugins\WordPress\Asset;
use BookingChatwing\IntegrationPlugins\WordPress\DataModel;
use BookingChatwing\IntegrationPlugins\WordPress\ShortCode;

$model = DataModel::getInstance();
$count = 0;
?>
<h2><a><?php _e("Booking", BOOKING_CHATWING_TEXTDOMAIN) ?></a></h2>
<div class="wrap">
  <table class="widefat">
    <thead>
    <tr>
      <th><b><?php _e('Alias', BOOKING_CHATWING_TEXTDOMAIN); ?></b></th>
      <th><b><?php _e('ID', BOOKING_CHATWING_TEXTDOMAIN); ?></b></th>
      <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($bookings)): ?>
      <?php foreach ($bookings as $booking): ?>
          <tr>
            <td><?php echo $booking['alias']; ?></td>
            <td><?php echo $booking['id']; ?></td>
            <td><input type="button" class="pure-button pure-button-primary" value="<?php _e('Get shortcode', BOOKING_CHATWING_TEXTDOMAIN) ?>" onclick="prompt('Shortcode for booking <?php echo $booking['alias'] ?>', '<?php echo esc_attr(ShortCode::generateShortCode(array('id' => $booking['id'], 'alias' => $booking['alias']))) ?>')" /></td>
          </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="4"><?php _e('Unavailable Booking', BOOKING_CHATWING_TEXTDOMAIN); ?></td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>

  <h2><a><?php _e("Settings", BOOKING_CHATWING_TEXTDOMAIN) ?></a></h2>

  <div id="poststuff">
    <form class="pure-form pure-form-aligned pure-g" method="post" action="<?php echo admin_url('admin.php') ?>">
      <fieldset>
        <div class="pure-control-group">
          <label for="token"><?php _e('Access token', BOOKING_CHATWING_TEXTDOMAIN) ?></label>
          <input id="token" type="text" name="token">
          <label for="token" id="current_token">
            <input type="checkbox" name="remove_token" id="remove_token" value="1">
          Delete current token ?
          </label>
        </div>
        <div class="pure-control-group">
          <label for="appID"><?php _e('App ID', LOBBY_CHATWING_TEXTDOMAIN) ?></label>
          <input id="app_id" type="text" name="app_id" value="<?php echo esc_html(get_option('booking_chatwing_default_app_id')); ?>">
        </div>
        <div class="pure-control-group">
          <label for="width"><?php _e('Booking width', BOOKING_CHATWING_TEXTDOMAIN) ?></label>
          <input type="text" name="width" id="width" value="<?php echo esc_html(get_option('booking_chatwing_default_width')) ? esc_html(get_option('booking_chatwing_default_width')) : 600 ?>">
          <?php _e('pixel') ?>
        </div>
        <div class="pure-control-group">
          <label for="height"><?php _e('Booking height', BOOKING_CHATWING_TEXTDOMAIN) ?></label>
          <input type="text" name="height" id="height" value="<?php echo esc_html(get_option('booking_chatwing_default_height'))? esc_html(get_option('booking_chatwing_default_height')) : 800; ?>">
          <?php _e('pixel') ?>
        </div>
        <div class="pure-controls">
          <input type="submit" onclick="myFunction()" class="pure-button pure-button-primary" value="<?php _e('Save', BOOKING_CHATWING_TEXTDOMAIN) ?>">
        </div>
      </fieldset>
      <div style="display: none">
        <input type="hidden" name="action" value="booking_chatwing_save_settings"><?php wp_nonce_field('settings_save', 'nonce' ); ?>
      </div>
    </form>
  </div>
</div>
<script>
function myFunction() {
  alert('Save successfully!!!');
}
</script>