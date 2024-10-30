<?php
/**
 * Pixicon class
 *
 * @package BuddyPress Identicons
 * @subpackage Classes
 */

/**
 * A class definition detailing a pixicon.
 *
 * @since 1.1.0
 * @access public
 */
class Pixicon extends Identicon {

	/**
	 * The type of identicon.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var string
	 */
	protected $type = 'pixicon';

	/**
	 * Set up.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param int $user_id The ID of the identicon owner.
	 */
	public function __construct( $user_id ) {
		parent::__construct( $user_id );
	}

	/**
	 * Create a pixicon.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function create() {

		// Bail if an identicon exists.
		if ( $this->identicon_exists() ) {
			return;
		}

		// Get a hash of the user's email address.
		$hash = md5( $this->user->user_email );

		for ( $x = 0; $x < 5; $x++ ) {
			for ( $y = 0; $y < 5; $y++ ) {
				$hex = substr( $hash, ( $x * 5 ) + $y + 6, 1 );
				$dec = hexdec( $hex );
				$data[$x][$y] = $dec % 2 === 0;
			}
		}
		$unit_w = $this->width / 6;
		$unit_h = $this->height / 6;

		$padding_h = $unit_w / 2;
		$padding_v = $unit_h / 2;

		// Create a new image.
		$this->image = imagecreatetruecolor( $this->width, $this->height );

		// Create a temporary image.
		$temp = imagecreatetruecolor( $this->width - ( $padding_h * 2 ), $this->height - ( $padding_v * 2 ) );

		// Get red, green and blue values.
		$r = substr( $hash, 0, 2 );
		$g = substr( $hash, 2, 2 );
		$b = substr( $hash, 4, 2 );

		// Set the foreground.
		$foreground = imagecolorallocate( $this->image, '0x' . $r, '0x' . $g, '0x' . $b );

		// Set the background.
		$background = imagecolorallocate( $this->image, '0xee', '0xee', '0xee' );

		if ( get_blog_option( get_current_blog_id(), 'identicons-background' ) == 1 ) {

			// Make the background transparent.
			imagecolortransparent( $this->image, $background );
			imagecolortransparent( $temp, $background );
		}

		// Paint the image background.
		imagefill( $this->image, 0, 0, $background );

		for ( $x = 0; $x < 5; $x++ ) {
			for ( $y = 0; $y < 5; $y++ ) {

				switch ( $x ) {
					case 3:
						$shift = 2;
						break;
					case 4:
						$shift = 4;
						break;
					default:
						$shift = 0;
				}
				$color = $background;

				if ( $data[$x - $shift][$y] ) {
					$color = $foreground;
				}
				$x1 = $x * $unit_w;
				$y1 = $y * $unit_h;
				$x2 = ( $x + 1 ) * $unit_w;
				$y2 = ( $y + 1 ) * $unit_h;

				imagefilledrectangle( $temp, $x1, $y1, $x2, $y2, $color );
			}
		}
		$dst_im = $this->image;
		$src_im = $temp;
		$dst_x = $padding_h;
		$dst_y = $padding_v;
		$src_x = 0;
		$src_y = 0;
		$src_w = $this->width - ( $padding_h * 2 );
		$src_h = $this->height - ( $padding_v * 2 );

		imagecopy( $dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h );

		// Save the image.
		$this->save();
	}
}
