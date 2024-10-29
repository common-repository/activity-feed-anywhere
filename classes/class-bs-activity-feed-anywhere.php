<?php

// If this file is called directly, abort.
defined( 'WPINC' ) || die( 'Process terminated.' );

/**
 * Class BS_Activity_Feed_Anywhere
 */
class BS_Activity_Feed_Anywhere {

	/** Properties ************************************************************/

	/**
	 * If the feed is enabled.
	 *
	 * @var bool
	 */
	public $feed_enabled = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * We register all our common hooks here.
	 *
	 * @return void
	 */
	public function __construct() {
		// See if our shortcode exists on the current queried object
		add_action( 'parse_query', array( $this, 'check_for_shortcode' ) );

		// Add the main shortcode
		add_shortcode( 'activity_feed_anywhere', array( $this, 'add_shortcode' ) );

		// Hook into the single activity template
		add_action( 'bp_before_activity_entry', array( $this, 'hook_into_activity' ) );

		// Declare pages containing our shortcode to be an activity component
		// add_filter( 'bp_is_current_component', array( $this, 'enable_component' ), 10, 2 );

		// Enable the Heartbeat to refresh activities
		add_filter( 'bp_activity_do_heartbeat', array( $this, 'enable_heartbeat' ) );

		// Template stack modification (later version)
		// add_action( 'bp_init', array( $this, 'modify_template_stack' ) );

		// Template inclusion (later version)
		// add_filter( 'bp_get_template_part', array( $this, 'entry_template' ), 999, 3 );

		// Add required body classes
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
	}

	/**
	 * Tell each activity they are part of the activity component.
	 *
	 * @return void
	 */
	public function hook_into_activity() {
		buddypress()->current_component = 'activity';
	}

	/**
	 * Modify the template stack. Currently unused.
	 *
	 * @return void
	 */
	public function modify_template_stack() {
		if ( function_exists( 'bp_register_template_stack' ) ) {
			bp_register_template_stack( 'bp_tol_register_template_location', 5 );
		}
	}

	/**
	 * See if our shortcode exists on the current queried object.
	 *
	 * @return void
	 */
	public function check_for_shortcode() {
		if ( true === $this->feed_enabled ) {
			return;
		}

		$queried_object = get_queried_object();

		if ( ! $queried_object ) {
			return;
		}

		if ( 'page' !== $queried_object->post_type ) {
			return;
		}

		if ( has_shortcode( $queried_object->post_content, 'activity_feed_anywhere' ) ) {
			buddypress()->current_component = 'activity';
			$this->feed_enabled             = true;
		}
	}

	/**
	 * We need to tell BuddyPress that a page/post with our shortcode on, should have the activity component active.
	 *
	 * @param $is_current_component
	 * @param $component
	 *
	 * @return bool|mixed
	 */
	public function enable_component( $is_current_component, $component ) {
		if ( function_exists( 'bp_duplicate_notice' ) && $this->feed_enabled ) {
			return true;
		} else {
			return $is_current_component;
		}
	}

	/**
	 * Tell our shortcode-inclusive pages to spin up the Heartbeat.
	 *
	 * @param $retval
	 *
	 * @return bool|mixed
	 */
	public function enable_heartbeat( $retval ) {
		if ( $this->feed_enabled ) {
			return true;
		} else {
			return $retval;
		}
	}

	/**
	 * Add classes to the body of Space pages, to assist with styling.
	 *
	 * @param $classes
	 *
	 * @return mixed
	 */
	public function add_body_classes( $classes ) {
		if ( $this->feed_enabled ) {
			$classes[] = 'activity buddypress activity-feed-anywhere';
		}

		return $classes;
	}

	/**
	 * Our shortcode.
	 *
	 * @param $atts
	 */
	public function add_shortcode( $atts ) {
		$a = shortcode_atts(
			array(
				'feed'    => true,
				'postbox' => true,
			),
			$atts
		);

		if ( ! $this->feed_enabled ) {
			return;
		}

		// We need our feed to be part of the activity component
		buddypress()->current_component = 'activity'; ?>

		<div id="buddypress" class="buddypress-wrap">

			<?php bp_nouveau_before_activity_directory_content(); ?>

		<?php if ( true === $a['postbox'] ) : ?>

			<?php if ( is_user_logged_in() ) : ?>

				<?php bp_get_template_part( 'activity/post-form' ); ?>

			<?php endif; ?>

		<?php endif; ?>

			<?php if ( true === $a['feed'] ) : ?>

				<?php bp_nouveau_template_notices(); ?>

				<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

					<?php bp_get_template_part( 'common/nav/directory-nav' ); ?>

				<?php endif; ?>

				<div class="screen-content">

					<?php //bp_get_template_part( 'common/search-and-filters-bar' ); ?>

					<?php bp_nouveau_activity_hook( 'before_directory', 'list' ); ?>

					<div id="activity-stream" class="activity" data-bp-list="activity">

						<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-activity-loading' ); ?></div>

					</div><!-- .activity -->

					<?php bp_nouveau_after_activity_directory_content(); ?>

				</div><!-- // .screen-content -->

			<?php endif; ?>

			<?php bp_nouveau_after_directory_page(); ?>

		</div>

		<?php
	}

	/**
	 * Include a template for activity/entry. Currently unused.
	 *
	 * @param $templates
	 * @param $slug
	 * @param $name
	 *
	 * @return mixed|string[]
	 */
	public function entry_template( $templates, $slug, $name ) {
		if ( 'activity/entry' !== $slug ) {
			return $templates;
		}

		return array( 'activity/entry.php' );
	}
}
