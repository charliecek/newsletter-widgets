<?php
/**
 * Plugin Name: Newsletter Widgets
 * Plugin URI: https://github.com/charliecek/newsletter-widgets
 * Description: Adds Additional Newsletter Widgets
 * Author: charliecek
 * Author URI: http://charliecek.eu/
 * Version: 1.1.0
 */

require_once __DIR__ . '/minimal-gdpr.php';

function newsletter_widgets_fix_subscription_localization() {
  $options = get_option('newsletter_profile');
  $data = array();
  $data['messages'] = array();
  if (isset($options['email_error'])) {
      $data['messages']['email_error'] = $options['email_error'];
  }
  if (isset($options['name_error'])) {
      $data['messages']['name_error'] = $options['name_error'];
  }
  if (isset($options['surname_error'])) {
      $data['messages']['surname_error'] = $options['surname_error'];
  }
  if (isset($options['profile_error'])) {
      $data['messages']['profile_error'] = $options['profile_error'];
  }
  if (isset($options['privacy_error'])) {
      $data['messages']['privacy_error'] = $options['privacy_error'];
  }
  $data['profile_max'] = NEWSLETTER_PROFILE_MAX;
  wp_localize_script('newsletter-subscription', 'newsletter', $data);
}
add_action('wp_enqueue_scripts', 'newsletter_widgets_fix_subscription_localization', 9999 );
