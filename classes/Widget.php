<?php namespace BookingChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Widget
 * @package BookingChatwing\IntegrationPlugins\WordPress
 * @author chatwing
 */

class Widget extends \WP_Widget {
  function __construct() {
    parent::__construct('booking_chatwing_cb', __('Booking Chatwing', BOOKING_CHATWING_TEXTDOMAIN));
  }

  public function widget($args, $instance) {
    $defaultAttributes = array(
      'title' => ''
    );

    $instance = array_merge($defaultAttributes, $instance);
    echo $args['before_widget'];
    echo $args['before_title'] . $instance['title'] . $args['after_title'];
    echo ShortCode::render(array(
      'id' => $instance['booking'],
      'alias' => DataModel::findAliasByID($instance['booking']),
      'width' => !empty($instance['width']) ? $instance['width'] : '',
      'height' => !empty($instance['height']) ? $instance['height'] : ''
    ));
    echo $args['after_widget'];
  }

  public function form($instance) {
    $bookings = DataModel::getInstance()->getBookingList();
    $currentID = !empty($instance['booking']) ? $instance['booking'] : null;
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title", BOOKING_CHATWING_TEXTDOMAIN); ?></label>
      <input type="text" class="widefat"
        id="<?php echo $this->get_field_id('title'); ?>"
        name="<?php echo $this->get_field_name('title'); ?>"
        value="<?php echo !empty($instance['title']) ? $instance['title'] : '' ?>"/>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('booking'); ?>"><?php _e('booking', BOOKING_CHATWING_TEXTDOMAIN); ?></label>
      <select name="<?php echo $this->get_field_name('booking'); ?>"
        id="<?php echo $this->get_field_id('booking'); ?>">
        <?php if (!empty($bookings)): foreach ($bookings as $booking): ?>
          <option
            value="<?php echo $booking['id'] ?>" <?php if ($booking['id'] == $currentID) echo 'selected="selected"'; ?>><?php echo $booking['alias']; ?></option>
        <?php endforeach;endif; ?>
      </select>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width', BOOKING_CHATWING_TEXTDOMAIN); ?></label>
      <input type="text"
        name="<?php echo $this->get_field_name('width'); ?>"
        id="<?php echo $this->get_field_id('width') ?>"
        class="widefat"
        value="<?php echo !empty($instance['width']) ? $instance['width'] : 300 ?>"/>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height', BOOKING_CHATWING_TEXTDOMAIN); ?></label>
      <input type="text"
        name="<?php echo $this->get_field_name('height'); ?>"
        id="<?php echo $this->get_field_id('height'); ?>"
        class="widefat"
        value="<?php echo !empty($instance['height']) ? $instance['height'] : 400 ?>"/>
    </p>
  <?php
  }

  public function update($new, $old) {
    return $new;
  }
}