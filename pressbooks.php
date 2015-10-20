<?php
/*
Plugin Name: Pressbooks
Plugin URI: http://www.pressbooks.com
Description: Simple Book Production
Version: 3.0
Author: BookOven Inc.
Author URI: http://www.pressbooks.com
Text Domain: pressbooks
License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) )
	return;

// -------------------------------------------------------------------------------------------------------------------
// Turn on $_SESSION
// -------------------------------------------------------------------------------------------------------------------

function _pb_session_start() {
	if ( ! session_id() ) {
		if ( ! headers_sent() ) {
			ini_set( 'session.use_only_cookies', true );
			session_start();
		}
		else {
			error_log( 'There was a problem with _pb_session_start(), headers already sent!' );
		}
	}
}

function _pb_session_kill() {
	$_SESSION = array();
	session_destroy();
}

add_action( 'init', '_pb_session_start', 1 );
add_action( 'wp_logout', '_pb_session_kill' );
add_action( 'wp_login', '_pb_session_kill' );

// -------------------------------------------------------------------------------------------------------------------
// Setup some defaults
// -------------------------------------------------------------------------------------------------------------------

if ( ! defined( 'PB_PLUGIN_VERSION' ) )
	define ( 'PB_PLUGIN_VERSION', '3.0' );

if ( ! defined( 'PB_PLUGIN_DIR' ) )
	define ( 'PB_PLUGIN_DIR', __DIR__ . '/' ); // Must have trailing slash!

if ( ! defined( 'PB_PLUGIN_URL' ) )
	define ( 'PB_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // Must have trailing slash!

// -------------------------------------------------------------------------------------------------------------------
// Class autoloader
// -------------------------------------------------------------------------------------------------------------------

function _pressbooks_autoload( $class_name ) {

	$prefix = 'PressBooks\\';
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
		// Ignore classes not in our namespace
		return;
	}

	$parts = explode( '\\', strtolower( $class_name ) );
	array_shift( $parts );
	$class_file = 'class-pb-' . str_replace( '_', '-', array_pop( $parts ) ) . '.php';
	$path = count( $parts ) ? implode( '/', $parts ) . '/' : '';
	require( PB_PLUGIN_DIR . 'includes/' . $path . $class_file );
}

spl_autoload_register( '_pressbooks_autoload' );

// -------------------------------------------------------------------------------------------------------------------
// Minimum requirements
// -------------------------------------------------------------------------------------------------------------------

// Override PHP version at your own risk!
if ( ! isset( $pb_minimum_php ) ) $pb_minimum_php = '5.6.0';
function _pb_minimum_php() {
	global $pb_minimum_php;
	echo '<div id="message" class="error fade"><p>';
	printf( __( 'Pressbooks will not work with your version of PHP. Pressbooks requires PHP version %s or greater. Please upgrade PHP if you would like to use Pressbooks.', 'pressbooks' ), $pb_minimum_php );
	echo '</p></div>';
}
if ( ! version_compare( PHP_VERSION, $pb_minimum_php, '>=' ) ) {
	add_action( 'admin_notices', '_pb_minimum_php' );
	return;
}

$pb_minimum_wp = '4.3.1';
if ( ! is_multisite() || ! version_compare( get_bloginfo( 'version' ), $pb_minimum_wp, '>=' ) ) {

	add_action( 'admin_notices', function () use ( $pb_minimum_wp ) {
		echo '<div id="message" class="error fade"><p>';
		printf( __( 'Pressbooks will not work with your version of WordPress. Pressbooks requires a dedicated install of WordPress Multi-Site, version %s or greater. Please upgrade WordPress if you would like to use Pressbooks.', 'pressbooks' ), $pb_minimum_wp );
		echo '</p></div>';
	} );

	return;
}

// -------------------------------------------------------------------------------------------------------------------
// Configure root site
// -------------------------------------------------------------------------------------------------------------------

register_activation_hook( __FILE__, function () {
	$activate = new \PressBooks\Activation();
	$activate->registerActivationHook();
} );

// -------------------------------------------------------------------------------------------------------------------
// Initialize
// -------------------------------------------------------------------------------------------------------------------

$GLOBALS['pressbooks'] = new \PressBooks\PressBooks();

// -------------------------------------------------------------------------------------------------------------------
// Hooks
// -------------------------------------------------------------------------------------------------------------------

require( PB_PLUGIN_DIR . 'hooks.php' );

if ( is_admin() ) {
	require( PB_PLUGIN_DIR . 'hooks-admin.php' );
}

// --------------------------------------------------------------------------------------------------------------------
// Shortcuts to help template designers who don't use real namespaces...
// --------------------------------------------------------------------------------------------------------------------

require( PB_PLUGIN_DIR . 'functions.php' );

// -------------------------------------------------------------------------------------------------------------------
// Override wp_mail()
// -------------------------------------------------------------------------------------------------------------------

if ( ! function_exists( 'wp_mail' ) && isset( $GLOBALS['PB_SECRET_SAUCE']['POSTMARK_API_KEY'] ) && isset( $GLOBALS['PB_SECRET_SAUCE']['POSTMARK_SENDER_ADDRESS'] ) ) {
	function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
		return \PressBooks\Utility\wp_mail( $to, $subject, $message, $headers, $attachments );
	}
}

/* The distinction between "the internet" & "books" will disappear in 5 years. Start adjusting now. */
