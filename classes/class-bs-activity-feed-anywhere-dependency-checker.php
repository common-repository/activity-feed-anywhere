<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles checking for Activity Feed Anywhere's dependencies.
 */
class BS_Activity_Feed_Anywhere_Dependency_Checker {
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Check if Activity Feed Anywhere's dependencies have been met.
	 *
	 * @return bool True if we should continue to load the plugin.
	 */
	public static function check_dependencies() {
		if ( ! self::check_php() ) {
			add_action( 'admin_notices', array( 'BS_Activity_Feed_Anywhere_Dependency_Checker', 'add_php_notice' ) );
			add_action( 'admin_init', array( __CLASS__, 'deactivate_self' ) );

			return false;
		}

		if ( ! self::check_buddypress() ) {
			add_action( 'admin_notices', array( 'BS_Activity_Feed_Anywhere_Dependency_Checker', 'add_buddypress_notice' ) );
			add_action( 'admin_init', array( __CLASS__, 'deactivate_self' ) );

			return false;
		}

		if ( ! self::check_buddyboss() ) {
			add_action( 'admin_notices', array( 'BS_Activity_Feed_Anywhere_Dependency_Checker', 'add_buddyboss_notice' ) );
			add_action( 'admin_init', array( __CLASS__, 'deactivate_self' ) );

			return false;
		}

		return true;
	}

	/**
	 * Checks for our PHP version requirement.
	 *
	 * @return bool
	 */
	private static function check_php() {
		return version_compare( phpversion(), self::MINIMUM_PHP_VERSION, '>=' );
	}

	/**
	 * Adds notice in WP Admin that minimum version of PHP is not met.
	 *
	 * @access private
	 */
	public static function add_php_notice() {
		$screen        = get_current_screen();
		$valid_screens = self::get_critical_screen_ids();

		if ( null === $screen || ! current_user_can( 'activate_plugins' ) || ! in_array( $screen->id, $valid_screens, true ) ) {
			return;
		}

		// translators: %1$s is version of PHP that Activity Feed Anywhere requires; %2$s is the version of PHP WordPress is running on.
		$message = sprintf( __( '<strong>Activity Feed Anywhere</strong> requires a minimum PHP version of %1$s, but you are running %2$s.', 'activity-feed-anywhere' ), self::MINIMUM_PHP_VERSION, phpversion() );

		echo '<div class="error"><p>';
		echo wp_kses( $message, array( 'strong' => array() ) );
		$php_update_url = 'https://wordpress.org/support/update-php/';
		if ( function_exists( 'wp_get_update_php_url' ) ) {
			$php_update_url = wp_get_update_php_url();
		}
		printf(
			'<p><a class="button button-primary" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
			esc_url( $php_update_url ),
			esc_html__( 'Learn more about updating PHP', 'activity-feed-anywhere' ),
			/* translators: accessibility text */
			esc_html__( '(opens in a new tab)', 'activity-feed-anywhere' )
		);
		echo '</p></div>';
	}

	/**
	 * Checks for our BuddyBoss requirement.
	 *
	 * @return bool
	 */
	private static function check_buddyboss() {
		// We aren't currently compatible with BuddyBoss
		include_once ABSPATH. 'wp-admin/includes/plugin.php';

		if ( is_plugin_active ('buddyboss-platform/bp-loader.php') ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Adds notice in WP Admin that BuddyBoss is prohibited.
	 *
	 * @access private
	 */
	public static function add_buddyboss_notice() {
		$screen        = get_current_screen();
		$valid_screens = self::get_critical_screen_ids();

		if ( null === $screen || ! current_user_can( 'activate_plugins' ) || ! in_array( $screen->id, $valid_screens, true ) ) {
			return;
		}

		$message = __( '<strong>Activity Feed Anywhere</strong> is currently incompatible with BuddyBoss.', 'activity-feed-anywhere' );

		echo '<div class="error"><p>';
		echo wp_kses( $message, array( 'strong' => array() ) );
		$spaces_url = 'https://buddypress.org';
		printf(
			'<p><a class="button button-primary" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
			esc_url( $spaces_url ),
			esc_html__( 'Learn about BuddyPress', 'activity-feed-anywhere' ),
			/* translators: accessibility text */
			esc_html__( '(opens in a new tab)', 'activity-feed-anywhere' )
		);
		echo '</p></div>';
	}

	/**
	 * Checks for our BuddyPress requirement.
	 *
	 * @return bool
	 */
	private static function check_buddypress() {
		if ( class_exists( 'BuddyPress' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Adds notice in WP Admin that BuddyPress is required.
	 *
	 * @access private
	 */
	public static function add_buddypress_notice() {
		$screen        = get_current_screen();
		$valid_screens = self::get_critical_screen_ids();

		if ( null === $screen || ! current_user_can( 'activate_plugins' ) || ! in_array( $screen->id, $valid_screens, true ) ) {
			return;
		}

		$message = __( '<strong>Activity Feed Anywhere</strong> requires BuddyPress to be installed and activated.', 'activity-feed-anywhere' );

		echo '<div class="error"><p>';
		echo wp_kses( $message, array( 'strong' => array() ) );
		$spaces_url = 'https://buddypress.org';
		printf(
			'<p><a class="button button-primary" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
			esc_url( $spaces_url ),
			esc_html__( 'Learn about BuddyPress', 'activity-feed-anywhere' ),
			/* translators: accessibility text */
			esc_html__( '(opens in a new tab)', 'activity-feed-anywhere' )
		);
		echo '</p></div>';
	}

	/**
	 * Deactivate self.
	 */
	public static function deactivate_self() {
		deactivate_plugins( BS_AFA_PLUGIN_BASENAME );
	}

	/**
	 * Returns the screen IDs where dependency notices should be displayed.
	 *
	 * @return array
	 */
	private static function get_critical_screen_ids() {
		return array( 'dashboard', 'plugins', 'plugins-network' );
	}
}