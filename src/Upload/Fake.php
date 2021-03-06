<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05/20/16
 * Time: 6:31 PM
 */

namespace Alpipego\Resizefly\Upload;


/**
 * Make WordPress think image sizes have been created
 * @package Alpipego\Resizefly\Upload
 */
class Fake {

	/**
	 * @var array $sizes array to hold registered image sizes
	 */
	private $sizes;

	/**
	 * register filters
	 */
	public function run() {
		\add_filter( 'intermediate_image_sizes_advanced', [ $this, 'getRegisteredImageSizes' ] );
		\add_filter( 'wp_generate_attachment_metadata', [ $this, 'fakeImageResize' ] );
	}

	/**
	 * get the registered image sizes and return an empty set
	 *
	 * @param array $sizes filter param - registered sizes
	 *
	 * @return array always return empty array
	 */
	public function getRegisteredImageSizes( $sizes ) {
		$this->sizes = $sizes;

		return [ ];
	}

	/**
	 * Create a fake meta entry so WordPress thinks, the image size has been created
	 *
	 * @param array $metadata filter param - image size metadata
	 *
	 * @return array either the original array or a manipulated one
	 */
	public function fakeImageResize( $metadata ) {
		foreach ( $this->sizes as $name => $size ) {
			// figure out what size WP would make this:
			$newsize = \image_resize_dimensions( $metadata['width'], $metadata['height'], $size['width'], $size['height'], $size['crop'] );

			if ( $newsize ) {
				$uploads = \wp_upload_dir( null, false );
				$file    = pathinfo( realpath( $uploads['basedir'] . DIRECTORY_SEPARATOR . $metadata['file'] ) );

				// build the fake meta entry for the size in question
				$metadata['sizes'][ $name ] = [
					'file'   => sprintf( '%s-%sx%s.%s', $file['filename'], $newsize[4], $newsize[5], $file['extension'] ),
					'width'  => $newsize[4],
					'height' => $newsize[5],
				];
			}
		}

		return $metadata;
	}
}
