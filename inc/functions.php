<?php
/**
 * Function definitions
 *
 * @package BuddyPress Identicons
 * @subpackage Functions
 */

/**
 * Load the plugin textdomain.
 *
 * @since 1.1.0
 */
function identicons_i18n() {
	load_plugin_textdomain( 'buddypress-identicons' );
}

/**
 * Register the admin settings.
 *
 * @since 1.1.0
 */
function identicons_register_admin_settings() {

	add_settings_section(
		'identicons',
		__( 'Identicons', 'buddypress-identicons' ),
		'identicons_settings_section_callback',
		'buddypress'
	);
	add_settings_field(
		'identicons-background',
		__( 'Background', 'buddypress-identicons' ),
		'identicons_settings_field_callback_background',
		'buddypress',
		'identicons'
	);
	register_setting(
		'buddypress',
		'identicons-background',
		'intval'
	);
}

/**
 * Fill the section with content.
 *
 * @since 1.1.0
 */
function identicons_settings_section_callback() {}

/**
 * Add an input to the field.
 *
 * @since 1.1.0
 */
function identicons_settings_field_callback_background() {

	$value = get_blog_option( get_current_blog_id(), 'identicons-background' );
	?>
	<input type="checkbox" name="identicons-background" id="identicons-background" value="1" <?php checked( $value ); ?> />
	<label for="identicons-background"><?php _e( 'Set a transparent background', 'buddypress-identicons' ); ?></label>
	<?php
}

/**
 * Add to the default avatar list.
 *
 * @since 1.2.0
 *
 * @param string $list HTML markup of the default avatar list.
 * @return string
 */
function identicons_filter_default_avatar_list( $list ) {

	$option = get_blog_option( get_current_blog_id(), 'avatar_default' );

	$path = plugins_url( 'images/pixicon.png', __DIR__ );

	$defaults['pixicon'] = __( 'Pixicon', 'buddypress-identicons' );

	foreach ( $defaults as $k => $v ) {
		$list .= '<label>';
		$list .= '<input type="radio" name="avatar_default" id="avatar_' . $k . '" value="' . esc_attr( $k ) . '" ' . checked( $option, $k, false ) . '/>';
		$list .= ' ';
		$list .= '<img src="' . esc_url( $path ) . '" class="avatar avatar-32" alt="' . esc_attr( $v ) . '" height="32" width="32" />';
		$list .= ' ';
		$list .= $v;
		$list .= '</label>';
		$list .= '<br />';
	}
	return $list;
}

/**
 * Check if a Gravatar image exists for a given email hash.
 *
 * @since 1.2.0
 *
 * @param string $email The user's email address.
 * @return bool True if a Gravatar image exists, else false.
 */
function identicons_grav_exists( $email ) {

	$hash = md5( strtolower( trim( $email ) ) );

	$response = wp_remote_head( 'http://www.gravatar.com/avatar/' . $hash . '?d=404' );

	if ( $response['response']['code'] === 200 ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Determine whether to skip Gravatar.
 *
 * @since 1.2.0
 *
 * @param bool $no_grav True to disable Gravatar, false to not.
 * @param array $params The parameters for the request.
 * @return bool
 */
function identicons_filter_fetch_avatar_no_grav( $no_grav, $params ) {

	// Bail if identicons aren't in use.
	if ( ! identicons_usage_check() ) {
		return $no_grav;
	}

	// Bail if not a user.
	if ( $params['object'] !== 'user' ) {
		return $no_grav;
	}

	if ( empty( $params['email'] ) ) {
		$params['email'] = bp_core_get_user_email( $params['item_id'] );
	}

	if ( identicons_grav_exists( $params['email'] ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Determine if get_avatar should be skipped.
 *
 * @since 1.2.0
 *
 * @param string $avatar The avatar HTML.
 * @param mixed $id_or_email The ID or email.
 * @param array $args The avatar's arguments.
 * @return null|string
 */
function identicons_pre_get_avatar( $avatar, $id_or_email, $args ) {

	global $pagenow;

	if ( $pagenow === 'options-discussion.php' || ! identicons_usage_check() ) {
		return $avatar;
	} else {
		$url = plugins_url( 'images/pixicon.png', dirname( __FILE__ ) );
		/**
		 * Filters the avatar URL.
		 *
		 * @since 2.0.1
		 *
		 * @param string The avatar URL.
		 */
		$url = apply_filters( 'identicons_get_avatar_url', $url );

		return '<img alt="' . $args['alt'] . '" src="' . $url . '" class="' . $args['class'] . '" height="' . $args['height'] . '" width="' . $args['width'] . '">';
	}
}

/**
 * Filter the default avatar URL.
 *
 * @since 1.1.0
 *
 * @param string $avatar_default The URL of the default avatar.
 * @param array $params See {@see bp_core_fetch_avatar()}.
 * @return string
 */
function identicons_default_avatar_url( $avatar_default, $params ) {

	// Bail if identicons aren't in use.
	if ( ! identicons_usage_check() ) {
		return $avatar_default;
	}

	$identicon = identicons_factory( $params['item_id'] );

	$identicon->create();

	return $identicon->read();
}

/**
 * Delete a user's identicon if they change their email.
 *
 * @since 1.2.0
 *
 * @param int $user_id The ID of the person updating their profile.
 * @param object $old_data The user's data prior to updating.
 */
function identicons_email_change( $user_id, $old_data ) {

	$user = new WP_User( $user_id );

	if ( $user->user_email === $old_data->user_email ) {
		// No change.
		return;
	}
	delete( $user_id );
}

/**
 * Delete an identicon.
 *
 * @since 1.1.0
 *
 * @param int $user_id The ID of the identicon owner.
 */
function identicons_delete( $user_id ) {

	$identicon = identicons_factory( $user_id );

	$identicon->delete();
}

/**
 * Check if identicons are in use.
 *
 * @since 1.1.0
 *
 * @return bool True if identicons are being used, else false.
 */
function identicons_usage_check() {

	$avatar_default = get_blog_option( get_current_blog_id(), 'avatar_default' );

	switch( $avatar_default ) {

		case plugins_url( 'images/pixicon.png', __DIR__ ):
		case 'pixicon':
			return true;

		default:
			return false;
	}
}

/**
 * Instantiate a new object based on the value of avatar_default.
 *
 * @since 1.1.4
 *
 * @param int $user_id The ID of the identicon owner.
 * @return object|bool
 */
function identicons_factory( $user_id ) {

	switch( get_blog_option( get_current_blog_id(), 'avatar_default' ) ) {

		case plugins_url( 'images/pixicon.png', __DIR__ ):
		case 'pixicon':
			return new Pixicon( $user_id );
	}
	return false;
}
