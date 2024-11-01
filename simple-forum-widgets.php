<?php
/**
 * Plugin Name: Simple Forum Widgets
 * Plugin URI: http://tecdiary.com/products/simple-forum
 * Description: Adds two new widgets (Forum Threads and Forum Categories) to display your simple forum threads/categories on your site
 * Author: Tecdiary
 * Author URI: http://tecdiary.com
 * Version: 1.0.2
 * Text Domain: simple-forum-widgets
 * License: MIT
 *
 * @package Simple Forum Widgets
 * @version 1.0
 *
 * Plugin prefix: 'simple_forum_widgets_'
 */


if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('IMPLE_FORUM_WIDGETS_PLUGIN_URL', WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)));
define( 'SIMPLE_FORUM_WIDGETS_VERSION', '1.0.2' );

function simple_forum_widgets_add_site_styles() {
	$css_url = plugins_url( "assets/css/simple-forum-widgets.css", __FILE__ );
	wp_register_style( 'simple_forum_widgets', $css_url, '', SIMPLE_FORUM_WIDGETS_VERSION );
	wp_enqueue_style( 'simple_forum_widgets' );
	wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'simple_forum_widgets_add_site_styles' );

function simple_forum_widgets_load_plugin_textdomain() {
    load_plugin_textdomain( 'simple-forum-widgets', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'simple_forum_widgets_load_plugin_textdomain' );

function simple_forum_widgets_login_action() {
	global $wp;
	$current_url = $_GET['redirect_to'] ? $_GET['redirect_to'] : get_dashboard_url();
    $sf_base_url = get_option('sf_base_url');
	if ( ! empty($sf_base_url)) {
		header("Location: {$sf_base_url}wp_login?return_url={$current_url}");
		exit();
	}
	wp_redirect( $current_url );
	exit;
}
add_action('wp_login', 'simple_forum_widgets_login_action');

function simple_forum_widgets_logout_action() {
	global $wp;
	$current_url = $_GET['redirect_to'] ? $_GET['redirect_to'] : wp_login_url();
    $sf_base_url = get_option('sf_base_url');
	if ( ! empty($sf_base_url)) {
		header("Location: {$sf_base_url}logout?return_url={$current_url}");
		exit();
	}
	wp_redirect( $current_url );
	exit;
}
add_action('wp_logout', 'simple_forum_widgets_logout_action');

function simple_forum_widgets_updated_field_value( $widget_field, $new_field_value ) {
	extract( $widget_field );
	return strip_tags( $new_field_value );
}

function simple_forum_widgets_get_option($option_name, $default_value = FALSE) {
	$options = get_option('sf-options');
	return simple_forum_widgets_get_value($option_name, $options, $default_value);
}

function simple_forum_widgets_get_option_name($option_name) {
	return 'sf-options'.'['.$option_name.']';
}

function simple_forum_widgets_update_option($option_name, $option_value) {
	$options = get_option('sf-options');
	if (!is_array($options))
		$options = array();

	$options[$option_name] = $option_value;
	$return = update_option('sf-options', $options);
}

function simple_forum_widgets_get_value($Key, &$Collection, $Default = FALSE) {
	$Result = $Default;
	if(is_array($Collection) && array_key_exists($Key, $Collection)) {
		$Result = $Collection[$Key];
	} elseif(is_object($Collection) && property_exists($Collection, $Key)) {
		$Result = $Collection->$Key;
	}
	return $Result;
}

function simple_forum_widgets_get_user() {
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		$user = array(
			'username' => $current_user->user_login,
			'email' => $current_user->user_email,
			'first_name' => $current_user->user_firstname,
			'last_name' => $current_user->user_lastname,
		);

		$data = array('loggedIn' => TRUE, 'user' => $user);
	} else {
		$data = array('loggedIn' => FALSE);
	}

   return $data;
}

function simple_forum_widgets_logout_user($return_url) {
	header("Location: ".wp_logout_url( $return_url ? $return_url : home_url() ));
	exit();
}

include( plugin_dir_path( __FILE__ ) . 'simple-forum-widgets-fields.php' );
include( plugin_dir_path( __FILE__ ) . 'widgets/threads-widget.php' );
include( plugin_dir_path( __FILE__ ) . 'widgets/categories-widget.php' );
include( plugin_dir_path( __FILE__ ) . 'simple-forum-widgets-admin.php' );
