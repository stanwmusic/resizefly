<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 25/07/16
 * Time: 15:10
 */

namespace Alpipego\Resizefly\Admin;


/**
 * Class AbstractOption
 * @package Alpipego\Resizefly\Admin
 */
abstract class AbstractOption {
	/**
	 * @var array $optionsField id and title of the field
	 */
	public $optionsField = [
		'id'    => null,
		'title' => null,
	];

	/**
	 * @var string $viewsPath path to views dir
	 */
	protected $viewsPath;

	/**
	 * @var string $optionsPage id of page to pass to add_settings_field
	 */
	protected $optionsPage;

	/**
	 * @var string $optionsGroup id of field group to pass to add_settings_field
	 */
	protected $optionsGroup;

	/**
	 * AbstractOption constructor.
	 *
	 * @param string $page The parent page
	 * @param string $section The containing section
	 * @param string $pluginPath Plugin base path
	 */
	public function __construct( $page, $section, $pluginPath ) {
		$this->viewsPath    = $pluginPath . 'views/';
		$this->optionsPage  = $page;
		$this->optionsGroup = $section;
	}

	/**
	 * Add the field to WP Admin
	 */
	public function run() {
		\add_action( 'admin_init', [ $this, 'addField' ] );
		\add_action( 'admin_init', [ $this, 'registerSetting' ] );
	}

	/**
	 * Register the option
	 */
	public function registerSetting() {
		\register_setting( 'resizefly', $this->optionsField['id'], [ $this, 'sanitize' ] );
	}

	/**
	 * Add settings field
	 */
	public function addField() {
		\add_settings_field( $this->optionsField['id'], $this->optionsField['title'], [
			$this,
			'callback'
		], $this->optionsPage, $this->optionsGroup );
	}

	/**
	 * Include the view and pass optional variables
	 *
	 * @param string $name name for view to use
	 * @param array $args variables to pass to view
	 */
	protected function includeView( $name, $args = [] ) {
		$fileArr = preg_split( '/(?=[A-Z-_])/', $name );
		$fileArr = array_map( function ( &$value ) {
			return trim( $value, '-_' );
		}, $fileArr );
		$fileArr = array_map( 'strtolower', $fileArr );

		include $this->viewsPath . 'field/' . implode( '-', $fileArr ) . '.php';
	}
}
