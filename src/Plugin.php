<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 05/17/16
 * Time: 12:23 PM
 */

namespace Alpipego\Resizefly;

use Alpipego\Resizefly\Pimple\Container;
use ReflectionClass;

/**
 * Extends Pimple Container
 * @package Alpipego\Resizefly
 */
class Plugin extends Container {

	/**
	 * Calls `run()` method on all objects registered on plugin container
	 */
	public function run() {
		foreach ( $this->keys() as $key ) { // Loop on contents
			$content = $this->offsetGet( $key );

			if ( is_object( $content ) ) {
				$reflection = new ReflectionClass( $content );
				if ( $reflection->hasMethod( 'run' ) ) {
					$content->run(); // Call run method on object
				}
			}
		}
	}

	/**
	 * wrapper for `load_plugin_textdomain`
	 *
	 * @param $dir string path to languages dir
	 */
	public function loadTextdomain( $dir ) {
		\load_plugin_textdomain( 'resizefly', false, $dir );
	}
}
