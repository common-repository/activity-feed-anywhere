<?php
/**
 * Plugin Name:  Activity Feed Anywhere
 * Plugin URI:   https://www.bouncingsprout.com
 * Description:  Create a custom BuddyPress activity post box and/or feed on any page.
 * Author:       Bouncingsprout Studio
 * Version:      1.0.0
 * Requires PHP: 7.0
 * License:      GNU General Public License v2 or later
 * Text Domain:  activity-feed-anywhere
 * Domain Path:  /languages/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// definitions to use throughout application
define( 'BS_AFA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BS_AFA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BS_AFA_TEMPLATE_DIR', dirname(__FILE__) . '/templates/' );
define( 'BS_AFA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'BS_AFA_TEXT_DOMAIN', 'activity-feed-anywhere' );

// Load text domain
load_plugin_textdomain( BS_AFA_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

require( BS_AFA_PLUGIN_PATH . 'includes/activity-feed-anywhere-functions.php' );

/**
 * Start the engines, captain...
 *
 * @return void
 */
function bs_afa_init() {
	// autoload Activity Feed Anywhere classes
	require BS_AFA_PLUGIN_PATH . '/classes/autoload.php';

	if ( ! BS_Activity_Feed_Anywhere_Dependency_Checker::check_dependencies() ) {
		return;
	} else {
		new BS_Activity_Feed_Anywhere();
	}
}
add_action( 'init', 'bs_afa_init' );


/**
 * Action hook to execute after Activity Feed Anywhere plugin init.
 *
 * Use this hook to init addons.
 */
do_action( 'activity_feed_anywhere_init' );


