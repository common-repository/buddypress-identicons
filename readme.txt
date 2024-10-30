=== BuddyPress Identicons ===
Contributors: henry.wright
Donate link: https://www.bhf.org.uk/get-involved/donate
Tags: buddypress, identicons, avatars, users
Requires at least: 3.8
Tested up to: 4.5.0
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Fun identicons for your BuddyPress site.

== Description ==

Sick of having a community full of the same mystery face?

If a member doesn't have an uploaded avatar or Gravatar, this plugin will generate an identicon from a hash of their email address. Identicons are fun and highly likely to be unique.

== Installation ==

1. Go to Plugins > Add New and search for `buddypress-identicons`
2. Click on Install Now

You can then go ahead and activate the plugin.

== Frequently Asked Questions ==

= Is the plugin network compatible? =

Yes.

= I don't like my own identicon. Can I change it? =

Yes. Your identicon will change if you change your email address.

= Can I change the image used for non-members? =

Yes. You can filter the image URL:

`function identicons_filter_avatar_url() {
	$upload_dir = wp_upload_dir();
	return trailingslashit( $upload_dir['baseurl'] ) . 'image.png';
}
add_filter( 'identicons_get_avatar_url', 'identicons_filter_avatar_url' );`

= Can I make the image background transparent? =

Yes. To set a transparent background go to Settings > BuddyPress and then look for the option under the Settings tab.

= Can I change the image size? =

Yes. The default is 150px square but you can change that by filtering the "full" width and height settings. For example:

`function identicons_filter_avatar_full_width() {
	return 120;
}
add_filter( 'bp_core_avatar_full_width', 'identicons_filter_avatar_full_width' );`

`function identicons_filter_avatar_full_height() {
	return 120;
}
add_filter( 'bp_core_avatar_full_height', 'identicons_filter_avatar_full_height' );`

= Can I change the identicon type? =

Yes. To change the identicon type go to Settings > Discussion and select an option under Default Avatar.

= Why is it that some members don't have an identicon? =

An identicon is used as a member's avatar only if a profile photo hasn't been uploaded.

= Where should I submit bug reports? =

If you think you've spotted a bug, please open an issue on [GitHub](https://github.com/henrywright/buddypress-identicons/issues).

== Screenshots ==

1. Identicons in use

== Changelog ==

= 2.0.1 =
* Fixed bug relating to the image used for non-members.

= 2.0.0 =
* Added language pack support.
* Improve image handling logic.
* Improve documentation.
* Fix bugs.

= 1.2.0 =
* Fix bugs.
* Use default image width and height set by BuddyPress.
* Let images uploaded to Gravatar take precedence over identicons.
* Generate identicons from email addresses instead of usernames.

= 1.1.4 =
* Text changes.

= 1.1.3 =
* Fix minor bugs.

= 1.1.2 =
* Fix errors associated with file removal on user account deletion.

= 1.1.1 =
* Remove padding option.
* Decrease default image width and height.
* Fix minor bugs.

= 1.1.0 =
* Refactor code.
* Add transparent background option.
* Add padding option.
* Add ability to change width and height.
* Fix bugs.

= 1.0.2 =
* Generate image when `bp_core_fetch_avatar()` is called.

= 1.0.1 =
* Remove the need for a CSS border.

= 1.0.0 =
* Initial release.
