<?php
/*
  Plugin Name: Asynchronous Admin Bar
  Description: Replace the Admin Bar with an Asynchronous version so that you can cache logged-in users
  Author: Ba
  Version: 0.0
*/
add_action('rest_api_init', function () {
  register_rest_route( 'async-admin-bar/v1', '/html/', array(
    'methods' => 'POST',
    'callback' => 'async_admin_bar_html',
  ) );
});
function async_admin_bar_html() {
  $user_id = apply_filters( 'determine_current_user', false );
  if(!$user_id) return null;
  wp_set_current_user($user_id);
  $user = wp_get_current_user();
  if(!$user) return null;
  $roles = null;
  if(defined('WP_ASYNC_ADMIN_BAR_USER_ROLES')) {
    $roles = WP_ASYNC_ADMIN_BAR_USER_ROLES;
  }
  if(is_array($roles)) {
    $has_role = false;
    foreach($roles as $role) {
      if(current_user_can($role)) {
        $has_role = true;
        break;
      }
    }
    if($has_role === false) return null;
  }
  $show_admin_bar = get_user_option('show_admin_bar_front', $user->id);
  if(!$show_admin_bar) return null;
  show_admin_bar(true);
  require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';
  require_once ABSPATH . WPINC . '/functions.wp-styles.php';
  $abc = apply_filters( 'wp_admin_bar_class', 'WP_Admin_Bar' );
  if ( class_exists( $abc ) ) {
    $ab = new $abc();
  } else {
    return null;
  }
  $ab->initialize();
  $ab->add_menus();
  do_action_ref_array( 'admin_bar_menu', array( &$ab ) );
  $result = $ab->render();
  wp_enqueue_admin_bar_bump_styles();
  wp_print_styles();
}
if(!is_admin()) {
  add_action('init', function(){ show_admin_bar( false ); });
  add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('async-admin-bar', plugins_url('assets/index.js', __FILE__ ), array(), false, array( 'strategy' => 'async' ) );
  });
}
?>
