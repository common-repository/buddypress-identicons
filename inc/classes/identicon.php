<?php
/**
 * Identicon class
 *
 * @package BuddyPress Identicons
 * @subpackage Classes
 */

/**
 * A class definition detailing an identicon.
 *
 * @since 1.0.0
 */
abstract class Identicon {

	/**
	 * The name of the parent directory.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	const DIR = 'identicons';

	/**
	 * Info on the identicon owner.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var object
	 */
	protected $user;

	/**
	 * An image resource identifier.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var resource
	 */
	protected $image;

	/**
	 * The width of the image.
	 *
	 * @since 1.1.1
	 * @access protected
	 * @var int
	 */
	protected $width;

	/**
	 * The height of the image.
	 *
	 * @since 1.1.1
	 * @access protected
	 * @var int
	 */
	protected $height;

	/**
	 * Info on the uploads directory.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var array
	 */
	private $upload_dir;

	/**
	 * The file extension of the image.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var string
	 */
	private $ext = '.png';

	/**
	 * Set up necessary values.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param int $user_id The ID of the identicon owner.
	 */
	protected function __construct( $user_id ) {

		$this->width = bp_core_avatar_full_width();
		$this->height = bp_core_avatar_full_height();

		$this->user = get_userdata( $user_id );

		$this->upload_dir = wp_upload_dir();

		if ( is_ssl() ) {
			// Replace http:// with https://
			$this->upload_dir['baseurl'] = str_replace( 'http://', 'https://', $this->upload_dir['baseurl'] );
		}
		$this->create_dir();
	}

	/**
	 * Create directories.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @return bool True if the directory was created or already exists, else false.
	 */
	private function create_dir() {

		if ( wp_mkdir_p( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . $this->user->ID ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Create an identicon.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	abstract public function create();

	/**
	 * Get an identicon's URL.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string|bool The URL of the identicon or false if the identicon doesn't exist.
	 */
	public function read() {

		if ( $this->identicon_exists() ) {
			return trailingslashit( $this->upload_dir['baseurl'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . $this->ext;
		}
		return false;
	}

	/**
	 * Output the image to file.
	 *
	 * @since 1.1.0
	 * @access protected
	 *
	 * @return bool True if the image was saved, else false.
	 */
	protected function save() {

		if ( @imagepng( $this->image, trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . $this->ext ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Delete an identicon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool True if the image was deleted or doesn't exist, else false.
	 */
	public function delete() {

		$ret = false;

		if ( ! $this->identicon_exists() ) {
			$ret = true;
		}

		if ( @unlink( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . $this->ext ) ) {
			$ret = true;
		}

		// If the directory is empty, remove it.
		@rmdir( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . $this->user->ID );

		return $ret;
	}

	/**
	 * Check if an identicon exists for a given user.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return bool True if an identicon exists or false if it doesn't.
	 */
	public function identicon_exists() {

		if ( file_exists( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . $this->ext ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Destructor.
	 *
	 * Destroy the image resource.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function __destruct() {
		if ( is_resource( $this->image ) ) {
			imagedestroy( $this->image );
		}
	}
}
