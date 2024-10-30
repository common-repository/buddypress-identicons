<?php
/**
 * Action hooks
 *
 * @package BuddyPress Identicons
 * @subpackage Actions
 */

add_action( 'bp_init',                    'identicons_i18n'                           );
add_action( 'bp_register_admin_settings', 'identicons_register_admin_settings', 99    );
add_action( 'profile_update',             'identicons_email_change',            10, 2 );
add_action( 'delete_user',                'identicons_delete'                         );
add_action( 'bp_core_pre_delete_account', 'identicons_delete'                         );
