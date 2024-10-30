<?php
/**
 * Filter hooks
 *
 * @package BuddyPress Identicons
 * @subpackage Filters
 */

add_filter( 'default_avatar_select',        'identicons_filter_default_avatar_list'         );
add_filter( 'bp_core_fetch_avatar_no_grav', 'identicons_filter_fetch_avatar_no_grav', 10, 2 );
add_filter( 'pre_get_avatar',               'identicons_pre_get_avatar',              10, 3 );
add_filter( 'bp_core_default_avatar_user',  'identicons_default_avatar_url',          10, 2 );
