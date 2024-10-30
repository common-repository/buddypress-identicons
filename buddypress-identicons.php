<?php
/**
 * Plugin Name: BuddyPress Identicons
 * Plugin URI: https://github.com/henrywright/buddypress-identicons
 * Description: Fun identicons for your BuddyPress site.
 * Version: 2.0.1
 * Author: Henry Wright
 * Author URI: http://about.me/henrywright
 * Text Domain: buddypress-identicons
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * BuddyPress Identicons
 *
 * @package BuddyPress Identicons
 */

/**
 * Require the plugin's files.
 *
 * @since 1.1.0
 */
function identicons_init() {

	require_once dirname( __FILE__ ) . '/inc/classes/identicon.php';
	require_once dirname( __FILE__ ) . '/inc/classes/pixicon.php';
	require_once dirname( __FILE__ ) . '/inc/functions.php';
	require_once dirname( __FILE__ ) . '/inc/actions.php';
	require_once dirname( __FILE__ ) . '/inc/filters.php';
}
add_action( 'bp_include', 'identicons_init' );

/**
 * Set the avatar_default option.
 *
 * @since 2.0.0
 */
function identicons_set_avatar_default() {
	update_blog_option( get_current_blog_id(), 'avatar_default', 'pixicon' );
}
register_activation_hook( __FILE__, 'identicons_set_avatar_default' );

/**
 * Reset the avatar_default option.
 *
 * @since 1.1.0
 */
function identicons_reset_avatar_default() {
	if ( ! identicons_usage_check() ) {
		return;
	}
	update_blog_option( get_current_blog_id(), 'avatar_default', 'mystery' );
}
register_deactivation_hook( __FILE__, 'identicons_reset_avatar_default' );
